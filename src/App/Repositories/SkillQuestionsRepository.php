<?php

namespace App\Repositories;

interface SkillQuestionsRepository
{
    public function find($skill_question_id);

    public function findOrFail($skill_question_id);

    public function findOptions($skill_question_id);

    public function create($question, $skill_id, $skill_question_type_id, $skill_question_category_id);

    public function createOption(
        $skill_question_id,
        $option_text,
        $option_order,
        $correct
    );

    public function createNumber($skill_question_id, $answer);

    public function updateImage($skill_question_id, $question_image);
}
