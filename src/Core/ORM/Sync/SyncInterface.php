<?php

namespace Core\ORM\Sync;

use Core\Bundle;
use Core\Config\Object;

interface SyncInterface {
    public function syncObject(Bundle $bundle, Object $object);
}