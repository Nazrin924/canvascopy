<?php

namespace App\Helpers\services;

/**
 * BbPHP: Blackboard Web Services Library for PHP
 * Copyright (C) 2011 by St. Edward's University (www.stedwards.edu)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * This is a stub class for service calls made under the Context service.
 *
 * @author johns
 */
class Context extends Service
{
    public function __call($method, $args = [''])
    {
        return parent::buildBody($method, 'Context', $args[0]);
    }

    public function login($args)
    {

        $loginusername = env('BB_WS_USERNAME', 'cuwsbbtools');
        $loginpassword = env('BB_WS_PASSWORD', 'cuwsbbtools');

        $args = [
            'userid' => $loginusername,
            'password' => $loginpassword,
            'clientVendorId' => 'blackboard',
            'clientProgramId' => 'Blackboard Inc.',
            'loginExtraInfo' => true,
            'expectedLifeSeconds' => 140000,
        ];

        return parent::buildBody('login', 'Context', $args);
    }
}
