<?php

/*
 * This file is part of Kryn.cms.
 *
 * (c) Kryn.labs, MArc Schmidt <marc@kryn.org>
 *
 * To get the full copyright and license informations, please view the
 * LICENSE file, that was distributed with this source code.
 *
 */


class adminWidgets {

    public static function init() {
        switch (getArgv(3)) {
            case 'getWidgets':
                json(self::getWidgets(getArgv('category')));
            case 'getPage':
                json(self::getPage());
            case 'load':
                return self::load();
            case 'getAll':
                return self::getAll();
            case 'getWidgetInfo':
                return self::getWidgetInfo();
            case 'saveAll':
                return self::saveAll();
            case 'loadAll':
                return self::loadAll();
        }
    }

    public static function getPage() {

        $ext = getArgv('extension');
        $widgetCode = getArgv('widget');

        $widget = kryn::$configs[$ext]['widgets'][$widgetCode];

        if ($widget['sql']) {
            $perPage = $widget['itemsPerPage'] ? $widget['itemsPerPage'] : 15;

            $page = getArgv('page') + 0;


            $from = ($perPage * $page) - $perPage;
            $count = $perPage;

            $title = _l($widget['title']);

            $return = array('items', 'count', 'title' => $title);

            $sql = $widget['sql'];

            if ($sql == "") return $return;

            if (strpos($sql, 'LIMIT ') === false) {
                $limit = ' LIMIT ' . $count . ' OFFSET ' . $from;
            }

            //$count = dbExec( $sql ); //preg_replace('/SELECT(.*)FROM/mi', 'SELECT count(0) as ctn FROM', str_replace("\n", " ", $sql ) ) );
            $countSql = preg_replace('/SELECT(.*)FROM/mi', 'SELECT count(0) as ctn FROM', str_replace("\n", " ", $sql));
            $countSql = preg_replace('/ORDER BY (.*)/', '', $countSql);
            $countRow = dbExfetch($countSql);
            $return['count'] = $countRow['ctn'] + 0;
            //$count->closeCursor();

            $res = dbExec($sql . $limit, -1);

            if (!$widget['withoutCountInTitle'])
                $return['title'] .= ' (' . $return['count'] . ')';

            $maxPages = 1;
            if ($return['count'] > 0) {
                $maxPages = ceil($return['count'] / $perPage);
            }
            $return['maxPages'] = $maxPages;
            while ($row = dbFetch($res, 1)) {

                $item = array();
                $idNr = 0;
                foreach ($row as $columnKey => $columnValue) {

                    //converts
                    if ($widget['columns'][$idNr][2] == 'timestamp') {
                        if ($widget['columns'][$idNr][3])
                            $columnValue = date($widget['columns'][$idNr][3], $columnValue);
                        else
                            $columnValue = date('d M H:i:s', $columnValue);
                    }


                    $item[] = $columnValue;

                    $idNr++;
                }
                if ($widget['manipulate_row'] && kryn::$modules[$ext] &&
                    method_exists(kryn::$modules[$ext], $widget['manipulate_row'])
                )
                    $item = kryn::$modules[$ext]->$widget['manipulate_row']($item);
                $return['items'][] = $item;

            }
        }

        if ($widget['method'] && kryn::$modules[$ext] && method_exists(kryn::$modules[$ext], $widget['method']))
            return kryn::$modules[$ext]->$widget['method']();


        return $return;

    }

    public static function getWidgets($pCategory) {
        global $user;

        $res = array();
        foreach (kryn::$configs as $modCode => $mod) {
            if ($mod['widgets'] && is_array($mod['widgets'])) {
                foreach ($mod['widgets'] as $key => $widget) {

                    if ($widget['category'] == $pCategory) {
                        $widget['code'] = $key;
                        $widget['extension'] = $modCode;
                        $res[$modCode]['widgets'][] = $widget;
                    }

                }
                if ($res[$modCode]['widgets']) {
                    $res[$modCode]['title'] =
                        $mod['title'][$user->getSessionLanguage()] ? $mod['title'][$user->getSessionLanguage()] :
                            $mod['title']['en'];
                }
            }
        }

        return $res;

    }

    public static function getWidgetInfo() {

        $module = getArgv('module');
        $widget = getArgv('widget');
        $widgets = kryn::$configs[$module]['widgets'];
        $res = $widgets[$widget][1];
        $res['module'] = $module;
        $res['widget'] = $widget;
        json($res);
    }

    public static function getAll() {

        foreach (kryn::$configs as $key => $config) {
            if (count($config['widgets']) > 0)
                $widgets[$key] = array($module, $config['widgets']);
        }
        tAssign('widgets', $widgets);
        $res = tFetch('admin/overview.allWidgets.tpl');
        json($res);
    }

    public static function loadAll() {
        global $user;
        $widgets = json_decode($user->user['widgets'], true);
        json($widgets);
    }

    public static function saveAll() {
        global $user;
        $id = $user->user_id;
        #        $widgets = json_decode( $_POST['widgets'], true );
        $widgets = $_POST['widgets'];
        dbUpdate('system_user', "id=$id", array('widgets' => $widgets));
        json(true);
    }

    public static function load() {

        $module = getArgv('module');
        $widget = getArgv('widget');

        $conf = array();
        $values = kryn::$modules[$module]->$widget($conf);
        json($values);
    }

}

?>
