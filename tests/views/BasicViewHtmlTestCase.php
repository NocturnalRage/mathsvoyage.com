<?php

namespace Tests\views;

use PHPUnit\Framework\TestCase;

abstract class BasicViewHtmlTestCase extends TestCase
{
    public function setPersonInfoLoggedOut()
    {
        return [
            'loggedIn' => false,
            'details' => null,
            'isAdmin' => false,
            'crsfToken' => 'dummy-crsf-token',
        ];
    }

    public function setPersonInfoLoggedIn($userId = 99)
    {
        return [
            'loggedIn' => true,
            'details' => [
                'user_id' => $userId,
                'given_name' => 'Fred',
            ],
            'isAdmin' => false,
            'crsfToken' => 'dummy-crsf-token',
        ];
    }

    public function setPersonInfoAdmin($userId = 98)
    {
        return [
            'loggedIn' => true,
            'details' => [
                'user_id' => 98,
                'given_name' => 'Fred',
            ],
            'isAdmin' => true,
            'crsfToken' => 'dummy-crsf-token',
        ];
    }
}
