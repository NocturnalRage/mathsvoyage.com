<?php

namespace App\Repositories;

use Framework\RepositoryNotFoundException;

class MysqlSkillsRepository implements SkillsRepository
{
    protected $dbh;

    public function __construct(\Mysqli $dbh)
    {
        $this->dbh = $dbh;
    }

    public function findByTitleAndTopicId($title, $topic_id)
    {
        $sql = 'SELECT *
                FROM   skills
                WHERE  title = ?
                AND    topic_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('si', $title, $topic_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    public function findBySlugAndTopicId($slug, $topic_id)
    {
        $sql = 'SELECT *
                FROM   skills
                WHERE  slug = ?
                AND    topic_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('si', $slug, $topic_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    public function findBySlugAndTopicIdOrFail($slug, $topic_id)
    {
        $skill = $this->findBySlugAndTopicId($slug, $topic_id);
        if (! $skill) {
            throw new RepositoryNotFoundException();
        }

        return $skill;
    }

    public function create($title, $slug, $topic_id, $learning_order)
    {
        $sql = 'INSERT INTO skills (
              skill_id,
              title,
              slug,
              topic_id,
              learning_order,
              created_at,
              updated_at
            )
            VALUES (NULL, ?, ?, ?, ?, now(), now())';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param(
            'ssii',
            $title,
            $slug,
            $topic_id,
            $learning_order
        );
        $stmt->execute();
        $stmt->close();

        return $this->dbh->insert_id;
    }

    public function all()
    {
        $sql = 'SELECT c.curriculum_name, c.curriculum_slug, 
                t.title as topic_title, t.slug as topic_slug,
                s.*
                FROM   curricula c
                JOIN   topics t on c.curriculum_id = t.curriculum_id
                JOIN   skills s on t.topic_id = s.topic_id
                ORDER BY c.display_order, t.learning_order, s.learning_order';
        $result = $this->dbh->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function findQuestions($skill_id)
    {
        $sql = 'SELECT sq.skill_question_id,
                       sq.skill_id,
                       sq.skill_question_type_id,
                       sq.skill_question_category_id,
                       sq.question,
                       sq.question_image,
                       sqo.skill_question_option_id,
                       sqo.option_text,
                       sqo.correct,
                       sqo.option_order,
                       sqn.skill_question_number_id,
                       sqn.answer
                FROM   skill_questions sq
                LEFT JOIN skill_question_options sqo ON sq.skill_question_id = sqo.skill_question_id
                LEFT JOIN skill_question_numbers sqn ON sq.skill_question_id = sqn.skill_question_id
                WHERE  skill_id = ?
                ORDER BY sq.skill_question_id, sqo.option_order';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $skill_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function findQuestionsByCategoryId($skill_id, $category_id)
    {
        $sql = 'SELECT sq.skill_question_id,
                       sq.skill_id,
                       sq.skill_question_type_id,
                       sq.skill_question_category_id,
                       sq.question,
                       sq.question_image,
                       sqo.skill_question_option_id,
                       sqo.option_text,
                       sqo.correct,
                       sqo.option_order,
                       sqn.skill_question_number_id,
                       sqn.answer
                FROM   skill_questions sq
                LEFT JOIN skill_question_options sqo ON sq.skill_question_id = sqo.skill_question_id
                LEFT JOIN skill_question_numbers sqn ON sq.skill_question_id = sqn.skill_question_id
                WHERE  sq.skill_id = ?
                AND    sq.skill_question_category_id = ?
                ORDER BY sq.skill_question_id, sqo.option_order';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('ii', $skill_id, $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function findAllQuestionsForSkill($skill_id)
    {
        $sql = 'SELECT sq.skill_question_id,
                       sq.skill_id,
                       sq.skill_question_type_id,
                       sq.skill_question_category_id,
                       sq.question,
                       sq.question_image,
                       sqo.skill_question_option_id,
                       sqo.option_text,
                       sqo.correct,
                       sqo.option_order,
                       sqn.skill_question_number_id,
                       sqn.answer
                FROM   skill_questions sq
                LEFT JOIN skill_question_options sqo ON sq.skill_question_id = sqo.skill_question_id
                LEFT JOIN skill_question_numbers sqn ON sq.skill_question_id = sqn.skill_question_id
                WHERE  sq.skill_id = ?
                ORDER BY sq.skill_question_id, sqo.option_order';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $skill_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function findWorkedSolutions($skill_id)
    {
        $sql = 'SELECT *
                FROM   worked_solutions
                WHERE  skill_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $skill_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function findVideos($skill_id)
    {
        $sql = 'SELECT *
                FROM   videos
                WHERE  skill_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $skill_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getSkillQuestionCategories()
    {
        $sql = 'SELECT *
                FROM   skill_question_categories
                ORDER BY skill_question_category_id';
        $result = $this->dbh->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
