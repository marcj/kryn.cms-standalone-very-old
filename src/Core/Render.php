<?php

/*
 * This file is part of Kryn.cms.
 *
 * (c) Kryn.labs, MArc Schmidt <marc@Kryn.org>
 *
 * To get the full copyright and license information, please view the
 * LICENSE file, that was distributed with this source code.
 *
 */

namespace Core;

use Core\Models\Content;
use Core\Render\TypeNotFoundException;
use Propel\Runtime\Map\TableMap;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Html render class
 *
 * @author MArc Schmidt <marc@Kryn.org>
 *
 * @events onRenderSlot
 *
 */

class Render
{
    /**
     * Cache of the current contents stage.
     *
     * @var array
     */
    public static $contents;

    private static $instances;

    private $nodeId;

    private $cachedSlotContents = array();

    /**
     * @param integer $nodeId
     * @return Render
     */
    public static function getInstance($nodeId = null)
    {
        if (!$nodeId) {
            $nodeId = Kryn::$page->getId();
        }

        if (!isset(static::$instances[$nodeId])) {
            static::$instances[$nodeId] = new Render($nodeId);
        }

        return static::$instances[$nodeId];
    }


    /**
     * @param integer $nodeId
     */
    function __construct($nodeId)
    {
        $this->nodeId = $nodeId;
    }

    /**
     * @param integer $slotId
     * @param array   $params
     *
     * @return string
     */
    public function getRenderedSlot($slotId = 1, $params = array())
    {
        $contents =& $this->getSlotContents($slotId);
        return static::renderContents($contents, $params);
    }
    
    public function getNodeId()
    {
        return $this->nodeId;
    }

    /**
     * @param integer $slotId
     * @return \Core\Models\Content[]
     */
    public function &getSlotContents($slotId)
    {
        if (!isset($this->cachedSlotContents[$slotId])){
            $this->cachedSlotContents[$slotId] = PageController::getSlotContents($this->getNodeId(), $slotId);
        }

        return $this->cachedSlotContents[$slotId];
    }

    /**
     * Build HTML for given contents.
     *
     * @param array $contents
     * @param array $slotProperties
     *
     * @return string
     * @internal
     */
    public static function renderContents(&$contents, $slotProperties)
    {
        $filteredContents = array();
        if (!($contents instanceof \Traversable)) {
            return;
        }

        foreach ($contents as $content) {
            $access = true;

            if (
                ($content->getAccessFrom() + 0 > 0 && $content->getAccessFrom() > time()) ||
                ($content->getAccessTo() + 0 > 0 && $content->getAccessTo() < time())
            ) {
                $access = false;
            }

            if ($content->getHide()) {
                $access = false;
            }

            if ($access && $content->getAccessFromGroups()) {

                $access = false;
                $groups = ',' . $content->getAccessFromGroups() . ',';

                $userGroups = Kryn::getClient()->getUser()->getUserGroups();

                foreach ($userGroups as $group) {
                    if (strpos($groups, ',' . $group->getGroupId() . ',') !== false) {
                        $access = true;
                        break;
                    }
                }

                if (!$access) {
                    $adminGroups = Kryn::getClient()->getUser()->getUserGroups();
                    foreach ($adminGroups as $group) {
                        if (strpos($groups, ',' . $group->getGroupId() . ',') !== false) {
                            $access = true;
                            break;
                        }
                    }
                }
            }

            if ($access) {
                $filteredContents[] = $content;
            }
        }

        $count = count($filteredContents);
        /*
         * Compatibility
         */
        $data['layoutContentsMax'] = $count;
        $data['layoutContentsIsFirst'] = true;
        $data['layoutContentsIsLast'] = false;
        $data['layoutContentsId'] = $slotProperties['id'];
        $data['layoutContentsName'] = $slotProperties['name'];

        $i = 0;

        //$oldContent = $tpl->getTemplateVars('content');
        Kryn::getEventDispatcher()->dispatch('core/render/slot/pre', new GenericEvent($data));

        $html = '';

        if ($count > 0) {
            foreach ($filteredContents as &$content) {
                if ($i == $count) {
                    $data['layoutContentsIsLast'] = true;
                }

                if ($i > 0) {
                    $data['layoutContentsIsFirst'] = false;
                }

                $i++;
                $data['layoutContentsIndex'] = $i;

                $html .= self::renderContent($content, $data);

            }
        }

        $argument = array($data, &$html);
        Kryn::getEventDispatcher()->dispatch('core/render/slot', new GenericEvent($argument));
        Event::fire('onRenderSlot', $argument);

        if ($slotProperties['assign'] != "") {
            Kryn::getInstance()->assign($slotProperties['assign'], $html);
            return '';
        }

        return $html;
    }

    /**
     * Build HTML for given content.
     *
     * @param Content $content
     * @param array   $parameters
     *
     * @return string
     * @throws Render\TypeNotFoundException
     */
    public static function renderContent(Content $content, $parameters = array())
    {
        $type = $content->getType();
        $class = 'Core\\Render\\Type' . ucfirst($type);

        if (class_exists($class)) {
            /** @var \Core\Render\TypeInterface $typeRenderer */
            $typeRenderer = new $class($content, $parameters);
            $html = $typeRenderer->render();
        } else {
            throw new TypeNotFoundException(sprintf(
                'Type renderer for `%s` not found. [%s]',
                $content->getType(),
                json_encode($content)
            ));
        }

        $data['content'] = $content->toArray(TableMap::TYPE_STUDLYPHPNAME);
        $data['parameter'] = $parameters;
        $data['html'] = $html;

        Kryn::getEventDispatcher()->dispatch('core/render/content/pre', new GenericEvent($data));

        $unsearchable = false;
        if ((!is_array($content->getAccessFromGroups()) && $content->getAccessFromGroups() != '') ||
            (is_array($content->getAccessFromGroups()) && count($content->getAccessFromGroups()) > 0) ||
            ($content->getAccessFrom() > 0 && $content->getAccessFrom() > time()) ||
            ($content->getAccessTo() > 0 && $content->getAccessTo() < time()) ||
            $content->getUnsearchable()
        ) {
            $unsearchable = true;
        }

        Event::fire('onRenderContent', $argument);

        if ($content->getTemplate() == '' || $content->getTemplate() == '-') {
            if ($unsearchable) {
                $result = '<!--unsearchable-begin-->' . $data['html'] . '<!--unsearchable-end-->';
            }
        } else {
            $result = Kryn::getInstance()->renderView($content->getTemplate(), $data);

            if ($unsearchable) {
                $result = '<!--unsearchable-begin-->' . $result . '<!--unsearchable-end-->';
            }
        }

        $argument = array(&$result, $data);
        Kryn::getEventDispatcher()->dispatch('core/render/content', new GenericEvent($argument));

        return $result;
    }

    public static function updatePage2DomainCache()
    {
        $r2d = array();
        $res = dbQuery('SELECT id, domain_id FROM ' . pfx . 'system_node ');

        while ($row = dbFetch($res)) {
            $r2d[$row['domain_id']] .= $row['id'] . ',';
        }
        dbFree(res);

        Kryn::setDistributedCache('core/node/toDomains', $r2d);

        return $r2d;
    }

}
