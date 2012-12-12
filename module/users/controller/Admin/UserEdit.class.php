<?php

namespace Users\Admin;

class UserEdit extends \Admin\ObjectWindow {

    public $object = 'user';

    public $loadSettingsAfterSave = true;
    
    private static $cacheUser = false;


}
