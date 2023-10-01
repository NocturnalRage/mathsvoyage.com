<?php

namespace App\Controllers;

use App\Repositories\SkillsRepository;

class DoNowController extends Controller
{
    public function show(SkillsRepository $skills)
    {
        if (! $this->isAdmin()) {
            return $this->redirectTo('/curriculum');
        }

        $skill = $skills->findOrFail($this->request->request['skillId']);

        if (empty($this->request->get['doNowState'])) {
            return $this->redirectTo('/curriculum');
        }
        $doNowState = $this->request->get['doNowState'];
        if ($doNowState == 'current') {
            $questions = $skills->getCurrentWorkedSolutions($skill['skill_id']);
        } elseif ($doNowState == 'topic') {
            $questions = $skills->getTopicWorkedSolutions($skill['skill_id']);
        } elseif ($doNowState == 'curriculum') {
            $questions = $skills->getCurriculumWorkedSolutions($skill['skill_id']);
        } else {
            return $this->redirectTo('/curriculum');
        }
        shuffle($questions);
        $this->response->setVars([
            'pageTitle' => 'Generate Do Now Questions',
            'metaDescription' => 'Generate Do Now Questions',
            'activeLink' => 'Skills',
            'questions' => $questions,
        ]);

        return $this->response;
    }
}
