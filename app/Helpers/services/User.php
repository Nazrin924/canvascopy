<?php

namespace App\Helpers\services;

/**
 * BbPHP: Blackboard Web Services Library for PHP
 * Copyright (C) 2011 by St. Edward's University (www.stedwards.edu)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * This is a stub class for service calls made under the User service.
 *
 * @author johns
 */
class User extends Service
{
    public function getUser($args)
    {
        $body = '<ns1:filter xmlns:ns2="http://user.ws.blackboard/xsd">';

        foreach ($args as $key => $arg) {
            $body .= '<ns2:'.$key.'>'.$arg.'</ns2:'.$key.'>';
        }

        $body .= '</ns1:filter>';

        return parent::buildBody('getUser', 'User', $body);
    }

    /**
     * Function to save user to Blackboard.
     */
    public function saveUser($args)
    {
        $body = '<ns1:user>';

        foreach ($args as $key => $arg) {
            if ($key == 'extendedInfo') {
                $body .= '<ns1:extendedInfo>';

                foreach ($arg as $key => $arg) {
                    $body .= '<ns1:'.$key.'>'.$arg.'</ns1:'.$key.'>';
                }
                $body .= '</ns1:extendedInfo>';
            } else {
                $body .= '<ns1:'.$key.'>'.$arg.'</ns1:'.$key.'>';
            }
        }

        $body .= '</ns1:user>';

        return parent::buildBody('saveUser', 'User', $body);
    }

    public function __call($method, $args = null)
    {
        return parent::buildBody($method, 'User', $args[0]);
    }
}
