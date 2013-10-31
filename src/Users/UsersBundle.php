<?php

namespace Users;

use Core\Bundle;

class UsersBundle extends Bundle
{
    public function getComposer()
    {
        return [
            'name' => 'krynlabs/kryn.cms-users',
            'activated' => true,
            'license' => 'LGPL',
            'description' => 'The user management bundle'
        ];
    }
}