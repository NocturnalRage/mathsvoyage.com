<?php

namespace App\Repositories;

interface SkillQuestionsRepository
{
    public function create($question, $skill_id, $skill_question_type_id);

    public function createOption(
        $skill_question_id,
        $option_text,
        $option_order,
        $correct
    );

    public function createNumber($skill_question_id, $answer);

    public function createHint($skill_question_id, $hint, $hint_order);

    public function updateImage($skill_question_id, $question_image);
}
