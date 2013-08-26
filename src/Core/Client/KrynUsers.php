<?php

namespace Core\Client;

use Core\Kryn;

class KrynUsers extends ClientAbstract
{
    /**
     * Checks the given credentials.
     *
     * @param string $login
     * @param string $password
     *
     * @return bool|integer Returns false if credentials are wrong and returns the user id, if credentials are correct.
     */
    public function checkCredentials($login, $password)
    {
        $login2 = $login;

        $userColumn = 'username';

        if ($this->config['emailLogin'] && strpos($login, '@') !== false && strpos($login, '.') !== false) {
            $userColumn = 'email';
        }

        $row = dbExfetch(
            "
                        SELECT id, passwd, passwd_salt
                        FROM " . pfx . "system_user
            WHERE
                id > 0
                AND $userColumn = ?
                AND passwd IS NOT NULL AND passwd != ''
                AND passwd_salt IS NOT NULL AND passwd_salt != ''
                AND (auth_class IS NULL OR auth_class = 'kryn')",
            $login2
        );

        if ($row['id'] > 0) {

            $hash = self::getHashedPassword($password, $row['passwd_salt']);

            if (!$hash || $hash != $row['passwd']) {
                return false;
            }
            return $row['id'];
        }

        return false;
    }

}
