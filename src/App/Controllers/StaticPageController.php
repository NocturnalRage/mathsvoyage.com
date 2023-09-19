<?php

namespace App\Controllers;

class StaticPageController extends Controller
{
    public function home()
    {
        $this->response->setVars([
            'pageTitle' => 'MathsVoyage.com',
            'metaDescription' => 'Let us help you along your maths journey',
            'activeLink' => 'Home',
        ]);
        $this->addSessionVar('flash');

        return $this->response;
    }

    public function arithmetic()
    {
        $this->response->setVars([
            'pageTitle' => 'Arithmetic Practice',
            'metaDescription' => 'A fast and efficient way to improve your maths is by improving your mental arithmetic.',
            'activeLink' => 'Arithmetic',
        ]);
        $this->addSessionVar('flash');

        return $this->response;
    }
}
