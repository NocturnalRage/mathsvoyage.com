<?php

namespace App\Controllers;

use App\Repositories\CurriculaRepository;
use App\Repositories\QuizzesRepository;
use App\Repositories\SkillsRepository;
use App\Repositories\TopicsRepository;
use Framework\Recaptcha\RecaptchaClient;

class SkillsController extends Controller
{
    public function index(SkillsRepository $skills)
    {
        if (! $this->isAdmin()) {
            return $this->redirectTo('/curriculum');
        }
        $this->response->setVars([
            'pageTitle' => 'All Skills',
            'metaDescription' => 'All Skills for the for the curricula',
            'activeLink' => 'Skills',
            'skills' => $skills->all(),
        ]);
        $this->addSessionVar('flash');

        return $this->response;
    }

    public function show(
        CurriculaRepository $curricula,
        TopicsRepository $topics,
        SkillsRepository $skills
    ) {
        if (! $this->isAdmin()) {
            return $this->redirectTo('/curriculum');
        }
        [$curriculum, $topic, $skill] = $this->retrieveSkillFromSlug($curricula, $topics, $skills);
        $questions = $skills->findQuestions($skill['skill_id']);
        $this->response->setVars([
            'pageTitle' => 'Skill Question Bank',
            'metaDescription' => 'All Skills for the for the curricula',
            'activeLink' => 'Skills',
            'curriculum' => $curriculum,
            'topic' => $topic,
            'skill' => $skill,
            'questions' => $questions,
        ]);
        $this->addSessionVar('flash');

        return $this->response;
    }

    public function new(TopicsRepository $topics, RecaptchaClient $recaptcha)
    {
        if (! $this->isAdmin()) {
            return $this->redirectTo('/curriculum');
        }

        $this->response->setVars([
            'pageTitle' => 'Add New Skills',
            'metaDescription' => 'Add a new skill',
            'activeLink' => 'Curricula',
            'submitButtonText' => 'Create',
            'topics' => $topics->all(),
            'recaptchaKey' => $recaptcha->getRecaptchaKey(),
        ]);
        $this->addSessionVar('errors');
        $this->addSessionVar('formVars');
        $this->addSessionVar('flash');

        return $this->response;
    }

    public function create(SkillsRepository $skills, RecaptchaClient $recaptcha)
    {
        if (! $this->isAdmin()) {
            return $this->redirectTo('/curriculum');
        }

        if ($this->recaptchaInvalid($recaptcha)) {
            return $this->redirectTo('/curriculum');
        }

        $this->request->validate([
            'title' => ['required', 'max:100'],
            'topic_id' => ['required', 'int'],
            'learning_order' => ['required', 'int'],
        ]);

        $topic_id = $this->request->post['topic_id'];
        $skill = $skills->findByTitleAndTopicId(
            $this->request->post['title'],
            $this->request->post['topic_id']
        );
        if ($skill) {
            $this->request->flash('Skill already exists. Please use a different title.');
            $this->request->session['errors'] = ['title' => 'Title alread exists for this topic'];

            return $this->redirectToSkillForm();
        }
        $slugText = $this->request->post['title'];
        $slugVersion = 0;
        $slug = $this->generateSlug($slugText);
        while ($skills->findBySlugAndTopicId($slug, $topic_id)) {
            $slugVersion++;
            $slugText = $this->request->post['title'].'-'.$slugVersion;
            $slug = $this->generateSlug($slugText);
        }
        $skillId = $skills->create(
            $this->request->post['title'],
            $slug,
            $topic_id,
            $this->request->post['learning_order']
        );

        return $this->redirectTo('/curriculum');
    }

    public function create_quiz(
        CurriculaRepository $curricula,
        TopicsRepository $topics,
        SkillsRepository $skills,
        QuizzesRepository $quizzes
    ) {
        if (! $this->loggedIn()) {
            return $this->redirectTo('/login');
        }
        [$curriculum, $topic, $skill] = $this->retrieveSkillFromSlug($curricula, $topics, $skills);
        $userId = $this->request->user['user_id'];
        $quizId = $this->getOrCreateQuiz($skill['skill_id'], $userId, $quizzes);

        $redirect = '/skills/'.$this->request->post['curriculumSlug'].'/'.$this->request->post['topicSlug'].'/'.$this->request->post['skillSlug'].'/quiz';
        $this->response->redirect($redirect);

        return $this->response;
    }

    public function quiz(
        CurriculaRepository $curricula,
        TopicsRepository $topics,
        SkillsRepository $skills,
        QuizzesRepository $quizzes
    ) {
        if (! $this->loggedIn()) {
            return $this->redirectTo('/login');
        }
        [$curriculum, $topic, $skill] = $this->retrieveSkillFromSlug($curricula, $topics, $skills);
        $userId = $this->request->user['user_id'];
        if ($quizInfo = $quizzes->findIncompleteSkillQuizInfo(
            $skill['skill_id'],
            $userId
        )) {
            $this->response->setVars([
                'pageTitle' => $skill['title'].' Quiz',
                'metaDescription' => 'Take a skill quiz to show what you have learnt.',
                'activeLink' => 'Curricula',
                'curriculum' => $curriculum,
                'topic' => $topic,
                'skill' => $skill,
                'returnSlug' => '/topics/'.$curriculum['curriculum_slug'].'/'.$topic['slug'].'#skill-'.$skill['skill_id'],
                'quizInfo' => $quizInfo,
            ]);

            return $this->response;
        } else {
            $redirect = '/topics/'.$curriculum['curriculum_slug'].'/'.$topic['slug'];
            $this->response->redirect($redirect);

            return $this->response;

            return $this->response;
        }
    }

    public function videos(
        CurriculaRepository $curricula,
        TopicsRepository $topics,
        SkillsRepository $skills
    ) {
        [$curriculum, $topic, $skill] = $this->retrieveSkillFromSlug($curricula, $topics, $skills);
        $videos = $skills->findVideos($skill['skill_id']);
        $this->response->setVars([
            'pageTitle' => $curriculum['curriculum_name'].' - '.$topic['title'].' - '.$skill['title'].' - Videos',
            'metaDescription' => 'Videos for '.$curriculum['curriculum_name'].' - '.$topic['title'].' - '.$skill['title'],
            'activeLink' => 'Curricula',
            'curriculum' => $curriculum,
            'topic' => $topic,
            'skill' => $skill,
            'videos' => $videos,
        ]);

        return $this->response;
    }

    public function worked_solutions(
        CurriculaRepository $curricula,
        TopicsRepository $topics,
        SkillsRepository $skills
    ) {
        [$curriculum, $topic, $skill] = $this->retrieveSkillFromSlug($curricula, $topics, $skills);
        $this->response->setVars([
            'pageTitle' => $curriculum['curriculum_name'].' - '.$topic['title'].' - '.$skill['title'].' - Worked Solutions',
            'metaDescription' => 'Worked Solutions for '.$curriculum['curriculum_name'].' - '.$topic['title'].' - '.$skill['title'],
            'activeLink' => 'Curricula',
            'curriculum' => $curriculum,
            'topic' => $topic,
            'skill' => $skill,
        ]);

        return $this->response;
    }

    public function worksheet(
        CurriculaRepository $curricula,
        TopicsRepository $topics,
        SkillsRepository $skills
    ) {
        if (! $this->isAdmin()) {
            return $this->redirectTo('/curriculum');
        }
        [$curriculum, $topic, $skill] = $this->retrieveSkillFromSlug($curricula, $topics, $skills);

        $this->request->validate([
            'categoryId' => ['required', 'int'],
            'numQuestions' => ['required', 'int'],
        ]);
        $numQuestions = $this->request->get['numQuestions'];
        $categoryId = $this->request->get['categoryId'];
        if ($categoryId == 1) {
            $worksheetType = 'Fluency';
            $questionsAndOptions = $skills->findQuestionsByCategoryId($skill['skill_id'], $categoryId);
        } elseif ($categoryId == 2) {
            $worksheetType = 'Problem Solving';
            $questionsAndOptions = $skills->findQuestionsByCategoryId($skill['skill_id'], $categoryId);
        } elseif ($categoryId == 3) {
            $worksheetType = 'Reasoning';
            $questionsAndOptions = $skills->findQuestionsByCategoryId($skill['skill_id'], $categoryId);
        } else {
            $worksheetType = 'All Question Types';
            $questionsAndOptions = $skills->findAllQuestionsForSkill($skill['skill_id']);
        }
        if (! $questionsAndOptions) {
            $this->request->flash('No questions for this skill and category yet!', 'warning');

            return $this->redirectTo('/skills');
        }
        $questions = [];
        $options = [];
        $currentSkillQuestionId = $questionsAndOptions[0]['skill_question_id'];
        $currentSkillId = $questionsAndOptions[0]['skill_id'];
        $currentSkillQuestionTypeId = $questionsAndOptions[0]['skill_question_type_id'];
        $currentSkillQuestionCategoryId = $questionsAndOptions[0]['skill_question_category_id'];
        $currentQuestion = $questionsAndOptions[0]['question'];
        $currentQuestionImage = $questionsAndOptions[0]['question_image'];
        $currentAnswer = $questionsAndOptions[0]['answer'];
        foreach ($questionsAndOptions as $question) {
            if ($currentSkillQuestionId != $question['skill_question_id']) {
                $questions[] = [
                    'question' => $currentQuestion,
                    'question_image' => $currentQuestionImage,
                    'skill_id' => $currentSkillId,
                    'skill_question_id' => $currentSkillQuestionId,
                    'skill_question_type_id' => $currentSkillQuestionTypeId,
                    'skill_question_category_id' => $currentSkillQuestionCategoryId,
                    'answer' => $currentAnswer,
                    'options' => $options,
                ];
                $options = [];
                $currentSkillQuestionId = $question['skill_question_id'];
                $currentSkillId = $question['skill_id'];
                $currentSkillQuestionTypeId = $question['skill_question_type_id'];
                $currentSkillQuestionCategoryId = $question['skill_question_category_id'];
                $currentQuestion = $question['question'];
                $currentQuestionImage = $question['question_image'];
                $currentAnswer = $question['answer'];
            }
            if ($this->isMultipleChoiceQuestion($currentSkillQuestionTypeId)) {
                $letter = 'A';
                if ($question['option_order'] == 1) {
                    $letter = 'A';
                } elseif ($question['option_order'] == 2) {
                    $letter = 'B';
                } elseif ($question['option_order'] == 3) {
                    $letter = 'C';
                } else {
                    $letter = 'D';
                }
                $options[] = [
                    'skill_question_option_id' => $question['skill_question_option_id'],
                    'option_text' => $question['option_text'],
                    'option_letter' => $letter,
                    'correct' => $question['correct'],
                ];
            }
        }
        $questions[] = [
            'question' => $currentQuestion,
            'question_image' => $currentQuestionImage,
            'skill_id' => $currentSkillId,
            'skill_question_id' => $currentSkillQuestionId,
            'skill_question_type_id' => $currentSkillQuestionTypeId,
            'skill_question_category_id' => $currentSkillQuestionCategoryId,
            'answer' => $currentAnswer,
            'options' => $options,
        ];

        shuffle($questions);
        if ($numQuestions < 1) {
            $numQuestions = 20;
        }
        if ($numQuestions > count($questions)) {
            $numQuestions = count($questions);
        }
        $this->response->setVars([
            'pageTitle' => $skill['title'].' Worksheet - '.$worksheetType,
            'metaDescription' => $skill['title'].' Worksheet - '.$worksheetType,
            'activeLink' => 'Skills',
            'curriculum' => $curriculum,
            'topic' => $topic,
            'skill' => $skill,
            'worksheetType' => $worksheetType,
            'questions' => array_slice($questions, 0, $numQuestions),
        ]);
        $this->addSessionVar('flash');

        return $this->response;
    }

    private function getOrCreateQuiz($skillId, $userId, $quizzes)
    {
        if ($quiz = $quizzes->findIncompleteSkillsQuiz($skillId, $userId)) {
            return $quiz['quiz_id'];
        }
        $questionBank = $quizzes->findQuestionsBySkillId($skillId);
        shuffle($questionBank);
        $totalQuestions = 7;
        $quizId = $quizzes->createSkillsQuiz($skillId, $userId);
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

    private function redirectToSkillForm()
    {
        $this->request->session['formVars'] = $this->request->post;
        $this->response->redirect('/skills/new');

        return $this->response;
    }

    private function isMultipleChoiceQuestion($skillQuestionTypeId)
    {
        return $skillQuestionTypeId == 1 ? true : false;
    }

    private function retrieveSkillFromSlug($curricula, $topics, $skills)
    {
        $curriculum = $curricula->findBySlugOrFail($this->request->request['curriculumSlug']);
        $topic = $topics->findBySlugAndCurriculumIdOrFail(
            $this->request->request['topicSlug'],
            $curriculum['curriculum_id']
        );
        $skill = $skills->findBySlugAndTopicIdOrFail(
            $this->request->request['skillSlug'],
            $topic['topic_id']
        );

        return [$curriculum, $topic, $skill];
    }
}
