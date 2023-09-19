<?php

namespace Tests\src\App\Controllers;

use App\Controllers\StaticPageController;
use Framework\Request;
use Framework\Response;
use Mockery;
use PHPUnit\Framework\TestCase;

class StaticPageControllerTest extends TestCase
{
    protected $users;

    protected $request;

    protected $response;

    protected $controller;

    public function setUp(): void
    {
        $this->users = Mockery::mock('App\Repositories\UserRepository');
        $this->request = new Request();
        $this->response = new Response('views');
        $this->controller = new StaticPageController(
            $this->request,
            $this->response
        );
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testClassCanBeInstantiated()
    {
        $this->assertSame(get_class($this->controller), 'App\Controllers\StaticPageController');
    }

    public function testHome()
    {
        $response = $this->controller->home();
        $responseVars = $response->getVars();
        $expectedPageTitle = 'MathsVoyage.com';
        $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
        $expectedMetaDescription = 'Let us help you along your maths journey';
        $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
    }
}
