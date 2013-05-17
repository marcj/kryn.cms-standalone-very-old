<?php

namespace Admin\Controller\Stores;

use Admin\Store;
use Core\Kryn;

class Layout extends Store
{
    public function getItem($pId)
    {
        $items = self::getItems();
        foreach ($items as $k => $v) {
            if ($k == $pId) {
                return $v;
            }
        }
    }

    public function getItems($pOffset = 0, $pLimit = 0)
    {
        $res = array();
        $c = 0;

        foreach (Kryn::getConfigs() as $config) {

            foreach ($config->getThemes() as $theme) {
                $c++;
                if ($c > $pOffset && (!$pLimit || $c <= $pLimit)) {
                    $res[] = array('label' => $theme->getLabel(), 'isSplit' => true);
                }
                foreach ($theme->getLayouts() as $layout) {
                    $c++;
                    if ($c > $pOffset && (!$pLimit || $c <= $pLimit)) {
                        $res[$layout->getFile()] = array('label' => $layout->getLabel() . ' (' . $theme->getLabel() . ')');
                    }
                }
            }
        }

        return $res;

    }

}
