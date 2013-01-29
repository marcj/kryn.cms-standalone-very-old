<?php

namespace Admin\Stores;

use Admin\Store;
use Core\Kryn;

class Layout extends Store {


    public function getItem($pId) {
        $items = self::getItems();
        foreach ($items as $k => $v){
            if ($k == $pId)
                return $v;
        }
    }

    public function getItems($pOffset = 0, $pLimit = 0) {

        $res = array();
        $c = 0;

        foreach (Kryn::$configs as $key => $config){

            foreach ($config['themes'] as $themeTitle => $themeConfig){
                if ($themeConfig['layouts']){
                    $c++;
                    if ($c > $pOffset && (!$pLimit || $c <= $pLimit))
                        $res[] = array('label' => $themeTitle, 'isSplit' => true);
                    foreach ($themeConfig['layouts'] as $title => $file){
                        $c++;
                        if ($c > $pOffset && (!$pLimit || $c <= $pLimit))
                            $res[$file] = array('label' => $title.' ('.$themeTitle.')');
                    }
                }
            }
        }

        return $res;

    }


}
