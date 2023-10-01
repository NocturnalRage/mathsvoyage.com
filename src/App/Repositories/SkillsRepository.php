<?php

namespace App\Repositories;

interface SkillsRepository
{
    public function find($skill_id);

    public function findOrFail($skill_id);

    public function findByTitleAndTopicId($title, $curriculum_id);

    public function findBySlugAndTopicId($slug, $curriculum_id);

    public function findBySlugAndTopicIdOrFail($slug, $topic_id);

    public function create($title, $slug, $topic_id, $learning_order);

    public function all();

    public function findQuestions($skill_id);

    public function findQuestionsByCategoryId($skill_id, $category_id);

    public function findAllQuestionsForSkill($skill_id);

    public function findWorkedSolutions($skill_id);

    public function findVideos($skill_id);

    public function getSkillQuestionCategories();

    public function getCurrentWorkedSolutions($skill_id);

    public function getTopicWorkedSolutions($skill_id);

    public function getCurriculumWorkedSolutions($skill_id);
}
