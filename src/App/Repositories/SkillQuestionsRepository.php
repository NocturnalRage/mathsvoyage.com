<?php

namespace App\Repositories;

interface SkillQuestionsRepository
{
    public function find($skill_question_id);

    public function findOrFail($skill_question_id);

    public function findOptions($skill_question_id);

    public function create($question, $skill_id, $skill_question_type_id, $skill_question_category_id, $randomise_options);

    public function createOption(
        $skill_question_id,
        $option_text,
        $option_order,
        $correct
    );

    public function createKasAnswer($skill_question_id, $answer, $form, $simplify);

    public function createNumericAnswer($skill_question_id, $numeric_type_id, $answer, $simplify);

    public function updateImage($skill_question_id, $question_image);

    public function createHint($skill_question_id, $hint, $hintOrder);
}
