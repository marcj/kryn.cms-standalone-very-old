<?php

namespace Core\Template\Engines;

/**
 * Template engine None.
 */
class Smarty implements EnginesInterface {

    private static $instance;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new \Smarty();

            self::$instance->template_dir = './';
            self::$instance->registerClass('Kryn', 'Core\Kryn');
            self::$instance->registerClass('Navigation', 'Core\Navigation');
            self::$instance->addPluginsDir(__DIR__ . '/../SmartyPlugins');

            include __DIR__ . '/../SmartyPlugins/smarty_internal_compile_asset.php';
            include __DIR__ . '/../SmartyPlugins/smarty_internal_compile_tc.php';
            include __DIR__ . '/../SmartyPlugins/smarty_internal_compile_t.php';
            self::$instance->loadPlugin('Smarty_Internal_Compile_Tc');
            self::$instance->loadPlugin('Smarty_Internal_Compile_T');
            self::$instance->error_reporting = E_ALL & ~E_NOTICE;

            self::$instance->assign('random', mt_rand());

            if (!is_dir($compileDir = \Core\Kryn::getTempFolder().'smarty-compile/'))
                mkdir($compileDir);

            self::$instance->compile_dir = $compileDir;
        }
        return self::$instance;
    }

    public function render($file, $data = null){
        $smarty = self::getInstance();
        $view   = $smarty->createTemplate($file, $data);
        return $view->fetch($file);
    }

}