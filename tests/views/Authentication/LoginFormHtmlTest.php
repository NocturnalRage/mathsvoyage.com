<?php

namespace Tests\views;

use Framework\Response;

class LoginFormHtmlTest extends BasicViewHtmlTestCase
{
    public function testBasicLoginFormViewWhenLoggedOut()
    {
        $recaptchaKey = 'recaptcha';
        $response = new Response('views');
        $response->setView('Authentication/loginForm.html.php');
        $response->setVars([
            'loggedIn' => false,
            'pageTitle' => 'Login',
            'metaDescription' => 'Login to my website.',
            'activeLink' => 'Login',
            'recaptchaKey' => $recaptchaKey,
            'crsfToken' => 'crsf',
            'loggedIn' => false,
            'user' => null,
            'isAdmin' => false,
        ]);
        $output = $response->requireView();
        $expect = 'Welcome Back!';
        $this->assertStringContainsString($expect, $output);
        $expect = '<form id="loginForm" method="post" action="/login">';
        $this->assertStringContainsString($expect, $output);
        $expect = 'Login To Your Account';
        $this->assertStringContainsString($expect, $output);
        $expect = 'class="g-recaptcha btn btn-primary"';
        $this->assertStringContainsString($expect, $output);
        $expect = 'data-sitekey="'.$recaptchaKey.'"';
        $this->assertStringContainsString($expect, $output);
        $expect = '<script src="https://www.google.com/recaptcha/api.js"></script>';
        $this->assertStringContainsString($expect, $output);
    }
}
