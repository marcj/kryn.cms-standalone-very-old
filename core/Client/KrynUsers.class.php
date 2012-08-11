<?php

namespace Core\Client;

class KrynUsers extends AuthAbstract {


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
                if (Kryn::$config['passwd_hash_compat'] != 1) return false;
                //compatibility
                $hash = md5($pPassword);
            }

            if ($hash != $row['passwd']) return false;

            return $row['id'];
        }
        return false;
    }

    public function createSessionById($pId){

        $cacheKey = $this->tokenId . '_' . $pId;

        //this is a critical section, since between checking whether a session exists
        //and setting the session object, another thread or another server (in the cluster)
        //can write the cache key.
        //So we LOCK all kryn php instances, like in multithreaded apps, but with all
        //cluster buddies too.
        \Core\Utils::lock('ClientCreateSession');

        //session id already used?
        if ($this->cache->get($cacheKey)) return false;

        $session = new \Session();
        $session->setId($token)
            ->setTime(time())
            ->setIp($_SERVER['REMOTE_ADDR'])
            ->setPage(Kryn::getRequestedPath(true))
            ->setUseragent($_SERVER['HTTP_USER_AGENT'])
            ->setIsStoredInDatabase(false);

        if (!$this->cache->set($this->tokenId . '_' . $token, $session, $expired))
            return false;

        $this->store->set($cacheKey, $this->getSession(), $this->config['timeout']);

        return $session;
    }

}