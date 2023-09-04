<?php

namespace App\Controllers;

class PageNotFoundController extends Controller
{
    public function index()
    {
        $this->response->setVars([
            'pageTitle' => 'Cube Shack | Page Not Found',
            'metaDescription' => 'Whoops! We could not find the page your were looking for.',
            'activeLink' => '',
        ]);

        return $this->response;
    }
}
