<?php

namespace Core\Client;

use Core\Kryn;
use Core\Utils;

class KrynUsers extends ClientAbstract {

    /**
     * Checks the given credentials.
     *
     * @param string $pLogin
     * @param string $pPassword
     *
     * @return bool|integer Returns false if credentials are wrong and returns the user id, if credentials are correct.
     */
    public function checkCredentials($pLogin, $pPassword) {

        $login = $pLogin;

        $userColumn = 'username';

        if ($this->config['emailLogin'] && strpos($pLogin, '@') !== false && strpos($pLogin, '.') !== false)
            $userColumn = 'email';

        $row = dbExfetch("
            SELECT id, passwd, passwd_salt
            FROM %pfx%system_user
            WHERE 
                id > 0
                AND $userColumn = ?
                AND (auth_class IS NULL OR auth_class = 'kryn')",
            $login);

        if ($row['id'] > 0) {

            if ($row['passwd_salt']) {
                $hash = self::getHashedPassword($pPassword, $row['passwd_salt']);
            } else {
                if (Kryn::$config['passwdHashCombat'] != 1) return false;
                //compatibility
                $hash = md5($pPassword);
            }

            if ($hash != $row['passwd']) return false;

            return $row['id'];
        }
        return false;
    }

}