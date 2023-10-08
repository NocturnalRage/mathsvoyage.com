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

        if (! $quiz = $quizzes->findQuiz($this->request->get['quizId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'options' => json_encode([]),
                'message' => json_encode('You must select a valid quiz to fetch options for.'),
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
        $currentAnswer = $options[0]['answer'];
        $currentRandomise = $options[0]['randomise_options'];
        $currentForm = $options[0]['form'];
        $currentSimplify = $options[0]['simplify'];

        foreach ($options as $option) {
            if ($option['skill_question_id'] != $currentSkillQuestionId) {
                if ($currentRandomise) {
                  shuffle($quizOption);
                }
                $quizOptions[] = [
                    'question' => $currentQuestion,
                    'question_image' => $currentQuestionImage,
                    'skill_question_id' => $currentSkillQuestionId,
                    'skill_question_type_id' => $currentSkillQuestionTypeId,
                    'randomise_options' => $currentRandomise,
                    'answer' => $currentAnswer,
                    'form' => $currentForm,
                    'simplify' => $currentSimplify,
                    'answers' => $quizOption,
                ];
                $quizOption = [];
                $currentQuestion = $option['question'];
                $currentQuestionImage = $option['question_image'];
                $currentSkillQuestionId = $option['skill_question_id'];
                $currentSkillQuestionTypeId = $option['skill_question_type_id'];
                $currentRandomise = $option['randomise_options'];
                $currentAnswer = $option['answer'];
                $currentForm = $option['form'];
                $currentSimplify = $option['simplify'];
            }
            $quizOption[] = [
                'skill_question_option_id' => $option['skill_question_option_id'],
                'option' => $option['option_text'],
                'option_order' => $option['option_order'],
                'correct' => $option['correct'],
            ];
        }
        if ($currentRandomise) {
          shuffle($quizOption);
        }
        $quizOptions[] = [
            'question' => $currentQuestion,
            'question_image' => $currentQuestionImage,
            'skill_question_id' => $currentSkillQuestionId,
            'skill_question_type_id' => $currentSkillQuestionTypeId,
            'answer' => $currentAnswer,
            'form' => $currentForm,
            'simplify' => $currentSimplify,
            'answers' => $quizOption,
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

        if (! $quiz = $quizzes->findQuiz($this->request->post['quizId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must select a valid quiz to record a response for.'),
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

        if (! isset($this->request->post['skillQuestionTypeId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must provide the question type to record the response.'),
            ]);

            return $this->response;
        }

        if ($this->request->post['skillQuestionTypeId'] == 1 && ! isset($this->request->post['skillQuestionOptionId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must provide an option for a multiple choice question.'),
            ]);

            return $this->response;
        }
        if ($this->request->post['skillQuestionTypeId'] == 2 && ! isset($this->request->post['answer'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must provide a numeric answer for a numeric question.'),
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

        if ($this->request->user['user_id'] != $quiz['user_id']) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must select your own quiz to record a response.'),
            ]);

            return $this->response;
        }

        if ($this->request->post['skillQuestionTypeId'] == 1) {
            if (! $quizzes->updateQuizMultipleChoiceQuestion(
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
        } elseif ($this->request->post['skillQuestionTypeId'] == 2) {
            if (! $quizzes->updateQuizNumericQuestion(
                $this->request->post['quizId'],
                $this->request->post['skillQuestionId'],
                $this->request->post['answer'],
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
            $topicQuizType = 2;
            if ($percent >= 1) {
                if ($previousMastery == 4) {
                    $mastery = 4;
                    $mastery_desc = 'Mastered';
                } elseif ($previousMastery == 3 && $quizTypeId >= $topicQuizType) {
                    $mastery = 4;
                    $mastery_desc = 'Mastered';
                } else {
                    $mastery = 3;
                    $mastery_desc = 'Proficent';
                }
            } elseif ($percent >= 0.7) {
                if ($previousMastery == 4) {
                    $mastery = 3;
                    $mastery_desc = 'Proficent';
                } else {
                    $mastery = 2;
                    $mastery_desc = 'Familiar';
                }
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
