<?php

namespace Tests\views;

use Framework\Response;

class RegisterThanksHtmlTest extends BasicViewHtmlTestCase
{
    public function testBasicRegisterThanksViewWhenLoggedOut()
    {
        $email = 'johndoe@example.com';
        $response = new Response('views');
        $response->setView('Authentication/registerThanks.html.php');
        $response->setVars([
            'loggedIn' => false,
            'pageTitle' => 'Thanks for joining',
            'metaDescription' => 'Thanks for joining our website',
            'activeLink' => 'Register',
            'crsfToken' => 'crsf',
            'loggedIn' => false,
            'user' => null,
            'isAdmin' => false,
        ]);
        $output = $response->requireView();
        $expect = 'Welcome to Cube Shack';
        $this->assertStringContainsString($expect, $output);
        $expect = 'Your Email Address Has Been Confirmed';
        $this->assertStringContainsString($expect, $output);
    }
}
