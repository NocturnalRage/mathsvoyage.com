<?php

namespace App\Repositories;

interface QuizzesRepository
{
    public function findIncompleteSkillsQuiz($skill_id, $user_id);

    public function findQuestionsBySkillId($skill_id);

    public function createSkillsQuiz($skill_id, $user_id);

    public function createTopicQuiz($topic_id, $user_id);

    public function createCurriculumQuiz($curriculum_id, $user_id);

    public function createQuizQuestion($quiz_id, $skill_question_id);

    public function findIncompleteSkillQuizInfo($skill_id, $user_id);

    public function findQuiz($quizId);

    public function getQuizOptions($quizId);

    public function updateQuizMultipleChoiceQuestion(
        $quizId,
        $skillQuestionId,
        $skillQuestionOptionId,
        $correctUnaided,
        $questionStartTime,
        $questionEndTime
    );

    public function updateQuizNumericQuestion(
        $quizId,
        $skillQuestionId,
        $answer,
        $correctUnaided,
        $questionStartTime,
        $questionEndTime
    );

    public function updateQuiz(
        $quizId,
        $questionStartTime,
        $questionEndTime
    );

    public function getQuizResultsAndSkillMastery($quizId);

    public function insertOrUpdateSkillMastery($skillId, $userId, $masteryLevelId);

    public function findIncompleteTopicQuiz($topicId, $userId);

    public function findIncompleteTopicQuizInfo($topicId, $userId);

    public function getAllTopicQuestions($topicId);

    public function findIncompleteCurriculumQuiz($curriculumId, $userId);

    public function findIncompleteCurriculumQuizInfo($curriculumId, $userId);

    public function getAllCurriculumQuestions($curriculumId);

    public function getSkillQuestionHints($skillQuestionId);
}
