<?php

namespace App\Repositories;

interface TopicsRepository
{
    public function all();

    public function findByTitleAndCurriculumId($title, $curriculum_id);

    public function findBySlugAndCurriculumId($slug, $curriculum_id);

    public function findBySlugAndCurriculumIdOrFail($slug, $curriculum_id);

    public function create($title, $slug, $curriculum_id, $learning_order);

    public function findSkills($topic_id);

    public function findSkillsAndMasteryLevels($topic_id, $user_id);
}
