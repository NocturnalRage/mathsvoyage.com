<?php

namespace Tests\views;

use Framework\Response;

class RegisterFormHtmlTest extends BasicViewHtmlTestCase
{
    public function testBasicRegisterFormViewWhenLoggedOut()
    {
        $recaptchaKey = 'recaptcha';
        $response = new Response('views');
        $response->setView('Authentication/registerForm.html.php');
        $response->setVars([
            'loggedIn' => false,
            'pageTitle' => 'Register',
            'metaDescription' => 'Register',
            'activeLink' => 'Register',
            'recaptchaKey' => $recaptchaKey,
            'crsfToken' => 'crsf',
            'loggedIn' => false,
            'user' => null,
            'isAdmin' => false,
        ]);
        $output = $response->requireView();
        $expect = '<form id="registerForm" method="post" action="/register">';
        $this->assertStringContainsString($expect, $output);
        $expect = 'Register';
        $this->assertStringContainsString($expect, $output);
        $expect = "Let's set up your free account";
        $this->assertStringContainsString($expect, $output);
        $expect = 'class="g-recaptcha btn btn-primary"';
        $this->assertStringContainsString($expect, $output);
        $expect = 'data-sitekey="'.$recaptchaKey.'"';
        $this->assertStringContainsString($expect, $output);
        $expect = '<script src="https://www.google.com/recaptcha/api.js"></script>';
        $this->assertStringContainsString($expect, $output);
    }
}
