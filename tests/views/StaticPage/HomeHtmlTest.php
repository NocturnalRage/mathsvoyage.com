<?php

namespace Tests\views\StaticPage;

use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class HomeHtmlTest extends BasicViewHtmlTestCase
{
    public function testHome()
    {
        $pageTitle = 'Cube Shack';
        $metaDescription = 'Welcome to my website';
        $activeLink = 'Home';
        $response = new Response('views');
        $response->setView('StaticPage/home.html.php');
        $response->setVars([
            'pageTitle' => $pageTitle,
            'metaDescription' => $metaDescription,
            'activeLink' => $activeLink,
            'crsfToken' => 'crsf',
            'loggedIn' => false,
            'user' => null,
            'isAdmin' => false,
        ]);
        $output = $response->requireView();

        $expect = '<title>'.$pageTitle.'</title>';
        $this->assertStringContainsString($expect, $output);
        $expect = '<meta name="description" content="'.$metaDescription.'">';
        $this->assertStringContainsString($expect, $output);
    }
}
