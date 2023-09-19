<?php

namespace App\Controllers;

use App\Repositories\SkillQuestionsRepository;
use App\Repositories\SkillsRepository;
use Framework\Recaptcha\RecaptchaClient;

class SkillQuestionsController extends Controller
{
    public function new(SkillsRepository $skills, RecaptchaClient $recaptcha)
    {
        if (! $this->isAdmin()) {
            return $this->redirectTo('/curriculum');
        }

        $this->response->setVars([
            'pageTitle' => 'Add New Skill Question',
            'metaDescription' => 'Add a new skill question',
            'activeLink' => 'Curricula',
            'submitButtonText' => 'Create',
            'skills' => $skills->all(),
            'recaptchaKey' => $recaptcha->getRecaptchaKey(),
        ]);
        $this->addSessionVar('errors');
        $this->addSessionVar('formVars');
        $this->addSessionVar('flash');

        return $this->response;
    }

    public function newNumber(SkillsRepository $skills, RecaptchaClient $recaptcha)
    {
        if (! $this->isAdmin()) {
            return $this->redirectTo('/curriculum');
        }

        $this->response->setVars([
            'pageTitle' => 'Add New Skill Number Question',
            'metaDescription' => 'Add a new skill number question',
            'activeLink' => 'Curricula',
            'submitButtonText' => 'Create',
            'skills' => $skills->all(),
            'recaptchaKey' => $recaptcha->getRecaptchaKey(),
        ]);
        $this->addSessionVar('errors');
        $this->addSessionVar('formVars');
        $this->addSessionVar('flash');

        return $this->response;
    }

    public function create(SkillQuestionsRepository $skillQuestions, RecaptchaClient $recaptcha)
    {
        if (! $this->isAdmin()) {
            return $this->redirectTo('/curriculum');
        }

        if ($this->recaptchaInvalid($recaptcha)) {
            return $this->redirectTo('/curriculum');
        }

        $this->request->validate([
            'question' => ['required', 'max:8000'],
            'skill_id' => ['required', 'int'],
            'correctOption' => ['required', 'int'],
            'option1' => ['required', 'max:1000'],
            'option2' => ['required', 'max:1000'],
            'option3' => ['required', 'max:1000'],
            'option4' => ['required', 'max:1000'],
            'hint1' => ['required', 'max:1000'],
            'hint2' => ['required', 'max:1000'],
            'hint3' => ['required', 'max:1000'],
        ]);

        $skillQuestionId = $skillQuestions->create(
            $this->request->post['question'],
            $this->request->post['skill_id'],
            1
        );

        // Process image
        $questionImageInfo = $this->request->files['question_image'];
        if (! empty($questionImageInfo) && $questionImageInfo['size'] > 0) {
            if (! $this->isSkillQuestionImageValid($questionImageInfo)) {
                $this->request->session['errors'] = $this->errors;
                $this->request->session['formVars'] = $this->request->request;
                $this->request->flash('The image was not valid!', 'danger');

                return $this->redirectTo('/skill-questions/new');
            }
            $questionImage = $this->moveFile(
                'images/skill-questions',
                $questionImageInfo,
                $skillQuestionId
            );
            if ($questionImage) {
                $rowsUpdated = $skillQuestions->updateImage(
                    $skillQuestionId,
                    $questionImage
                );
            }
        }

        $skillQuestionOptionId = $skillQuestions->createOption(
            $skillQuestionId,
            $this->request->post['option1'],
            1,
            $this->request->post['correctOption'] == '1' ? 1 : 0
        );
        $skillQuestionOptionId = $skillQuestions->createOption(
            $skillQuestionId,
            $this->request->post['option2'],
            2,
            $this->request->post['correctOption'] == '2' ? 1 : 0
        );
        $skillQuestionOptionId = $skillQuestions->createOption(
            $skillQuestionId,
            $this->request->post['option3'],
            3,
            $this->request->post['correctOption'] == '3' ? 1 : 0
        );
        $skillQuestionOptionId = $skillQuestions->createOption(
            $skillQuestionId,
            $this->request->post['option4'],
            4,
            $this->request->post['correctOption'] == '4' ? 1 : 0
        );

        $hintId = $skillQuestions->createHint(
            $skillQuestionId,
            $this->request->post['hint1'],
            1
        );
        $hintId = $skillQuestions->createHint(
            $skillQuestionId,
            $this->request->post['hint2'],
            2
        );
        $hintId = $skillQuestions->createHint(
            $skillQuestionId,
            $this->request->post['hint3'],
            3
        );

        return $this->redirectTo('/curriculum');
    }

    public function createNumber(SkillQuestionsRepository $skillQuestions, RecaptchaClient $recaptcha)
    {
        if (! $this->isAdmin()) {
            return $this->redirectTo('/curriculum');
        }

        if ($this->recaptchaInvalid($recaptcha)) {
            return $this->redirectTo('/curriculum');
        }

        $this->request->validate([
            'question' => ['required', 'max:8000'],
            'skill_id' => ['required', 'int'],
            'answer' => ['required', 'float'],
            'hint1' => ['required', 'max:1000'],
            'hint2' => ['required', 'max:1000'],
            'hint3' => ['required', 'max:1000'],
        ]);

        $skillQuestionId = $skillQuestions->create(
            $this->request->post['question'],
            $this->request->post['skill_id'],
            2
        );

        // Process image
        $questionImageInfo = $this->request->files['question_image'];
        if (! empty($questionImageInfo) && $questionImageInfo['size'] > 0) {
            if (! $this->isSkillQuestionImageValid($questionImageInfo)) {
                $this->request->session['errors'] = $this->errors;
                $this->request->session['formVars'] = $this->request->request;
                $this->request->flash('The image was not valid!', 'danger');

                return $this->redirectTo('/skill-questions/new');
            }
            $questionImage = $this->moveFile(
                'images/skill-questions',
                $questionImageInfo,
                $skillQuestionId
            );
            if ($questionImage) {
                $rowsUpdated = $skillQuestions->updateImage(
                    $skillQuestionId,
                    $questionImage
                );
            }
        }

        $skillQuestionNumberId = $skillQuestions->createNumber(
            $skillQuestionId,
            $this->request->post['answer']
        );

        $hintId = $skillQuestions->createHint(
            $skillQuestionId,
            $this->request->post['hint1'],
            1
        );
        $hintId = $skillQuestions->createHint(
            $skillQuestionId,
            $this->request->post['hint2'],
            2
        );
        $hintId = $skillQuestions->createHint(
            $skillQuestionId,
            $this->request->post['hint3'],
            3
        );

        return $this->redirectTo('/curriculum');
    }

    private function isSkillQuestionImageValid($questionImageInfo)
    {
        if ($questionImageInfo['size'] == 0) {
            $this->errors['question_image'] = 'You must select an image for this skill question.';
        }
        if ($questionImageInfo['size'] > 8388608) {
            $this->errors['question_image'] = 'Sorry, the file is larger than 8MB. Please reduce the size and try again.';
        } elseif ($questionImageInfo['type'] != 'image/jpeg' && $questionImageInfo['type'] != 'image/png') {
            $this->errors['question_image'] = 'Could not process an image of type '.$questionImageInfo['type'].'. The image must be a .jpg, or .png';
        }
        if (! empty($this->errors)) {
            return false;
        }

        return true;

    }

    private function moveFile($baseDir, $fileInfo, $skillQuestionId)
    {
        $imageFileType = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);
        $fullFileName = 'question-'.$skillQuestionId.'.'.$imageFileType;
        $uploadFile = $baseDir.'/'.$fullFileName;

        if (! move_uploaded_file($fileInfo['tmp_name'], $uploadFile)) {
            return false;
        }

        return $fullFileName;
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
}
