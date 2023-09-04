<?php

namespace Tests\views;

use Framework\Response;

class RegisterConfirmHtmlTest extends BasicViewHtmlTestCase
{
    public function testBasicRegisterConfirmViewWhenLoggedOut()
    {
        $email = 'johndoe@example.com';
        $response = new Response('views');
        $response->setView('Authentication/registerConfirm.html.php');
        $response->setVars([
            'loggedIn' => false,
            'pageTitle' => 'Confirm Your Email Address',
            'metaDescription' => 'Confirm your email address to complete your registration',
            'activeLink' => 'Register',
            'registeredEmail' => $email,
            'crsfToken' => 'crsf',
            'loggedIn' => false,
            'user' => null,
            'isAdmin' => false,
        ]);
        $output = $response->requireView();
        $expect = 'Confirm Your Email Address';
        $this->assertStringContainsString($expect, $output);
        $expect = "I've just sent you an email to ".$email;
        $this->assertStringContainsString($expect, $output);
    }
}
