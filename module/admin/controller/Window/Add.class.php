<?php


/**
 * This class should be used as motherclass in your window classes, which
 * are defined from the admin entry points in your extension.
 *
 * @author MArc Schmidt <marc@kryn.org>
 */

namespace Admin\Window;

class Add extends Edit {

    public $versioning = false;

    function saveItem() {

        $row = array();

        foreach ($this->_fields as $key => $field) {

            if ($field['fake'] == true) continue;

            $val = getArgv($key);

            //compatibility, deprecated
            $mod = ($field['add']['modifier']) ? $field['add']['modifier'] : $field['modifier'];
            if ($mod) {
                $val = $this->$mod($val);
            }

            if ($field['customSave'] != '') {
                $func = $field['customSave'];
                if (method_exists($this, $func))
                    $this->$func($row);
                else if (function_exists($func))
                    $func($row);
                continue;
            }

            if (is_array($val))
                $val = json_encode($val);

            $row[$key] = $val;
        }

        if ($this->multiLanguage) {
            $row['lang'] = getArgv('lang', 2);
        }

        if ($this->domainDepended) {
            $row['lang'] = getArgv('domain_id')+0;
        }

        if ($this->object){

            $this->last = krynObjects::add($this->object, $row);

            if (is_array($this->last)){
                //error
                return $this->last;
            }

            //custom saves
            foreach ($this->_fields as $key => $field) {
                if (!empty($field['customSave'])) {
                    $func = $field['customSave'];
                    $this->$func();
                }
            }

        } else {

            $this->last = dbInsert($this->table, $row);
            $_REQUEST[$this->primary[0]] = $this->last;

            //relations
            foreach ($this->_fields as $key => $field) {
                if ($field['relation'] == 'n-n') {
                    $values = json_decode(getArgv($key));
                    foreach ($values as $value) {
                        $sqlInsert = "
                            INSERT INTO %pfx%" . $field['n-n']['middle'] . "
                            ( " . $field['n-n']['middle_keyleft'] . ", " . $field['n-n']['middle_keyright'] . " )
                            VALUES ( '" . getArgv($field['n-n']['left_key']) . "', '$value' );";
                        dbExec($sqlInsert);
                    }
                }
            }
        }

        //custom saves
        foreach ($this->_fields as $key => $field) {
            if (!empty($field['customSave'])) {
                $func = $field['customSave'];
                $this->$func();
            }
        }


        return array('last_id' => $this->last);
    }
}

/*
* Compatibility for older extension
* @deprecated
*/
class windowAdd extends adminWindowAdd {

}

?>
