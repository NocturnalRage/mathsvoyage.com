<?php

namespace App\Repositories;

interface SkillQuestionsRepository
{
    public function create($question, $skill_id);

    public function createOption(
        $skill_question_id,
        $option_text,
        $option_order,
        $correct
    );

    public function createHint($skill_question_id, $hint, $hint_order);

    public function updateImage($skill_question_id, $question_image);
}
