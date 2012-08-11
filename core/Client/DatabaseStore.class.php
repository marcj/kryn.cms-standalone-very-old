<?php

namespace Core\Client;

use Core\Cache\CacheInterface;

class DatabaseStore implements CacheInterface {
	

    public function removeExpiredSessions() {
        $lastTime = time() - $this->config['session_timeout'];
        dbDelete('system_session', 'time < ' . $lastTime);
	}
}