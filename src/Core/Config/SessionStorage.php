<?php

namespace Core\Config;

class SessionStorage extends Model
{
    protected $docBlock = 'A class that handles the actual data storage.';

    protected $docBlocks = [
        'class' => 'The full classname of the storage. MUST have `Core\Cache\CacheInterface` as interface.
      Define `database` for the database storage.'
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