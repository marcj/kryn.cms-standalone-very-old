<?php

namespace Admin;

use Core\Bundle;

class AdminBundle extends Bundle
{
    public function getComposer()
    {
        return [
            'name' => 'krynlabs/kryn.cms-admin',
            'activated' => true,
            'description' => 'The administration bundle.',
            'license' => 'LGPL'
        ];
    }
}