<?php

namespace App\Repositories;

interface WorkedSolutionsRepository
{
    public function create($skill_id);

    public function update($worked_solution_id, $question, $answer);
}
