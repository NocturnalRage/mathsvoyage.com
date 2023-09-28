<?php

namespace App\Controllers;

use App\Repositories\UserRepository;

class StaticPageController extends Controller
{
    public function home(UserRepository $users)
    {
        if ($this->loggedIn()) {
            $this->response->setView('StaticPage/home-logged-in.html.php');
            $quizResults = $users->getQuizResultsSummary($this->request->user['user_id']);
            $ttResults = $users->getTimesTablesResultsSummary($this->request->user['user_id']);
            $gaResults = $users->getGeneralArithmeticResultsSummary($this->request->user['user_id']);
            $this->response->setVars([
                'pageTitle' => 'MathsVoyage.com',
                'metaDescription' => 'Let us help you along your maths journey',
                'activeLink' => 'Home',
                'quizResults' => $quizResults,
                'ttResults' => $ttResults,
                'gaResults' => $gaResults,
            ]);
            $this->addSessionVar('flash');

            return $this->response;
        } else {
            $this->response->setVars([
                'pageTitle' => 'MathsVoyage.com',
                'metaDescription' => 'Let us help you along your maths journey',
                'activeLink' => 'Home',
            ]);
            $this->addSessionVar('flash');

            return $this->response;
        }
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
