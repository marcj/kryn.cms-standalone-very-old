<?php

namespace Core\Config;

class Options extends Model
{
    /**
     * @var array
     */
    protected $options;

    protected $rootName = 'options';
    protected $optionName = 'option';

    public function setupObject()
    {
        $this->options = array();
        foreach ($this->element->childNodes as $child) {
            if ('#' !== substr($child->nodeName, 0, 1)){
                $this->extractOptions($child, $this->options);
            }
        }
    }

    public function extractOptions(\DOMNode $child, array &$options)
    {
        $key = $child->attributes->getNamedItem('key');
        $key ? $key = $key->nodeValue : null;

        $valueText = null;
        $valueNodes = array();
        foreach ($child->childNodes as $element) {
            if ('#text' === $element->nodeName) {
                $valueText = $element->nodeValue;
            } else if ('#' !== substr($element->nodeName, 0, 1)) {
                $this->extractOptions($element, $valueNodes);
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
     * @param \DOMNode     $node
     * @param \DOMDocument $doc
     * @param bool         $printDefaults
     */
    protected function appendOptionsXml(\DOMNode $node, \DOMDocument $doc, $printDefaults = false)
    {
        if (null === $this->options || 0 === count($this->options)) {
            return;
        }

        $this->appendOptions($this->options, $node, $doc);
    }

    protected function appendOptions(array $values, \DOMNode $node, \DOMDocument $doc, $name = null)
    {
        foreach ($values as $key => $value) {
            $element = $doc->createElement($name ? : $this->optionName);
            if (!is_integer($key)) {
                $element->setAttribute('key', $key);
            }
            $node->appendChild($element);

            if (is_array($value)) {
                $this->appendOptions($value, $element, $doc, 'value');
            } else {
                $value = is_bool($value) ? $value?'true':'false' : (string)$value;
                $element->nodeValue = $value;
            }
        }
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    public function setOption($key, $val)
    {
        $this->options[$key] = $val;
    }

    public function getOption($key)
    {
        return $this->options[$key];
    }
}