<?php

namespace Tests\views\StaticPage;

use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class IndexHtmlTest extends BasicViewHtmlTestCase
{
    public function testIndex()
    {
        $pageTitle = 'Page Not Found';
        $metaDescription = 'We could not find your page';
        $activeLink = 'Home';
        $response = new Response('views');
        $response->setView('PageNotFound/index.html.php');
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
