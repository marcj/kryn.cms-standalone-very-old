<?php

namespace Core\Config;

class SessionStorage extends Model
{
    protected $docBlock = 'The full classname of the storage. MUST have `Core\Cache\CacheInterface` as interface.';

    protected $docBlocks = [
        'class' => 'The full classname or `database` for the database storage.'
    ];

    /**
     * @var string
     */
    protected $class = 'database';

    /**
     * @var Options
     */
    protected $options;

    /**
     * @param Options $options
     */
    public function setOptions(Options $options)
    {
        $this->options = $options;
    }

    public function addOption($key, $value)
    {
        if (null === $this->options) {
            $this->options = new Options();
        }

        $this->options->addOption($key, $value);
    }

    public function getOption($key)
    {
        if (null === $this->options) {
            return null;
        }

        return $this->options->getOption($key);
    }

    /**
     * @return Options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

}