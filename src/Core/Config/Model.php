<?php

namespace Core\Config;

class Model
{
    /**
     * @var \DOMElement
     */
    protected $element;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param \DOMElement $element
     * @param Config      $config
     */
    public function __construct(\DOMElement $element = null, Config $config = null)
    {
        if ($config) {
            $this->config = $config;
        }

        if ($element) {
            $this->element = $element;
            $this->setupObject();
        }
    }

    /**
     * Initialize the object.
     */
    public function setupObject()
    {

    }

    /**
     * @param \DOMNode $node
     *
     * @return mixed
     * @throws \Exception
     */
    public function getModelInstance(\DOMNode $node)
    {
        if (!$this->config) {
            throw new \Exception(sprintf('Instance of `%s` does not have a `config`.', get_class($this)));
        }

        return $this->config->getModelInstance($node);
    }

    /**
     * @return mixed
     */
    public function getParentInstance()
    {
        return $this->getModelInstance($this->element->parentNode);
    }

    /**
     * <parameters>
     *    <parameter>first</parameter>
     *    <parameter>second</parameter>
     *    <parameter id="foo">bar</parameter>
     *    <parameter id="ho">sa</parameter>
     * </parameters>
     *
     * => array(
     *    0 => 'first',
     *    1 => 'second',
     *    'foo' => 'bar',
     *    'ho' => 'sa',
     * )
     *
     * @param string $element
     * @param string $childrenElement
     * @param string $keyName
     *
     * @return array
     */
    public function getParameterValues($element = 'parameters', $childrenElement = 'parameter', $keyName = 'id')
    {
        $values = array();
        $params = $this->getDirectChild($element);
        if ($params) {
            foreach ($params->childNodes as $param) {
                /** @var $param \DOMNode */
                if ($childrenElement === $param->nodeName) {
                    if ($id = $param->attributes->getNamedItem($keyName)->nodeValue) {
                        $values[$id] = $param->nodeValue;
                    } else {
                        $values[] = $param->nodeValue;
                    }
                }
            }
        }
        return $values;
    }

    /**
     * @param string $variableName
     */
    public function setVar($variableName)
    {
        $element = $this->getDirectChild($variableName);
        if ($element) {
            $setter = 'set' . ucfirst($variableName);
            $value = $element->nodeValue;

            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }

    /**
     * @param string $variableName
     */
    public function setAttributeVar($variableName)
    {
        $element = $this->element->attributes->getNamedItem($variableName);
        if ($element) {
            $setter = 'set' . ucfirst($variableName);
            $value = $element->nodeValue;

            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }

    /**
     * @param \DOMElement $element
     */
    public function setElement($element)
    {
        $this->element = $element;
    }

    /**
     * @return \DOMElement
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @param Config $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param string $tag
     *
     * @return \DOMElement[]
     */
    public function getDirectChildren($tag)
    {
        $children = array();
        $root     = $this->element->firstChild && $this->element->firstChild->nodeName == 'bundle'
            ? $this->element->firstChild
            : $this->element;

        foreach ($root->childNodes as $child) {
            if ($child->nodeName == $tag) {
                $children[] = $child;
            }
        }
        return $children;
    }

    /**
     * @param string $tag
     *
     * @return \DOMElement
     */
    public function getDirectChild($tag)
    {
        $root = $this->element->firstChild && $this->element->firstChild->nodeName == 'bundle'
            ? $this->element->firstChild
            : $this->element;

        if ($root->childNodes) {
            foreach ($root->childNodes as $child) {
                if ($child->nodeName == $tag) {
                    return $child;
                }
            }
        }
    }

    /**
     * @param null $element
     *
     * @return array
     */
    public function toArray($element = null)
    {
        $result = array();
        $blacklist = array('config', 'element');
        $element = $element ?: $this;

        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getDefaultProperties();

        foreach ($element as $k => $v) {
            if (in_array($k, $blacklist)) {
                continue;
            }

            $getter = 'get' . ucfirst($k);
            if (is_callable(array($this, $getter))) {
                $value = $this->$getter();

                if ($value === $properties[$k]) continue;

                $result[$k] = $value;
                if (is_array($result[$k])) {
                    foreach ($result[$k] as &$item) {
                        if (is_object($item)) {
                            if ($item instanceof Model) {
                                $item = $item->toArray();
                            } else {
                                $item = (array)$item;
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    public function dumpElement(\DOMElement $element)
    {
        $doc = new \DOMDocument();
        $doc->appendChild($doc->importNode($element->cloneNode(true), true));
        return $doc->saveXML();
    }
}