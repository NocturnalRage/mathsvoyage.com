<?php

namespace App\Controllers;

use App\Repositories\SkillsRepository;
use App\Repositories\VideosRepository;
use Framework\Recaptcha\RecaptchaClient;

class VideosController extends Controller
{
    public function new(SkillsRepository $skills, RecaptchaClient $recaptcha)
    {
        if (! $this->isAdmin()) {
            return $this->redirectTo('/curriculum');
        }

        $this->response->setVars([
            'pageTitle' => 'Add New Video',
            'metaDescription' => 'Add a new video.',
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

    public function create(VideosRepository $videos, RecaptchaClient $recaptcha)
    {
        if (! $this->isAdmin()) {
            return $this->redirectTo('/curriculum');
        }

        if ($this->recaptchaInvalid($recaptcha)) {
            return $this->redirectTo('/curriculum');
        }

        $this->request->validate(
            [
                'skill_id' => ['required', 'int'],
                'youtube_id' => ['required', 'max:100'],
            ]
        );

        $videoId = $videos->create(
            $this->request->post['skill_id'],
            $this->request->post['youtube_id']
        );

        return $this->redirectTo('/curriculum');
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
