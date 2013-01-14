<?php

namespace Admin;

use Core\SystemFile;

class Config {

    public static function getLabels() {
        $res['langs'] = dbTableFetchAll('system_langs', "1=1 ORDER BY title");
        $res['timezones'] = timezone_identifiers_list();
        return $res;
    }

    public static function getConfig() {
        global $cfg;

        $cfg = include('config.php');

        $settings['system'] = $cfg;

        return $settings;
    }

    public static function saveConfig() {

        $cfg = include('config.php');

        $blacklist[] = 'passwd_hash_key';

        if (!getArgv('sessiontime'))
            $_REQUEST['sessiontime'] = 3600;

        foreach ($_POST as $key => $value) {
            if (!in_array($key, $blacklist)) {
                $cfg[$key] = getArgv($key);
            }
        }

        SystemFile::setContent('config.php', "<?php return " . var_export($cfg, true) . "\n?>");

        dbUpdate('system_langs', array('visible' => 1), array('visible' => 0));
        $langs = getArgv('languages');
        foreach ($langs as $l)
            dbUpdate('system_langs', array('code' => $l), array('visible' => 1));

        return true;
    }

    public static function saveCommunity() {
        global $cfg;

        $pw = md5(getArgv('passwd'));
        $email = getArgv('email', 1);
        $json = wget("http://www.kryn.org/rpc?t=checkLogin&email=$email&pw=$pw");
        if ($json === false)
            json(2);
        $res = json_decode($json, true);
        if ($res['status'] >= 1) {
            $cfg['communityEmail'] = $email;
            $cfg['communityId'] = $res['id'];
            kryn::fileWrite('inc/config.php', "<?php \n\$cfg = " . var_export($cfg, true) . "\n?>");
            json(1);
        }

        json(0);
    }


}

?>
