<?php

namespace Core;

use Core\Models\Content;
use Symfony\Component\HttpFoundation\Response;

/**
 * This is the response, we use to generate the basic html skeleton.
 * Ths actual body content comes from Core\PageController.
 */
class PageResponse extends Response
{
    /**
     * @var string
     */
    public $docType = 'html5';

    /**
     * @var array
     */
    public static $docTypeDeclarations = array(

        'html 4.01 transitional' =>
        '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
        'html 4.01 strict' =>
        '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
        'html 4.01 frameset' =>
        '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
        'xhtml 1.0 transitional' =>
        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
        'xhtml 1.0 strict' =>
        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
        'xhtml 1.0 frameset' =>
        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
        'xhtml 1.1 dtd' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
        'html5' => '<!DOCTYPE html>'
    );

    /**
     * The html starting tag.
     *
     * @var string
     */
    private $htmlTag = '<html>';

    /**
     * All plugin responses. Mostly only one.
     *
     * @var array
     */
    private $pluginResponse = array();

    /**
     * CSS files.
     *
     * @var array
     */
    private $css = array(
        array('path' => '@CoreBundle/css/normalize.css', 'type' => 'text/css'),
        array('path' => '@CoreBundle/css/defaults.css', 'type' => 'text/css')
    );

    /**
     * Javascript files and scripts.
     *
     * @var array
     */
    private $js = array();

    /**
     * @var string
     */
    private $title;

    /**
     * All additional html>head elements.
     *
     * @var array
     */
    private $header = array();

    /**
     * @var bool
     */
    private $domainHandling = true;

    /**
     * @var string
     */
    private $favicon = '@CoreBundle/images/favicon.ico';

    /**
     * @var bool
     */
    private $resourceCompression = true;

    /**
     * Constructor
     */
    public function __construct($content = '', $status = 200, $headers = array())
    {
        $this->setDocType($this->getDocType());
        parent::__construct($content, $status, $headers);
    }

    /**
     * Sets the end-tag of header elements. default is '>', but on XHTML it needs to be '/>'.
     *
     * @param string $endTag
     */
    public function setEndTag($endTag)
    {
        $this->endTag = $endTag;
    }

    /**
     * Returns the end-tag of header elements. default is '>', but on XHTML it needs to be '/>'.
     *
     * @return string
     */
    public function getEndTag()
    {
        return $this->endTag;
    }

    /**
     * @param string $favicon
     */
    public function setFavicon($favicon)
    {
        $this->favicon = $favicon;
    }

    /**
     * @return string
     */
    public function getFavicon()
    {
        return $this->favicon;
    }

    /**
     * @param boolean $domainHandling
     */
    public function setDomainHandling($domainHandling)
    {
        $this->domainHandling = $domainHandling;
    }

    /**
     * @return bool
     */
    public function getDomainHandling()
    {
        return $this->domainHandling;
    }

    /**
     * @param boolea $resourceCompression
     */
    public function setResourceCompression($resourceCompression)
    {
        $this->resourceCompression = $resourceCompression;
    }

    /**
     * @return bool
     */
    public function getResourceCompression()
    {
        return $this->resourceCompression;
    }

    /**
     * Adds a css file to the page.
     *
     * @param string $path
     * @param string $type
     */
    public function addCssFile($path, $type = 'text/css')
    {
        $insert = array('path' => $path, 'type' => $type);
        if (array_search($insert, $this->css) === false) {
            $this->css[] = $insert;
        }
    }

    /**
     * Adds css source to the page.
     *
     * @param string $content
     * @param string $type
     */
    public function addCss($content, $type = 'text/css')
    {
        $insert = array('content' => $content, 'type' => $type);
        if (array_search($insert, $this->css) === false) {
            $this->css[] = $insert;
        }
    }

    /**
     * Adds a javascript file to the page.
     *
     * @param string $path
     * @param string $position
     * @param string $type
     */
    public function addJsFile($path, $position = 'top', $type = 'text/javascript')
    {
        $insert = array('path' => $path, 'position' => $position, 'type' => $type);
        if (array_search($insert, $this->js) === false) {
            $this->js[] = $insert;
        }
    }

    /**
     * Adds javascript source to the page.
     *
     * @param string $content
     * @param string $position
     * @param string $type
     */
    public function addJs($content, $position = 'top', $type = 'text/javascript')
    {
        $insert = array('content' => $content, 'position' => $position, 'type' => $type);
        if (array_search($insert, $this->js) === false) {
            $this->js[] = $insert;
        }
    }

    /**
     * Adds a additionally HTML header element.
     *
     * @param string $content
     */
    public function addHeader($content)
    {
        $this->header[] = $content;
    }

    /**
     * Builds the HTML skeleton, sends all HTTP headers and the HTTP body.
     *
     * This handles the SearchEngine stuff as well.
     *
     * @return Response
     */
    public function send()
    {
        $this->prepare(Kryn::getRequest());

        $this->setCharset('utf-8');

        $html = $this->buildHtml();
        $this->setContent($html);

        Kryn::getEventDispatcher()->dispatch('core.page-response-send-pre');

        //search engine, todo
        if (false && Kryn::$disableSearchEngine == false) {
            SearchEngine::createPageIndex($html);
        }

        return parent::send();
    }

    public function buildHtml()
    {
        $body = $this->buildBody();

        $header = $this->getTitleTag();
        $header .= $this->getBaseHrefTag();
        $header .= $this->getContentTypeTag();
        $header .= $this->getMetaLanguageTag();
        $header .= $this->getFaviconTag();

        $header .= $this->getAdditionalHeaderTags();

        $header .= $this->getCssTags();
        //$header .= $this->getCssContent();

        $header .= $this->getScriptTags('top');

        $beforeBodyClose = '';

        $beforeBodyClose .= $this->getScriptTags('bottom');

        $docType = $this->getDocTypeDeclaration();
        $htmlOpener = $this->getHtmlTag();

        $html = sprintf(
"%s
%s
<head>
%s
</head>
<body>
%s
%s
</body>
</html>",
            $docType,
            $htmlOpener,
            $header,
            $body,
            $beforeBodyClose
        );

        $html = preg_replace('/href="#([^"]*)"/', 'href="' . Kryn::getBaseUrl() . '#$1"', $html);
        $html = Kryn::parseObjectUrls($html);
        $html = Kryn::translate($html);
        Kryn::removeSearchBlocks($html);

        return $html;
    }

    /**
     * Builds the html header tag for the favicon.
     *
     * @return string
     */
    public function getFaviconTag()
    {
        if ($this->getFavicon()) {
            return sprintf(
                '<link rel="shortcut icon" type="image/x-icon" href="%s">' . chr(10),
                Kryn::resolvePublicPath($this->getFavicon())
            );
        }
    }

    /**
     * Builds the html body of the current page.
     *
     * @return string
     */
    public function buildBody()
    {
        $page = Kryn::getPage();
        if (!$page) {
            return '';
        }

        Kryn::$themeProperties = array();
        $propertyPath = '';

        $layout = Kryn::$page->getLayout();
        if (false !== ($pos = strpos($layout, '/'))) {
            $layout = substr($layout, 0, $pos);
        }
        $layout = explode('.', $layout);
        $bundleName = Kryn::getBundleName($layout[0]);

        if ($config = Kryn::getConfig($bundleName)) {
            $theme = $config->getTheme($layout[1]);
            if ($theme) {
                $propertyPath = '@' . $bundleName . '/' . $theme->getId();
            }
        }

        if ($propertyPath) {
            if ($themeProperties = kryn::$domain->getThemeProperties()) {
                Kryn::$themeProperties = $themeProperties->getByPath($propertyPath);
            }
        }

        $layout = $page->getLayout();

        return Kryn::getInstance()->renderView(
            $layout,
            array(
                 'themeProperties' => Kryn::$themeProperties
            )
        );
    }

    /**
     * Returns `<meta http-equiv="content-type" content="text/html; charset=%s">` based on $this->getCharset().
     *
     * @return string
     */
    public function getContentTypeTag()
    {
        return sprintf(
            '<meta http-equiv="content-type" content="text/html; charset=%s">' . chr(10),
            $this->getCharset()
        );
    }

    /**
     * Returns all additional html header elements.
     */
    public function getAdditionalHeaderTags()
    {
        return implode("\n    ", $this->header) . "\n";
    }

    /**
     * Returns the key of current doc type.
     * If you need the whole doctype message use `getDocTypeDeclaration`
     *
     * @return string
     */
    public function getDocType()
    {
        return $this->docType;
    }

    /**
     * Sets the current docType.
     *
     * If you want to use own docTypes, extend `PageResponse::$docTypeDeclarations`.
     * Example
     *
     *    PageResponse::$docTypeDeclarations['doctypXy'] = '<!DOCTYPE HTML PUBLIC "-/ ...';
     *    Kryn::getResponse()->setDocType('doctypXy');
     *
     * @param $docType The key of PageResponse::$docTypeDeclarations
     */
    public function setDocType($docType)
    {
        $this->docType = $docType;
        $this->setEndTag(((strpos(strtolower($this->docType), 'xhtml') !== false) ? '/>' : '>') . "\n");
    }

    /**
     * Returns the full doc type declaration.
     *
     * @return mixed
     */
    public function getDocTypeDeclaration()
    {
        return self::$docTypeDeclarations[$this->docType];
    }

    /**
     * Returns the current html starting tag.
     *
     * @return string
     */
    public function getHtmlTag()
    {
        return $this->htmlTag;
    }

    /**
     * Sets the current html starting tag.
     *
     * @param string $htmlTag
     */
    public function setHtmlTag($htmlTag)
    {
        $this->htmlTag = $htmlTag;
    }

    /**
     * Returns the `<base href="%s"` based on Core\Kryn::getBaseUrl().
     *
     * @return string
     */
    public function getBaseHrefTag()
    {
        return sprintf('<base href="%s" %s', Kryn::getBaseUrl(), $this->getEndTag());
    }

    /**
     * Returns `<meta name="DC.language" content="%s">` filled with the language of the current domain.
     *
     * @return string
     */
    public function getMetaLanguageTag()
    {
        if ($this->getDomainHandling()) {
            return sprintf(
                '<meta name="DC.language" content="%s" %s',
                Kryn::$domain->getLang(),
                $this->getEndTag()
            );
        }
    }

    /**
     * Returns the title as html tag.
     *
     * @return string
     */
    public function getTitleTag()
    {
        if ($this->getDomainHandling()) {
            $title = Kryn::$domain->getTitleFormat();

            if (Kryn::$page) {
                $title = str_replace(
                    array(
                         '%title'
                    ),
                    array(
                         Kryn::$page->getAlternativeTitle() ? : Kryn::$page->getTitle()
                    )
                    ,
                    $title
                );
            }
        } else {
            $title = $this->getTitle();
        }

        return sprintf("<title>%s</title>\n", $title);
    }

    /**
     * Sets the html title.
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Gets the html title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Compares two PageResponses and returns the difference as array/
     *
     * @param  PageResponse $response
     *
     * @return array
     */
    public function diff(PageResponse $response)
    {
        $diff = array();

        $blacklist = array('pluginResponse');

        foreach ($this as $key => $value) {
            if (in_array($key, $blacklist)) {
                continue;
            }
            $getter = 'get' . ucfirst($key);

            if (!is_callable(array($this, $getter))) {
                continue;
            }

            $particular = null;
            $other = $response->$getter();

            if (is_array($value)) {
                $particular = $this->arrayDiff($value, $other);
            } elseif ($value != $other) {
                $particular = $other;
            }

            if ($particular) {
                $diff[$key] = $particular;
            }
        }

        return $diff;
    }

    /**
     * @param  array $p1
     * @param  arry  $p2
     *
     * @return array
     */
    public function arrayDiff($p1, $p2)
    {
        $diff = array();
        foreach ($p2 as $v) {
            if (array_search($v, $p1) === false) {
                $diff[] = $v;
            }
        }

        return $diff;
    }

    /**
     * Patches a diff from $this->diff().
     *
     * @param array $diff
     */
    public function patch(array $diff)
    {
        foreach ($diff as $key => $value) {
            if (is_array($value) && is_array($this->$key)) {
                $this->$key = array_merge($this->$key, $value);
            } else {
                $this->$key = $value;
            }
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\ResponseHeaderBag $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\ResponseHeaderBag
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $css
     */
    public function setCss($css)
    {
        $this->css = $css;
    }

    /**
     * @return array
     */
    public function getCss()
    {
        return $this->css;
    }

    /**
     * @param array $js
     */
    public function setJs($js)
    {
        $this->js = $js;
    }

    /**
     * @return array
     */
    public function getJs()
    {
        return $this->js;
    }

    /**
     *
     *
     * @param  PluginResponse $response
     *
     * @return PageResponse
     */
    public function setPluginResponse(PluginResponse $response)
    {
        $param = $response->getControllerRequest()->attributes->get('_route_params');
        $this->pluginResponse[$param['_content']->getId()] = $response;

        return $this;
    }

    /**
     *
     * @param Content $content
     *
     * @return string
     */
    public function getPluginResponse($content)
    {
        $id = $content;
        if ($content instanceof Content) {
            $id = $content->getId();
        }

        return isset($this->pluginResponse[$id]) ? $this->pluginResponse[$id] : '';
    }

    /**
     * Returns all <link> tags based on the attached css files.
     *
     * @return string
     * @throws \FileNotFoundException
     */
    public function getCssTags()
    {
        $result = '';

        if ($this->getResourceCompression()) {

            $cssCode = '';
            foreach ($this->css as $css) {
                if ($css['path']) {
                    if (false !== strpos($css['path'], "://")) {
                        $result .= sprintf(
                            '<link rel="stylesheet" type="text/css" href="%s" %s',
                            $css['path'],
                            $this->getEndTag()
                        );
                    } else {
                        //local
                        $file = Kryn::resolvePath($css['path'], 'Resources/public');

                        if (file_exists($file) && $modifiedTime = filemtime($file)) {
                            $cssCode .= $file . '_' . $modifiedTime;
                        } else {
                            Kryn::getLogger()->addError(tf('CSS file `%s` [%s] not found.', $file, $css['path']));
                        }
                    }
                } else {
                    $cssCode .= '_' . $css['content'] . '_';
                }
            }

            $cssMd5 = md5($cssCode);
            $cssCachedFile = 'cachedCss_' . $cssMd5 . '.css';

            if (!file_exists(PATH_WEB_CACHE . $cssCachedFile)) {
                $compressFiles = array();
                foreach ($this->css as $css) {
                    if ($css['path']) {
                        if (false !== strpos($css['path'], "://")) {
                            $result .= sprintf(
                                '<link rel="stylesheet" type="text/css" href="%s" %s',
                                $css['path'],
                                $this->getEndTag()
                            );
                        } else {
                            $compressFiles[] = $css['path'];
                        }
                    } else {
                        $result .= sprintf(
                            '<style type="text/css">' . chr(10) . '%s' . chr(10) . '</style>' . chr(10),
                            $css['content']
                        );
                    }
                }
                $cssContent = Utils::compressCss($compressFiles, 'cache/');
                file_put_contents(PATH_WEB_CACHE . $cssCachedFile, $cssContent);
            }
            $result .= sprintf(
                '<link rel="stylesheet" type="text/css" href="cache/%s" %s',
                $cssCachedFile,
                $this->getEndTag()
            );
        } else {
            foreach ($this->css as $css) {
                if ($css['path']) {
                    if (false !== strpos($css['path'], "://")) {
                        $result .= sprintf(
                            '<link rel="stylesheet" type="text/css" href="%s" %s',
                            $css['path'],
                            $this->getEndTag()
                        );
                    } else {
                        $file = Kryn::resolvePath($css['path'], 'Resources/public');
                        $public = Kryn::resolvePublicPath($css['path']);

                        if (file_exists($file)) {
                            $modifiedTime = filemtime($file);
                        }

                        $result .= sprintf(
                            '<link rel="stylesheet" type="%s" href="%s" %s',
                            $css['type'],
                            $public . ($modifiedTime ? '?c=' . $modifiedTime : ''),
                            $this->getEndTag()
                        );
                    }
                } else {
                    $result .= sprintf(
                        '<style type="text/css">' . chr(10) . '%s' . chr(10) . '</style>' . chr(10),
                        $css['content']
                    );
                }
            }
        }

        return $result;

    }

    /**
     * Generates the <script> tags based on all attached js files/scripts.
     *
     * @param string $position
     *
     * @return string
     * @throws \FileNotFoundException
     */
    public function getScriptTags($position = 'top')
    {
        $result = '';

        if ($this->getResourceCompression()) {
            $jsCode = '';
            foreach ($this->js as $js) {
                if ($js['position'] != $position) {
                    continue;
                }

                if ($js['path']) {
                    if (false !== strpos($js['path'], "http://")) {
                        $result .= '<script type="text/javascript" src="' . $js['path'] . '" ></script>' . "\n";
                    } else {
                        //local
                        $file = Kryn::resolvePath($js['path'], 'Resources/public');
                        if (file_exists($file) && $modifiedTime = filemtime($file)) {
                            $jsCode .= $file . '_' . $modifiedTime;
                        } else {
                            Kryn::getLogger()->addError(tf('JavaScript file `%s` [%s] not found.', $file, $js['path']));
                        }
                    }
                } else {
                    $jsCode .= '_' . $js['content'] . '_';
                }
            }

            $jsMd5 = md5($jsCode);
            $jsCachedFile = 'cachedJs_' . $jsMd5 . '.js';
            $jsContent = '';

            if (!file_exists(PATH_WEB_CACHE . $jsCachedFile)) {
                foreach ($this->js as $js) {
                    if ($js['position'] != $position) {
                        continue;
                    }

                    if ($js['path']) {
                        if (false !== strpos($js['path'], "://")) {
                            $result .= sprintf(
                                '<script type="%s" src="%s"></script>' . chr(10),
                                $js['type'],
                                $js['path']
                            );
                        } else {
                            $file = Kryn::resolvePath($js['path'], 'Resources/public');
                            $public = Kryn::resolvePublicPath($js['path']);

                            if (file_exists($file)) {
                                $jsContent .= "/* ($public, {$js['path']}) - $file */\n\n";
                                $jsContent .= file_get_contents($file) . "\n\n\n";
                            }
                        }
                    } else {
                        $jsContent .= "/* javascript block */\n\n";
                        $jsContent .= $js['content'] . "\n\n\n";
                    }
                }
                file_put_contents(PATH_WEB_CACHE . $jsCachedFile, $jsContent);
            }

            $result .= '<script type="text/javascript" src="cache/' . $jsCachedFile . '" ></script>' . "\n";
        } else {
            foreach ($this->js as $js) {
                if ($js['position'] != $position) {
                    continue;
                }

                if ($js['path']) {
                    if (false !== strpos($js['path'], "://")) {
                        $result .= sprintf('<script type="%s" src="%s"></script>' . chr(10), $js['type'], $js['path']);
                    } else {
                        $file = Kryn::resolvePath($js['path'], 'Resources/public');
                        $public = Kryn::resolvePublicPath($js['path']);

                        $modifiedTime = file_exists($file) ? filemtime($file) : null;

                        $result .= sprintf(
                            '<script type="%s" src="%s"></script>' . chr(10),
                            $js['type'],
                            $public . ($modifiedTime ? '?c=' . $modifiedTime : '')
                        );
                    }
                } else {
                    $result .= sprintf(
                        '<script type="%s">' . chr(10) . '%s' . chr(10) . '</script>' . chr(10),
                        $js['type'],
                        $js['content']
                    );
                }
            }
        }

        return $result;

    }

}
