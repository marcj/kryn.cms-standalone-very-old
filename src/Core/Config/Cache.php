<?php

namespace Core\Config;

class Cache extends Model
{
    protected $docBlock = '
  The cache layer we use for the distributed caching.
  (The `fast caching` is auto determined (Order: APC, XCache, Files))
  ';

    protected $docBlocks = [
        'class' => 'The full classname of the storage. MUST have `Core\Cache\CacheInterface` as interface.'
    ];


    /**
     * @var string
     */
    protected $class = '\Core\Cache\Files';

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

    public function setOption($key, $value)
    {
        if (null === $this->options) {
            $this->options = new Options();
        }

        $this->options->setOption($key, $value);
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