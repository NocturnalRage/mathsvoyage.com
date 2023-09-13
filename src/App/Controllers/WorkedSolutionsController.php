<?php

namespace App\Controllers;

use App\Repositories\SkillsRepository;
use App\Repositories\WorkedSolutionsRepository;
use Framework\Recaptcha\RecaptchaClient;

class WorkedSolutionsController extends Controller
{
    public function new(SkillsRepository $skills, RecaptchaClient $recaptcha)
    {
        if (! $this->isAdmin()) {
            return $this->redirectTo('/curriculum');
        }

        $this->response->setVars([
            'pageTitle' => 'Add New Worked Solution',
            'metaDescription' => 'Add a new worked solution.',
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

    public function create(WorkedSolutionsRepository $workedSolutions, RecaptchaClient $recaptcha)
    {
        if (! $this->isAdmin()) {
            return $this->redirectTo('/curriculum');
        }

        if ($this->recaptchaInvalid($recaptcha)) {
            return $this->redirectTo('/curriculum');
        }

        $this->request->validate(['skill_id' => ['required', 'int']]);

        // Process image
        $questionImageInfo = $this->request->files['question'];
        $answerImageInfo = $this->request->files['answer'];
        if (! $this->isImageValid($questionImageInfo, 'question')) {
            $this->request->session['errors'] = $this->errors;
            $this->request->session['formVars'] = $this->request->request;
            $this->request->flash('The image was not valid!', 'danger');

            return $this->redirectTo('/worked-solutions/new');
        }
        if (! $this->isImageValid($answerImageInfo, 'answer')) {
            $this->request->session['errors'] = $this->errors;
            $this->request->session['formVars'] = $this->request->request;
            $this->request->flash('The image was not valid!', 'danger');

            return $this->redirectTo('/worked-solutions/new');
        }
        $workedSolutionId = $workedSolutions->create($this->request->post['skill_id']);
        $questionImage = $this->moveFile(
            'images/worked-solutions',
            $questionImageInfo,
            $workedSolutionId,
            'question'
        );
        $answerImage = $this->moveFile(
            'images/worked-solutions',
            $answerImageInfo,
            $workedSolutionId,
            'answer'
        );
        if ($questionImage && $answerImage) {
            $rowsUpdated = $workedSolutions->update(
                $workedSolutionId,
                $questionImage,
                $answerImage
            );
        }

        return $this->redirectTo('/curriculum');
    }

    public function fetch(SkillsRepository $skills)
    {
        $this->response->setView('WorkedSolutions/worked_solutions.json.php');
        $this->response->header('Content-Type: application/json');

        if (! isset($this->request->get['skillId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'workedSolutions' => json_encode([]),
                'message' => json_encode('You must select a skill to fetch worked examples for.'),
            ]);

            return $this->response;
        }

        if (! $workedSolutions = $skills->findWorkedSolutions($this->request->get['skillId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'workedSolutions' => json_encode([]),
                'message' => json_encode('You must select a valid skill to fetch worked examples for.'),
            ]);

            return $this->response;
        }
        shuffle($workedSolutions);
        $this->response->setVars([
            'status' => json_encode('success'),
            'workedSolutions' => json_encode($workedSolutions),
            'message' => json_encode('Worked solutions retrieved'),
        ]);

        return $this->response;
    }

    private function isImageValid($imageInfo, $q_or_a)
    {
        $sizeInfo = getimagesize($imageInfo['tmp_name']);

        $imageWidth = $sizeInfo[0];
        $imageHeight = $sizeInfo[1];
        if ($imageWidth != 640 || $imageHeight != 360) {
            $this->errors[$q_or_a] = 'The image must be 640x360 pixels.';
        }
        if ($imageInfo['size'] == 0) {
            $this->errors[$q_or_a] = 'You must select an image for this worked solution.';
        }
        if ($imageInfo['size'] > 8388608) {
            $this->errors[$q_or_a] = 'Sorry, the file is larger than 8MB. Please reduce the size and try again.';
        } elseif ($imageInfo['type'] != 'image/jpeg' && $imageInfo['type'] != 'image/png') {
            $this->errors[$q_or_a] = 'Could not process an image of type '.$imageInfo['type'].'. The image must be a .jpg, or .png';
        }
        if (! empty($this->errors)) {
            return false;
        }

        return true;

    }

    private function moveFile($baseDir, $fileInfo, $workedSolutionId, $q_or_a)
    {
        $imageFileType = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);
        $fullFileName = $q_or_a.'-'.$workedSolutionId.'.'.$imageFileType;
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
