<?php

namespace App\Controllers;

use App\Repositories\CurriculaRepository;
use App\Repositories\QuizzesRepository;
use App\Repositories\TopicsRepository;
use Framework\Recaptcha\RecaptchaClient;

class TopicsController extends Controller
{
    public function index(TopicsRepository $topics)
    {
        if (! $this->isAdmin()) {
            return $this->redirectTo('/curriculum');
        }
        $this->response->setVars([
            'pageTitle' => 'All Topics',
            'metaDescription' => 'All Topics for the for the curricula',
            'activeLink' => 'Topics',
            'topics' => $topics->all(),
        ]);
        $this->addSessionVar('flash');

        return $this->response;
    }

    public function show(CurriculaRepository $curricula, TopicsRepository $topics)
    {
        $curriculum = $curricula->findBySlugOrFail($this->request->get['curriculumSlug']);
        $topic = $topics->findBySlugAndCurriculumIdOrFail(
            $this->request->get['topicSlug'],
            $curriculum['curriculum_id']
        );
        $this->response->setVars([
            'pageTitle' => $topic['title'],
            'metaDescription' => 'Topics for the '.$curriculum['curriculum_name'].' curriculum',
            'activeLink' => 'Curricula',
            'curriculum' => $curriculum,
            'topic' => $topic,
            'skills' => $topics->findSkillsAndMasteryLevels(
                $topic['topic_id'],
                $this->request->user['user_id'] ?? 0
            ),
        ]);
        $this->addSessionVar('flash');

        return $this->response;
    }

    public function new(CurriculaRepository $cr, RecaptchaClient $recaptcha)
    {
        if (! $this->isAdmin()) {
            return $this->redirectTo('/curriculum');
        }

        $this->response->setVars([
            'pageTitle' => 'Add New Topic',
            'metaDescription' => 'Add a new topic',
            'activeLink' => 'Curricula',
            'submitButtonText' => 'Create',
            'curricula' => $cr->all(),
            'recaptchaKey' => $recaptcha->getRecaptchaKey(),
        ]);
        $this->addSessionVar('errors');
        $this->addSessionVar('formVars');
        $this->addSessionVar('flash');

        return $this->response;
    }

    public function create(TopicsRepository $topics, RecaptchaClient $recaptcha)
    {
        if (! $this->isAdmin()) {
            return $this->redirectTo('/curriculum');
        }

        if ($this->recaptchaInvalid($recaptcha)) {
            return $this->redirectTo('/curriculum');
        }

        $this->request->validate([
            'title' => ['required', 'max:100'],
            'curriculum_id' => ['required', 'int'],
            'learning_order' => ['required', 'int'],
        ]);

        $curriculum_id = $this->request->post['curriculum_id'];
        $topic = $topics->findByTitleAndCurriculumId(
            $this->request->post['title'],
            $this->request->post['curriculum_id']
        );
        if ($topic) {
            $this->request->flash('Topic already exists. Please use a different title.');
            $this->request->session['errors'] = ['title' => 'Title alread exists for this curriculum'];

            return $this->redirectToTopicForm();
        }
        $slugText = $this->request->post['title'];
        $slugVersion = 0;
        $slug = $this->generateSlug($slugText);
        while ($topics->findBySlugAndCurriculumId($slug, $curriculum_id)) {
            $slugVersion++;
            $slugText = $this->request->post['title'].'-'.$slugVersion;
            $slug = $this->generateSlug($slugText);
        }
        $topicId = $topics->create(
            $this->request->post['title'],
            $slug,
            $curriculum_id,
            $this->request->post['learning_order']
        );

        return $this->redirectTo('/curriculum');
    }

    public function create_quiz(CurriculaRepository $curricula, TopicsRepository $topics, QuizzesRepository $quizzes)
    {
        if (! $this->loggedIn()) {
            return $this->redirectToLoginPage();
        }

        $curriculum = $curricula->findBySlugOrFail($this->request->post['curriculumSlug']);
        $topic = $topics->findBySlugAndCurriculumId(
            $this->request->post['topicSlug'],
            $curriculum['curriculum_id']
        );

        $quizId = $this->getOrCreateQuiz($topic['topic_id'], $this->request->user['user_id'], $quizzes);

        $redirect = '/topics/'.$this->request->post['curriculumSlug'].'/'.$this->request->post['topicSlug'].'/quiz';
        $this->response->redirect($redirect);

        return $this->response;
    }

    public function quiz(
        CurriculaRepository $curricula,
        TopicsRepository $topics,
        QuizzesRepository $quizzes
    ) {
        if (! $this->loggedIn()) {
            return $this->redirectTo('/login');
        }
        $curriculum = $curricula->findBySlugOrFail($this->request->get['curriculumSlug']);
        $topic = $topics->findBySlugAndCurriculumIdOrFail(
            $this->request->get['topicSlug'],
            $curriculum['curriculum_id']
        );
        $userId = $this->request->user['user_id'];
        if ($quizInfo = $quizzes->findIncompleteTopicQuizInfo(
            $topic['topic_id'],
            $userId
        )) {
            $this->response->setView('Skills/quiz.html.php');
            $this->response->setVars([
                'pageTitle' => $topic['title'].' Quiz',
                'metaDescription' => 'Take a topic quiz to show what you have learnt.',
                'activeLink' => 'Curricula',
                'curriculum' => $curriculum,
                'topic' => $topic,
                'quizInfo' => $quizInfo,
            ]);

            return $this->response;
        } else {
            $redirect = '/topics/'.$curriculum['curriculum_slug'].'/'.$topic['slug'];
            $this->response->redirect($redirect);

            return $this->response;
        }
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

    private function getOrCreateQuiz($topicId, $userId, $quizzes)
    {
        if ($quiz = $quizzes->findIncompleteTopicQuiz($topicId, $userId)) {
            return $quiz['quiz_id'];
        }
        $questionBank = $quizzes->getAllTopicQuestions(
            $topicId
        );
        shuffle($questionBank);
        $totalQuestions = 20;
        $quizId = $quizzes->createTopicQuiz($topicId, $userId);
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

    private function redirectToTopicForm()
    {
        $this->request->session['formVars'] = $this->request->post;
        $this->response->redirect('/topics/new');

        return $this->response;
    }
}
