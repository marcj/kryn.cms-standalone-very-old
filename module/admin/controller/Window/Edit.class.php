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

namespace Admin\Window;

class Edit extends WindowAbstract {


    public function unlock($pType, $pId) {
        dbDelete('system_lock', "type = '$pType' AND key = '$pId'");
        return true;
    }

    public function canLock($pType, $pId) {
        global $user;

        $row = dbTableFetch('system_lock', 1, "type = '$pType' AND key = '$pId'");
        if ($row['session_id'] == $user->sessionid) return true;
        if (!$row['id'] > 0) return true;

        $user = dbTableFetch('system_user');
        return false;
    }

    public function lock($pType, $pId) {
        global $user;

        $row = dbTableFetch('system_lock', 1, "type = '$pType' AND key = '$pId'");
        if ($row['id'] > 0) return false;

        dbInsert('system_lock', array(
            'type' => $pType,
            'key' => $pId,
            'session_id' => $user->sessionid,
            'time' => time()
        ));
        return true;
    }


    public function getPreviewUrls($pRow) {

        if ($this->previewPlugins) {

            $cachedPluginRelations =& Core\Kryn::getCache('kryn_pluginrelations');
            $module = $this->module;

            foreach ($this->previewPlugins as $plugin => $urlGetter) {

                $moduleToUse = $module;
                $pluginToUse = $plugin;

                if (strpos($plugin, '/') !== false) {
                    $ex = explode('/', $plugin);
                    $moduleToUse = $ex[0];
                    $pluginToUse = $ex[1];
                }

                $pages =& $cachedPluginRelations[$moduleToUse][$pluginToUse];
                if (count($pages) > 0) {
                    foreach ($pages as &$page) {

                        $pluginValue = substr($page['content'], strpos($page['content'], '::'));
                        $pluginValue = substr($pluginValue, strpos($pluginValue, '::'));

                        if (method_exists($this, $urlGetter)) {
                            $previewUrls[$moduleToUse . '/' . $pluginToUse][$page['id']] =
                                Core\Kryn::pageUrl($page['id']) . '/' .
                                    $this->$urlGetter($pRow, json_decode($pluginValue, true), $page['id']);
                        }

                    }
                }
            }
        }
        return $previewUrls;
    }

    /**
     * Loads all plugins from system_contents to a indexed cached array
     */
    public static function cachePluginsRelations() {

        $res = dbExec('
        SELECT p.domain_id, p.id, c.content, p.title
        FROM 
            %pfx%system_contents c,
            %pfx%system_page_version v,
            %pfx%system_page p
        WHERE 1=1
            AND c.type = \'plugin\'
            AND c.hide = 0
            AND v.id = c.version_id
            AND p.id = v.page_id
            AND (p.access_denied = \'0\' OR p.access_denied IS NULL)
            AND v.active = 1
        ');

        if (!$res) {
            Core\Kryn::setCache('kryn_pluginrelations', array());
            return;
        }

        $pluginRelations = array();

        while ($row = dbFetch($res)) {

            preg_match('/([a-zA-Z0-9_-]*)::([a-zA-Z0-9_-]*)::(.*)/', $row['content'], $matches);
            $pluginRelations[$matches[1]][$matches[2]][] = $row;

        }
        Core\Kryn::setCache('kryn_pluginrelations', $pluginRelations);
    }



    /**
     * Loads all pages which have included the plugin in $previewPlugins
     */
    public function loadPreviewPages() {

        if (!$this->previewPlugins)
            return;

        $cachedPluginRelations =& Core\Kryn::getCache('kryn_pluginrelations');
        if (true || !$cachedPluginRelations || count($cachedPluginRelations) == 0) {
            self::cachePluginsRelations();
            $cachedPluginRelations =& Core\Kryn::getCache('kryn_pluginrelations');
        }

        $module = $this->module;

        $this->previewPluginPages = array();

        foreach ($this->previewPlugins as $plugin => $urlGetter) {

            $moduleToUse = $module;
            $pluginToUse = $plugin;

            if (strpos($plugin, '/') !== false) {
                $ex = explode('/', $plugin);
                $moduleToUse = $ex[0];
                $pluginToUse = $ex[1];
            }

            $pages =& $cachedPluginRelations[$moduleToUse][$pluginToUse];
            if (count($pages) > 0) {
                foreach ($pages as &$page) {
                    $this->previewPluginPages[$moduleToUse . '/' . $pluginToUse][$page['domain_id']][$page['id']] =
                        array(
                            'title' => $page['title'],
                            'path' => Core\Kryn::getPagePath($page['id'])
                        );
                }
            }
        }
    }

    /**
     * Return the selected item from database.
     *
     * @return array
     */
    public function getItem($pPk) {

        $pk = \Core\Object::parsePk($this->object, $pPk);
        $fields = array();

        foreach ($this->_fields as $key => $field){
            if (!$field['customValue'] && !($field['select'] && $field['relation'] == 'n-n')
                && !in_array($key, $fields)){
                $fields[] = $key;
            }
        }

        $res['values'] = \Core\Object::get($this->object, $pk, array(
            'fields' => implode(',', $fields)
        ));

    }
    
    /**
     * Saves the item to database.
     */
    public function saveItem() {

        $tableInfo = $this->db[$this->table];

        $values = array();

        $row = array();
        foreach ($this->_fields as $key => $field) {
            if ($field['fake'] == true) continue;
            if ($field['type'] == 'subview') continue;

            $val = getArgv($key);

            if ($field['update']['onlyIfFilled'] || $field['onlyIfFilled']) {
                if (empty($val)) continue;
            }

            if ($field['customSave'] != '') {
                $func = $field['customSave'];
                if (method_exists($this, $func))
                    $this->$func($row);
                else if (function_exists($func))
                    $func($row);
                continue;
            }

            if ($field['disabled'] == true)
                continue;

            if ($field['type'] == 'select' && $field['relation'] == 'n-n')
                continue;

            $mod = ($field['update']['modifier']) ? $field['update']['modifier'] : $field['modifier'];
            if ($mod) {
                #$val = $this->$mod($val);
                if (method_exists($this, $mod))
                    $val = $this->$mod($val);
                else if (function_exists($mod))
                    $val = $mod($val);
            }

            if ($field['type'] == 'fileList') {
                $val = json_encode($val);
            } else if ($field['type'] == 'select' && $field['multi'] && !$field['relation']) {
                $val = json_encode($val);
            }

            $row[$key] = $val;
        }

        if (getArgv('_kryn_relation_table')) {
            $relation = database::getRelation(getArgv('_kryn_relation_table'), $this->table);
            if ($relation) {
                $params = getArgv('_kryn_relation_params');
                foreach ($relation['fields'] as $field_left => $field_right) {
                    if (!$row[$field_right]) {
                        $row[$field_right] = $params[$field_right];
                    }
                }
            }
        }

        if ($this->multiLanguage) {
            $curLang = getArgv('lang', 2);
            $row['lang'] = $curLang;
        }

        $primary = array();
        foreach ($this->primary as $field) {
            $val = getArgv($field);

            if (isset($val)) {
                $primary[$field] = $val;
                $row[$field] = $val;
            }
        }

        $res = array();
        if ($this->versioning == true && getArgv('publish') != 1) {

            //only save in versiontable
            $res['version_id'] = admin::addVersionRow($this->table, $primary, $row);

        } else {


                if ($this->versioning == true) {
                    //save old state
                    admin::addVersion($this->table, $primary);
                }

            if ($this->object){

                $this->last = krynObjects::update($this->object, $primary, $row);

                if (is_array($this->last)){
                    //error
                    return $this->last;
                }

                if ($this->last)
                    $res['success'] = true;

            } else {

                //publish - means: write in origin table
                $res['success'] = dbUpdate($this->table, $primary, $row);

                $res['version_id'] = '-'; //means live

                foreach ($this->_fields as $key => $field) {
                    if ($field['relation'] == 'n-n') {
                        $values = json_decode(getArgv($key));
                        $sqlDelete = "
                            DELETE FROM %pfx%" . $field['n-n']['middle'] . "
                            WHERE " . $field['n-n']['middle_keyleft'] . " = '" . getArgv($field['n-n']['left_key'], 1) .
                                     "'";
                        dbExec($sqlDelete);
                        foreach ($values as $value) {
                            $sqlInsert = "
                                INSERT INTO %pfx%" . $field['n-n']['middle'] . "
                                ( " . $field['n-n']['middle_keyleft'] . ", " . $field['n-n']['middle_keyright'] . " )
                                VALUES ( '" . getArgv($field['n-n']['left_key'], 1) . "', '" . esc($value) . "' );";
                            dbExec($sqlInsert);
                        }
                    }
                }
            }
        }

        $res['preview_urls'] = $this->getPreviewUrls($row);
        return $res;
    }

}


?>
