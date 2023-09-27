<?php

namespace App\Repositories;

interface SkillsRepository
{
    public function findByTitleAndTopicId($title, $curriculum_id);

    public function findBySlugAndTopicId($slug, $curriculum_id);

    public function findBySlugAndTopicIdOrFail($slug, $topic_id);

    public function create($title, $slug, $topic_id, $learning_order);

    public function all();

    public function findQuestions($skill_id);

    public function findWorkedSolutions($skill_id);

    public function findVideos($skill_id);

    public function getSkillQuestionCategories();
}
