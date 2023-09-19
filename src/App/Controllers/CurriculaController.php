<?php

namespace App\Controllers;

use App\Repositories\CurriculaRepository;
use App\Repositories\QuizzesRepository;
use Framework\Recaptcha\RecaptchaClient;

class CurriculaController extends Controller
{
    public function index(CurriculaRepository $curricula)
    {
        $this->response->setVars([
            'pageTitle' => 'Curricula',
            'metaDescription' => 'A list of all the courses on offer',
            'activeLink' => 'Curricula',
            'curricula' => $curricula->all(),
        ]);
        $this->addSessionVar('flash');

        return $this->response;
    }

    public function show(CurriculaRepository $curricula)
    {
        $c = $curricula->findBySlugOrFail($this->request->get['slug']);
        $topics = $curricula->findTopics(
            $c['curriculum_id'],
            $this->request->user['user_id']
        );
        $this->response->setVars([
            'pageTitle' => $c['curriculum_name'],
            'metaDescription' => 'Topics for the '.$c['curriculum_name'].' curriculum',
            'activeLink' => 'Curricula',
            'curriculum' => $c,
            'topics' => $topics,
        ]);
        $this->addSessionVar('flash');

        return $this->response;
    }

    public function new(RecaptchaClient $recaptcha)
    {
        if (! $this->isAdmin()) {
            return $this->redirectTo('/curriculum');
        }

        $this->response->setVars([
            'pageTitle' => 'Add New Curriculum',
            'metaDescription' => 'Add a new curriculum',
            'activeLink' => 'Curricula',
            'submitButtonText' => 'Create',
            'recaptchaKey' => $recaptcha->getRecaptchaKey(),
        ]);
        $this->addSessionVar('errors');
        $this->addSessionVar('formVars');
        $this->addSessionVar('flash');

        return $this->response;
    }

    public function edit(
        CurriculaRepository $curricula,
        RecaptchaClient $recaptcha
    ) {
        if (! $this->isAdmin()) {
            return $this->redirectTo('/curriculum');
        }

        $curriculum = $curricula->findBySlugOrFail($this->request->get['slug']);
        $formVars = $this->request->session['formVars'] ?? $curriculum;
        unset($this->request->session['formVars']);

        $this->response->setVars([
            'pageTitle' => 'Edit Curriculum',
            'metaDescription' => 'Edit curriculum',
            'activeLink' => 'Curricula',
            'curriculum' => $curriculum,
            'formVars' => $formVars,
            'submitButtonText' => 'Update',
            'recaptchaKey' => $recaptcha->getRecaptchaKey(),
        ]);
        $this->addSessionVar('errors');
        $this->addSessionVar('flash');

        return $this->response;
    }

    public function create(
        CurriculaRepository $curricula,
        RecaptchaClient $recaptcha
    ) {
        if (! $this->isAdmin()) {
            return $this->redirectTo('/curriculum');
        }

        if ($this->recaptchaInvalid($recaptcha)) {
            return $this->redirectTo('/curriculum');
        }

        $this->request->validate([
            'curriculum_name' => ['required', 'max:25'],
            'display_order' => ['required', 'int'],
        ]);

        $c = $curricula->findByName($this->request->post['curriculum_name']);
        if ($c) {
            $this->request->flash('Curriculum already exists. Please use a different curriculum name.');
            $this->request->session['errors'] = ['curriculum_name' => 'Curriculum alread exists'];

            return $this->redirectToCurriculumForm();
        }
        $slugText = $this->request->post['curriculum_name'];
        $slugVersion = 0;
        $slug = $this->generateSlug($slugText);
        while ($curricula->findBySlug($slug)) {
            $slugVersion++;
            $slugText = $this->request->post['curriculum_name'].'-'.$slugVersion;
            $slug = $this->generateSlug($slugText);
        }
        $curriculumId = $curricula->create(
            $this->request->post['curriculum_name'],
            $slug,
            $this->request->post['display_order']
        );

        return $this->redirectTo('/curriculum');
    }

    public function update(
        CurriculaRepository $curricula,
        RecaptchaClient $recaptcha
    ) {
        if (! $this->isAdmin()) {
            return $this->redirectTo('/curriculum');
        }

        if ($this->recaptchaInvalid($recaptcha)) {
            return $this->redirectTo('/curriculum');
        }

        $this->request->validate([
            'curriculum_name' => ['required', 'max:25'],
            'display_order' => ['required', 'int'],
        ]);

        $rowsUpdated = $curricula->updateBySlug(
            $this->request->post['curriculum_name'],
            $this->request->post['slug'],
            $this->request->post['display_order']
        );
        if ($rowsUpdated == 0) {
            $this->request->flash('Could not update the curriculum', 'danger');
        }

        return $this->redirectTo('/curriculum/'.$this->request->post['slug']);
    }

    public function create_quiz(CurriculaRepository $curricula, QuizzesRepository $quizzes)
    {
        if (! $this->loggedIn()) {
            return $this->redirectToLoginPage();
        }

        $curriculum = $curricula->findBySlugOrFail($this->request->post['curriculumSlug']);
        $quizId = $this->getOrCreateQuiz($curriculum['curriculum_id'], $this->request->user['user_id'], $quizzes);

        $redirect = '/curriculum/'.$this->request->post['curriculumSlug'].'/quiz';
        $this->response->redirect($redirect);

        return $this->response;
    }

    public function quiz(
        CurriculaRepository $curricula,
        QuizzesRepository $quizzes
    ) {
        if (! $this->loggedIn()) {
            return $this->redirectTo('/login');
        }
        $curriculum = $curricula->findBySlugOrFail($this->request->get['curriculumSlug']);
        $userId = $this->request->user['user_id'];
        if ($quizInfo = $quizzes->findIncompleteCurriculumQuizInfo(
            $curriculum['curriculum_id'],
            $userId
        )) {
            $this->response->setView('Skills/quiz.html.php');
            $this->response->setVars([
                'pageTitle' => $curriculum['curriculum_name'].' Quiz',
                'metaDescription' => 'Take a curriculum quiz to show what you have learnt.',
                'activeLink' => 'Curricula',
                'curriculum' => $curriculum,
                'returnSlug' => '/curriculum',
                'quizInfo' => $quizInfo,
            ]);

            return $this->response;
        } else {
            $redirect = '/curriculum';
            $this->response->redirect($redirect);

            return $this->response;
        }
    }

    private function getOrCreateQuiz($curriculumId, $userId, $quizzes)
    {
        if ($quiz = $quizzes->findIncompleteCurriculumQuiz($curriculumId, $userId)) {
            return $quiz['quiz_id'];
        }
        $questionBank = $quizzes->getAllCurriculumQuestions($curriculumId);
        shuffle($questionBank);
        $totalQuestions = 20;
        $quizId = $quizzes->createCurriculumQuiz($curriculumId, $userId);
        if ($totalQuestions > count($questionBank)) {
            $totalQuestions = count($questionBank);
        }
        for ($i = 0; $i < $totalQuestions; $i++) {
            $quizzes->createQuizQuestion(
                $quizId,
                $questionBank[$i]['skill_question_id']
            );
        }

        return $quizId;
    }

    private function recaptchaInvalid($recaptcha)
    {
        if (empty($this->request->post['g-recaptcha-response'])) {
            $this->request->flash('No validation provided. Please ensure you are human!', 'danger');

            return true;
        }
        $expectedRecaptchaAction = 'loginwithversion3';
        if (! $recaptcha->verified(
            $this->request->server['SERVER_NAME'],
            $expectedRecaptchaAction,
            $this->request->post['g-recaptcha-response'],
            $this->request->server['REMOTE_ADDR']
        )) {
            $this->request->flash('Could not validate your request. Please ensure you are human!', 'danger');

            return true;
        }

        return false;
    }

    private function redirectToCurriculumForm()
    {
        $this->request->session['formVars'] = $this->request->post;
        $this->response->redirect('/curriculum/new');

        return $this->response;
    }
}
