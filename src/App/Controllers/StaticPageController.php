<?php

namespace App\Controllers;

class StaticPageController extends Controller
{
    public function home()
    {
        $this->response->setVars([
            'pageTitle' => 'Cube Shack',
            'metaDescription' => 'A simple PHP framework',
            'activeLink' => 'Home',
        ]);
        $this->addSessionVar('flash');

        return $this->response;
    }
}
