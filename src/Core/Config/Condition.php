<?php

namespace Core\Config;

use Core\Kryn;

/**
 * Class Asset
 *
 * Paths are relative to `
 *
 * @bundlePath/Resources/public`.
 */
class Condition extends Model
{
    /**
     * @var array
     */
    protected $rules = [];

    public function fromArray($values, $key = null)
    {
        $this->rules = $values;
    }

    public function importNode(\DOMNode $element)
    {
        $this->rules = $this->extractNode($element);
    }

    public function extractNode(\DOMNode $element)
    {
        $value = [];

        foreach ($element->childNodes as $node) {
            if ('rule' === $node->tagName) {
                $value[] = array(
                    $node->getAttribute('key'),
                    $node->getAttribute('type') ?: '=',
                    $node->nodeValue
                );
            } else if ('group' === $node->tagName) {
                $value[] = $this->extractNode($node);
            } else if ($node->tagName){
                $value[] = $node->tagName;
            }
        }

        return $value;
    }

        /**
     * Appends the xml structure with our values.
     *
     * @param \DOMNode $node
     * @param \DOMDocument $doc
     * @param boolean $printDefaults
     * @throws \Exception
     */
    public function appendXml(\DOMNode $node, \DOMDocument $doc, $printDefaults = false)
    {
        if ($this->rules) {
            $condition = $doc->createElement('condition');
            foreach ($this->rules as $rule) {
                $this->appendXmlValue(null, $rule, $condition, $doc);
            }
            $node->appendChild($condition);
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
        if (is_array($value)) {
            if (is_array($value[0])) {
                //we have a group
                $group = $doc->createElement('group');
                $node->appendChild($group);
                foreach ($value as $rule) {
                    $this->appendXmlValue(null, $rule, $group, $doc);
                }
            } else {
                //we have a rule
                $rule = $doc->createElement('rule');
                $node->appendChild($rule);
                $rule->setAttribute('key', $value[0]);
                $rule->setAttribute('type', $this->getType($value[1]));
                $rule->nodeValue = $value[2];
            }
        } else if (is_string($value)) {
            //we have 'and' or 'or'.
            $andOr = $doc->createElement($value);
            $node->appendChild($andOr);
        }
    }

    public function getType($type) {
        switch (strtoupper($type)) {
            case '!=': return 'not equal';
            case '=': return 'equal';
            case '>': return 'greater';
            case '<': return 'less';

            case '=<':
            case '<=': return 'lessEqual';

            case '=>':
            case '>=': return 'greaterEqual';

            case '= CURRENT_USER': return 'equal CURRENT_USER';
            case '!= CURRENT_USER': return 'not equal CURRENT_USER';
        }

        return $type;
    }

    /**
     * @param bool $printDefaults
     *
     * @return array
     */
    public function toArray($printDefaults = false)
    {
        return 0 === count($this->rules) ? null : $this->rules;
    }

    /**
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }
}