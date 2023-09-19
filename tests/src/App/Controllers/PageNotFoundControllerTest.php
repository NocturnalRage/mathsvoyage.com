<?php

namespace Tests\src\App\Controllers;

use App\Controllers\PageNotFoundController;
use Framework\Request;
use Framework\Response;
use Mockery;
use PHPUnit\Framework\TestCase;

class PageNotFoundControllerTest extends TestCase
{
    protected $request;

    protected $response;

    protected $controller;

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testPageNotFoundController()
    {
        $request = new Request();
        $response = new Response('views');
        $controller = new PageNotFoundController(
            $request,
            $response
        );
        $this->response = $controller->index();
        $responseVars = $this->response->getVars();
        $expectedPageTitle = 'MathsVoyage.com | Page Not Found';
        $expectedMetaDescription = 'Whoops! We could not find the page your were looking for.';
        $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
        $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
    }
}
