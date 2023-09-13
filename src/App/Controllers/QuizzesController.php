<?php

namespace App\Controllers;

use App\Repositories\QuizzesRepository;

class QuizzesController extends Controller
{
    public function fetchQuiz(QuizzesRepository $quizzes)
    {
        $this->response->setView('Quizzes/options.json.php');
        $this->response->header('Content-Type: application/json');

        if (! $this->loggedIn()) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'options' => json_encode([]),
                'message' => json_encode('You must be logged in to fetch your quiz options.'),
            ]);

            return $this->response;
        }

        if (! isset($this->request->get['quizId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'options' => json_encode([]),
                'message' => json_encode('You must select a quiz to fetch options for.'),
            ]);

            return $this->response;
        }

        if (! $options = $quizzes->getQuizOptions($this->request->get['quizId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'options' => json_encode([]),
                'message' => json_encode('You must select a valid quiz to fetch options for.'),
            ]);

            return $this->response;
        }

        if ($this->request->user['user_id'] != $options[0]['user_id']) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'options' => json_encode([]),
                'message' => json_encode('You must select your own quiz.'),
            ]);

            return $this->response;
        }

        $answers = [];
        $quizOption = [];
        $currentQuestion = $options[0]['question'];
        $currentQuestionImage = $options[0]['question_image'];
        $currentSkillQuestionId = $options[0]['skill_question_id'];
        $currentSkillQuestionTypeId = $options[0]['skill_question_type_id'];

        foreach ($options as $option) {
            if ($option['skill_question_id'] != $currentSkillQuestionId) {
                $hints = $quizzes->getSkillQuestionHints(
                    $currentSkillQuestionId
                );
                $quizOptions[] = [
                    'question' => $currentQuestion,
                    'question_image' => $currentQuestionImage,
                    'skill_question_id' => $currentSkillQuestionId,
                    'skill_question_type_id' => $currentSkillQuestionTypeId,
                    'answers' => $quizOption,
                    'hints' => $hints,
                ];
                $quizOption = [];
                $currentQuestion = $option['question'];
                $currentQuestionImage = $option['question_image'];
                $currentSkillQuestionId = $option['skill_question_id'];
                $currentSkillQuestionTypeId = $option['skill_question_type_id'];
            }
            $quizOption[] = [
                'skill_question_option_id' => $option['skill_question_option_id'],
                'option' => $option['option_text'],
                'option_order' => $option['option_order'],
                'correct' => $option['correct'],
            ];
        }
        $hints = $quizzes->getSkillQuestionHints(
            $currentSkillQuestionId
        );
        $quizOptions[] = [
            'question' => $currentQuestion,
            'question_image' => $currentQuestionImage,
            'skill_question_id' => $currentSkillQuestionId,
            'skill_question_type_id' => $currentSkillQuestionTypeId,
            'answers' => $quizOption,
            'hints' => $hints,
        ];

        // Give questions in random order
        shuffle($quizOptions);
        $this->response->setVars([
            'status' => json_encode('success'),
            'options' => json_encode($quizOptions),
            'message' => json_encode('Questions retrieved'),
        ]);

        return $this->response;
    }

    public function recordResponse(QuizzesRepository $quizzes)
    {
        $this->response->setView('Quizzes/record.json.php');
        $this->response->header('Content-Type: application/json');

        if (! $this->loggedIn()) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must be logged in to record a response.'),
            ]);

            return $this->response;
        }

        if (! isset($this->request->post['quizId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must select a quiz to record a response for.'),
            ]);

            return $this->response;
        }

        if (! isset($this->request->post['skillQuestionId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must select a quiz question to record a response for.'),
            ]);

            return $this->response;
        }
        if (! isset($this->request->post['skillQuestionOptionId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must select a response to record it.'),
            ]);

            return $this->response;
        }
        if (! isset($this->request->post['correctUnaided'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('Did not receive the correct or incorrect flag.'),
            ]);

            return $this->response;
        }
        if (! isset($this->request->post['questionStartTime'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('Did not receive the start time for the question.'),
            ]);

            return $this->response;
        }
        if (! isset($this->request->post['questionEndTime'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('Did not receive the end time for the question.'),
            ]);

            return $this->response;
        }

        if (! $options = $quizzes->getQuizOptions($this->request->post['quizId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must select a valid quiz to record a response for.'),
            ]);

            return $this->response;
        }

        if ($this->request->user['user_id'] != $options[0]['user_id']) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must select your own quiz to record a response.'),
            ]);

            return $this->response;
        }

        if (! $quizzes->updateQuizQuestion(
            $this->request->post['quizId'],
            $this->request->post['skillQuestionId'],
            $this->request->post['skillQuestionOptionId'],
            $this->request->post['correctUnaided'],
            $this->request->post['questionStartTime'],
            $this->request->post['questionEndTime']
        )
        ) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('Could not record response.'),
            ]);

            return $this->response;
        }

        $this->response->setVars([
            'status' => json_encode('success'),
            'message' => json_encode('Response recorded'),
        ]);

        return $this->response;
    }

    public function recordCompletion(QuizzesRepository $quizzes)
    {
        $this->response->setView('Quizzes/complete.json.php');
        $this->response->header('Content-Type: application/json');

        if (! $this->loggedIn()) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'skillsMatrix' => json_encode([]),
                'message' => json_encode('You must be logged in to complete a quiz.'),
            ]);

            return $this->response;
        }

        if (! isset($this->request->post['quizId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'skillsMatrix' => json_encode([]),
                'message' => json_encode('You must select a quiz to complete.'),
            ]);

            return $this->response;
        }

        if (! isset($this->request->post['quizStartTime'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'skillsMatrix' => json_encode([]),
                'message' => json_encode('Did not receive the start time for the quiz.'),
            ]);

            return $this->response;
        }
        if (! isset($this->request->post['quizEndTime'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'skillsMatrix' => json_encode([]),
                'message' => json_encode('Did not receive the end time for the quiz.'),
            ]);

            return $this->response;
        }

        if (! $options = $quizzes->getQuizOptions($this->request->post['quizId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'skillsMatrix' => json_encode([]),
                'message' => json_encode('You must select a valid quiz to complete.'),
            ]);

            return $this->response;
        }

        if ($this->request->user['user_id'] != $options[0]['user_id']) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'skillsMatrix' => json_encode([]),
                'message' => json_encode('You must select your own quiz to complete.'),
            ]);

            return $this->response;
        }

        if (! $quizzes->updateQuiz(
            $this->request->post['quizId'],
            $this->request->post['quizStartTime'],
            $this->request->post['quizEndTime']
        )
        ) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'skillsMatrix' => json_encode([]),
                'message' => json_encode('Could not record response.'),
            ]);

            return $this->response;
        }
        // Update Skills
        if (! $resultsSkillsMatrix = $quizzes->getQuizResultsAndSkillMastery($this->request->post['quizId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'skillsMatrix' => json_encode([]),
                'message' => json_encode('Could not retrieve skills.'),
            ]);

            return $this->response;
        }
        $skillsMatrix = [];
        foreach ($resultsSkillsMatrix as $result) {
            $result['mastery_level_id'] = (int) $result['mastery_level_id'];
            $result['correct'] = (int) $result['correct'];
            $result['total'] = (int) $result['total'];
            $previousMastery = (int) $result['mastery_level_id'];
            $percent = $result['correct'] / $result['total'];
            $quizTypeId = $result['quiz_type_id'];
            $skillQuizType = 1;
            $tutorialQuizType = 2;
            if ($percent >= 1) {
                if ($previousMastery == 4) {
                    $mastery = 4;
                    $mastery_desc = 'Mastered';
                } elseif ($previousMastery == 3 && $quizTypeId == $tutorialQuizType) {
                    $mastery = 4;
                    $mastery_desc = 'Mastered';
                } else {
                    $mastery = 3;
                    $mastery_desc = 'Proficent';
                }
            } elseif ($percent >= 0.7) {
                $mastery = 2;
                $mastery_desc = 'Familiar';
            } else {
                $mastery = 1;
                $mastery_desc = 'Attempted';
            }
            $result['new_mastery_level_id'] = $mastery;
            $result['new_mastery_level_desc'] = $mastery_desc;
            $skillsMatrix[] = $result;

            $quizzes->insertOrUpdateSkillMastery(
                $result['skill_id'],
                $this->request->user['user_id'],
                $mastery
            );
        }

        $this->response->setVars([
            'status' => json_encode('success'),
            'skillsMatrix' => json_encode($skillsMatrix),
            'message' => json_encode('Completion recorded'),
        ]);

        return $this->response;
    }
}
