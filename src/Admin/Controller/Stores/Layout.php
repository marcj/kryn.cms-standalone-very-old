<?php

namespace Admin\Controller\Stores;

use Admin\Controller\Store;
use Core\Kryn;

class Layout extends Store
{
    public function getItem($id)
    {
        $items = self::getItems();
        foreach ($items as $k => $v) {
            if ($k == $id) {
                return $v;
            }
        }
    }

    public function getItems($offset = 0, $limit = 0)
    {
        $res = array();
        $c = 0;

        foreach (Kryn::getConfigs() as $config) {

            foreach ($config->getThemes() as $theme) {
                $c++;
                if ($c > $offset && (!$limit || $c <= $limit)) {
                    $res[] = array('label' => $theme->getLabel(), 'isSplit' => true);
                }
                foreach ($theme->getLayouts() as $layout) {
                    $c++;
                    if ($c > $offset && (!$limit || $c <= $limit)) {
                        $res[$layout->getFile()] = array('label' => $layout->getLabel() . ' (' . $theme->getLabel() . ')');
                    }
                }
            }
        }

        return $res;

    }

}
