<?php

namespace Core\Template;

use Core\Kryn;

class Engine
{

    private static $instances = array();

    private $engine;

    public static function createForFileName($path)
    {
        $engine = preg_replace('/[^a-zA-Z0-9_]/', '', substr($path, strrpos($path, '.')));

        $class = 'Core\\Template\\Engines\\' . ucfirst(strtolower($engine));

        if (!class_exists($class)) {
            $class = 'Core\\Template\\Engines\\None';
        }

        return isset(self::$instances[$class]) ? self::$instances[$class] : self::$instances[$class] = new Engine($class);
    }

    public function __construct($class)
    {
        $this->engine = new $class();
    }

    public function render($view, $data = null)
    {

        if (strpos($view, PATH) === 0) {
            $view = substr($view, strlen(PATH));
        }

        if (!is_file($view)) {
            throw new TemplateException(sprintf('View `%s` does not exist.', $view));
        }

        try {
            return $this->engine->render($view, $data);
        } catch (\Exception $e) {
            throw new TemplateException(sprintf('View `%s` raised a error.', $view), $e->getCode(), $e);
        }
    }

}