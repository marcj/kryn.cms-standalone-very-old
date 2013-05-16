<?php

namespace Core\Config;

class Client extends Model
{
    protected $docBlock = 'The client session/authorisation/authentication handling.';

    /**
     * @var string
     */
    protected $class = '\Core\Client\KrynUsers';

    /**
     * @var Options
     */
    protected $options;

    /**
     * @var SessionStorage
     */
    protected $sessionStorage;

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

    /**
     * @param SessionStorage $sessionStorage
     */
    public function setSessionStorage(SessionStorage $sessionStorage)
    {
        $this->sessionStorage = $sessionStorage;
    }

    /**
     * @return SessionStorage
     */
    public function getSessionStorage()
    {
        if (null === $this->sessionStorage) {
            $this->sessionStorage = new SessionStorage();
        }
        return $this->sessionStorage;
    }



}