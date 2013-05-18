<?php

namespace Core\Config;

class Model implements \ArrayAccess
{
    /**
     * The element passed in constructor.
     *
     * @var \DOMElement
     */
    protected $element;

    /**
     * @var string
     */
    protected $rootName;

    /**
     * Defines which values are attributes of the <rootName> element.
     *
     * @var array
     */
    protected $attributes = [];

    protected $additionalNodes = [];
    protected $additionalAttributes = [];
    protected $arrayIndexNames = [];
    protected $excludeDefaults = [];

    /**
     * Defines a header comment of values (not attributes).
     *
     * @var array
     */
    protected $docBlocks = [];

    /**
     * Defines a comment for the root element.
     *
     * @var string
     */
    protected $docBlock = '';

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param \DOMElement|string $element
     * @param Config      $config
     */
    public function __construct($element = null, Config $config = null)
    {
        if (null === $this->rootName) {
            $array = explode('\\', get_called_class());
            $this->rootName = lcfirst(array_pop($array));
        }

        if ($config) {
            $this->config = $config;
        }

        if ($element) {
            if (is_string($element)) {
                $dom = new \DOMDocument();
                $dom->loadXml($element);
                $this->element = $dom->firstChild;
            } else if ($element instanceof \DOMElement) {
                $this->element = $element;
            } else {
                return;
            }
            $this->setupObject();
        }
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getAdditional($key)
    {
        return $this->additionalNodes[$key];
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getAdditionalAttribute($key)
    {
        return $this->additionalAttributes[$key];
    }

    /**
     * @return string lowerCased bundle name (without `Bundle` suffix)
     */
    public function getBundleName()
    {
        $bundleConfig = $this->getBundleConfig();

        return $bundleConfig ? strtolower($bundleConfig->getName()) : null;
    }

    /**
     * @return Config
     */
    public function getBundleConfig()
    {
        if (null === $this->config) {
            if ('bundle' === $this->element->nodeName) {
                $this->config = $this->getModelInstance($this->element);
            } else {
                $parent = $this->element;
                while (($parent = $parent->parentNode)) {
                    if ('bundle' === $parent->nodeName) {
                        $this->config = $this->getModelInstance($parent);
                        break;
                    }
                }
            }
        }

        return $this->config;
    }

    /**
     * @param mixed $val
     *
     * @return bool
     */
    public function bool($val)
    {
        return filter_var($val, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Initialize the object.
     */
    public function setupObject()
    {
        $this->importNode($this->element);
    }

    public function importNode(\DOMNode $element)
    {
        $reflection = new \ReflectionClass($this);

        /** @var \DOMNode $child */
        foreach ($element->childNodes as $child) {
            $nodeName = $child->nodeName;
            $value    = $child->nodeValue;

            if ('#text' === $nodeName || '#comment' === $nodeName) {
                continue;
            }

            $setter = 'set' . ucfirst($nodeName);
            $setterValue = $value;
            if (method_exists($this, $setter)) {
                $reflectionMethod = $reflection->getMethod($setter);
                $parameters = $reflectionMethod->getParameters();
                if (1 <= count($parameters)) {
                    $firstParameter = $parameters[0];
                    if ($firstParameter->getClass() && $className = $firstParameter->getClass()->name) {
                        $setterValue = new $className($child);
                    }
                    if ($firstParameter->isArray()){
                        $setterValue = array();
                        foreach ($child->childNodes as $subChild) {
                            if ('#' !== substr($subChild->nodeName, 0, 1)) {
                                $clazz = $this->elementMap[$subChild->nodeName] ?: $this->char2Camelcase($subChild->nodeName, '-');
                                $clazz = '\Core\Config\\' . $clazz;
                                if (class_exists($clazz)) {
                                    $object = new $clazz($subChild);
                                    $setterValue[] = $object;
                                } else {
                                    $setterValue[] = $subChild->nodeValue;
                                }
                            }
                        }
                    }
                }
            }

            if (is_callable(array($this, $setter))) {
                $this->$setter($setterValue);
            } else if (!$this->$nodeName) {
                $this->extractExtraNodes($child, $this->additionalNodes);
            }
        }
        foreach ($element->attributes as $attribute) {
            $nodeName = $attribute->nodeName;
            $value    = $attribute->nodeValue;

            $setter = 'set' . ucfirst($nodeName);
            if (is_callable(array($this, $setter))) {
                $this->$setter($value);
            } else if (!$this->$nodeName) {
                $this->additionalAttributes[$nodeName] = $value;
            }
        }
    }

    private function char2Camelcase($value, $char = '_')
    {
        $ex = explode($char, $value);
        $return = '';
        foreach ($ex as $str) {
            $return .= ucfirst($str);
        }
        return $return;
    }

    /**
     * @param \DOMNode $child
     * @param array    $options
     */
    public function extractExtraNodes(\DOMNode $child, array &$options)
    {
        $key = $child->attributes->getNamedItem('key');
        $key = $key ? $key->nodeValue : ('item' === $child->nodeName ? null : $child->nodeName);

        $valueText = null;
        $valueNodes = array();
        foreach ($child->childNodes as $element) {
            if ('#text' === $element->nodeName) {
                $valueText = $element->nodeValue;
            } else if ('#' !== substr($element->nodeName, 0, 1)) {
                $this->extractExtraNodes($element, $valueNodes);
            }
        }

        $value = 0 == count($valueNodes) ? $valueText : $valueNodes;

        if ($key) {
            $options[$key] = $value;
        } else {
            $options[] = $value;
        }
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
        $root = $this->element->firstChild && $this->element->firstChild->nodeName == 'bundle'
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
     * @return string
     */
    public function __toString()
    {
        return $this->toXml();
    }

    /**
     * Generates a XML string with all current values.
     *
     * @param boolean $printDefaults
     *
     * @return string
     */
    public function toXml($printDefaults = false)
    {
        $doc = new \DOMDocument();
        $doc->formatOutput = true;

        $this->appendXml($doc, $doc, $printDefaults);

        return trim(str_replace("<?xml version=\"1.0\"?>\n", '', $doc->saveXML()));
    }

    /**
     * Saves the xml into a file.
     *
     * @param string  $path
     * @param boolean $withDefaults
     *
     * @return int
     */
    public function save($path, $withDefaults = false)
    {
        $string = $this->toXml($withDefaults);
        return file_put_contents($path, $string);
    }

    /**
     * Appends the xml structure with our values.
     *
     * @param \DOMNode     $node
     * @param \DOMDocument $doc
     * @param boolean      $printDefaults
     */
    public function appendXml(\DOMNode $node, \DOMDocument $doc, $printDefaults = false)
    {
        if ($this->docBlock) {
            $comment = $doc->createComment($this->docBlock);
            $node->appendChild($comment);
        }

        $rootNode = $doc->createElement($this->rootName);
        $node->appendChild($rootNode);

        $reflection = new \ReflectionClass($this);
        $defaultProperties = $reflection->getDefaultProperties();

        $reflectionModel = new \ReflectionClass(__CLASS__);
        $modelProperties = $reflectionModel->getDefaultProperties();

        foreach ($this as $key => $val) {
            $getter = 'get' . ucfirst($key);
            if (!method_exists($this, $getter)) {
                continue;
            }

            $val = $this->$getter();
            if ($defaultProperties[$key] === $val && !$printDefaults && !in_array($key, $this->excludeDefaults)) {
                continue;
            }
            if (array_key_exists($key, $modelProperties)) {
                continue;
            }

            $setter = 'append' . ucfirst($key) . 'Xml';

            if (is_callable(array($this, $setter))) {
                $this->$setter($rootNode, $doc, $printDefaults);
            } else {
                $this->appendXmlValue($key, $val, $rootNode, $doc, null, $printDefaults);
            }
        }

        foreach ($this->additionalNodes as $k => $v) {
            $this->appendXmlValue($k, $v, $rootNode, $doc);
        }

        foreach ($this->additionalAttributes as $k => $v) {
            $rootNode->setAttribute($k, (string)$v);
        }
    }

    /**
     * Appends the xm structure with the given values.
     *
     * @param string       $key
     * @param mixed        $value
     * @param \DOMNode     $node
     * @param \DOMDocument $doc
     * @param boolean      $arrayType
     * @param boolean      $printDefaults
     */
    public function appendXmlValue(
        $key,
        $value,
        \DOMNode $node,
        \DOMDocument $doc,
        $arrayType = false,
        $printDefaults = false
    )
    {
        if (null === $value || (is_scalar($value) && !in_array($key, $this->attributes)) || is_array($value) || $value instanceof Model) {
            if ($comment = $this->docBlocks[$key]) {
                $comment = $doc->createComment($comment);
                $node->appendChild($comment);
            }
        }

        if (is_scalar($value) || null === $value) {
            $value = is_bool($value) ? $value?'true':'false' : (string)$value;
            if ($arrayType) {
                $element = $doc->createElement($this->arrayIndexNames[$arrayType] ?: 'item');
                if (!is_integer($key)) {
                    $element->setAttribute('key', (string)$key);
                }
                $element->nodeValue = $value;
                $node->appendChild($element);
            } else {
                if (in_array($key, $this->attributes)) {
                    $node->setAttribute($key, $value);
                } else {
                    $element = $doc->createElement(is_integer($key) ? ($this->arrayIndexNames[$arrayType] ?: 'item') : $key);
                    $element->nodeValue = $value;
                    $node->appendChild($element);
                }
            }
        } else if (is_array($value)) {
            $element = $doc->createElement(is_integer($key) ? ($this->arrayIndexNames[$arrayType] ?: 'item') : $key);
            foreach ($value as $k => $v) {
                $this->appendXmlValue($k, $v, $element, $doc, $key, $printDefaults);
            }
            $node->appendChild($element);
        } else if ($value instanceof Model) {
            $value->appendXml($node, $doc, $printDefaults);
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
        $element = $element ? : $this;

        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getDefaultProperties();

        foreach ($element as $k => $v) {
            if (in_array($k, $blacklist)) {
                continue;
            }

            $getter = 'get' . ucfirst($k) . 'Array';
            if (!method_exists($this, $getter) || !is_callable(array($this, $getter))) {
                $getter = 'get' . ucfirst($k);
                if (!method_exists($this, $getter) || !is_callable(array($this, $getter))) {
                    continue;
                }
            }
            $value = $this->$getter();

            if ($value === $properties[$k]) {
                continue;
            }

            $result[$k] = $value;

            if (is_array($result[$k])) {
                foreach ($result[$k] as $key => $item) {
                    if (is_object($item)) {
                        if ($item instanceof Model) {
                            $result[$k][$key] = $item->toArray();
                        } else {
                            $result[$k][$key] = (array)$item;
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @param array $values
     */
    public function fromArray(array $values)
    {
        $blacklist = array('config', 'element');

        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getDefaultProperties();

        foreach ($this as $k => $v) {
            if (in_array($k, $blacklist)) {
                continue;
            }

            $setter = 'set' . ucfirst($k) . 'Array';
            if (!method_exists($this, $setter) || !is_callable(array($this, $setter))) {
                $setter = 'set' . ucfirst($k);
                if (!method_exists($this, $setter) || !is_callable(array($this, $setter))) {
                    continue;
                }
            }
            $value = $values[$k];

            if ($value === $properties[$k]) {
                continue;
            }
            if (null === $value) {
                $value = $properties[$k];
            }

            $this->$setter($value);
        }
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (null !== $offset) {
            $setter = 'set' . ucfirst($offset);
            if (is_array($value) && is_array(current($value))) {
                if (is_callable(array($this, $setter . 'Array'))) {
                    $setter .= 'Array';
                }
            }
            $this->$setter($value);
        }
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        $setter = 'get' . ucfirst($offset);
        return is_callable(array($this, $setter)) || is_callable(array($this, $setter . 'Array'));
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->offsetSet($offset, null);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if (null !== $offset) {
            $getter = 'get' . ucfirst($offset);
            if (is_callable(array($this, $getter . 'Array'))) {
                $getter .= 'Array';
            }
            if (is_callable(array($this, $getter))) {
                return $this->$getter();
            }
        }
    }
}