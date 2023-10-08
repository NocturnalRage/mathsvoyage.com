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
            'categories' => $skills->getSkillQuestionCategories(),
            'recaptchaKey' => $recaptcha->getRecaptchaKey(),
        ]);
        $this->addSessionVar('errors');
        $this->addSessionVar('formVars');
        $this->addSessionVar('flash');

        return $this->response;
    }

    public function newKasAnswer(SkillsRepository $skills, RecaptchaClient $recaptcha)
    {
        if (! $this->isAdmin()) {
            return $this->redirectTo('/curriculum');
        }

        $this->response->setVars([
            'pageTitle' => 'Add New Kas Answer Question',
            'metaDescription' => 'Add a new Kas answer question',
            'activeLink' => 'Curricula',
            'submitButtonText' => 'Create',
            'skills' => $skills->all(),
            'categories' => $skills->getSkillQuestionCategories(),
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
            'skill_question_category_id' => ['required', 'int'],
            'correctOption' => ['required', 'int'],
            'option1' => ['required', 'max:1000'],
            'option2' => ['required', 'max:1000'],
            'option3' => ['required', 'max:1000'],
            'option4' => ['required', 'max:1000'],
        ]);
        $randomiseOptions = 1;
        if (empty($this->request->post['randomise_options'])) {
          $randomiseOptions = 0;
        }

        $skillQuestionId = $skillQuestions->create(
            $this->request->post['question'],
            $this->request->post['skill_id'],
            1, /* Multiple Choice Question Type */
            $this->request->post['skill_question_category_id'],
            $randomiseOptions
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
                'uploads/skill-questions',
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

        return $this->redirectTo('/curriculum');
    }

    public function createKasAnswer(SkillQuestionsRepository $skillQuestions, RecaptchaClient $recaptcha)
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
            'skill_question_category_id' => ['required', 'int'],
            'answer' => ['required'],
        ]);

        $skillQuestionId = $skillQuestions->create(
            $this->request->post['question'],
            $this->request->post['skill_id'],
            2, /* Numeric Question Type */
            $this->request->post['skill_question_category_id'],
            0 /* randomise_options - not relevant for this question type */
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
                'uploads/skill-questions',
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

        $skillQuestionNumberId = $skillQuestions->createKasAnswer(
            $skillQuestionId,
            $this->request->post['answer'],
            1,
            1
        );

        return $this->redirectTo('/curriculum');
    }

    public function show(SkillQuestionsRepository $skillQuestions)
    {
        if (! $this->isAdmin()) {
            return $this->redirectTo('/curriculum');
        }
        $question = $skillQuestions->find($this->request->get['skillQuestionId']);
        $options = $skillQuestions->findOptions($this->request->get['skillQuestionId']);
        if ($options) {
            $numOptions = count($options);
            for ($i = 0; $i < $numOptions; $i++) {
                if ($options[$i]['option_order'] == 1) {
                    $options[$i]['option_letter'] = 'A';
                } elseif ($options[$i]['option_order'] == 2) {
                    $options[$i]['option_letter'] = 'B';
                } elseif ($options[$i]['option_order'] == 3) {
                    $options[$i]['option_letter'] = 'C';
                } else {
                    $options[$i]['option_letter'] = 'D';
                }
            }
        }

        $this->response->setVars([
            'pageTitle' => 'Show question to create a worked solution',
            'metaDescription' => 'Show question to create a worked solution',
            'activeLink' => 'Curricula',
            'question' => $question,
            'options' => $options,
        ]);

        return $this->response;
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
