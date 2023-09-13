<?php

namespace App\Repositories;

use Framework\RepositoryNotFoundException;

class MysqlTopicsRepository implements TopicsRepository
{
    protected $dbh;

    public function __construct(\Mysqli $dbh)
    {
        $this->dbh = $dbh;
    }

    public function all()
    {
        $sql = 'SELECT c.curriculum_name, c.curriculum_slug, t.*
                FROM   curricula c
                JOIN   topics t on c.curriculum_id = t.curriculum_id
                ORDER BY learning_order';
        $result = $this->dbh->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function findByTitleAndCurriculumId($title, $curriculum_id)
    {
        $sql = 'SELECT *
                FROM   topics
                WHERE  title = ?
                AND    curriculum_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('si', $title, $curriculum_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    public function findBySlugAndCurriculumId($slug, $curriculum_id)
    {
        $sql = 'SELECT *
                FROM   topics
                WHERE  slug = ?
                AND    curriculum_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('si', $slug, $curriculum_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    public function findBySlugAndCurriculumIdOrFail($slug, $curriculum_id)
    {
        $topic = $this->findBySlugAndCurriculumId($slug, $curriculum_id);
        if (! $topic) {
            throw new RepositoryNotFoundException();
        }

        return $topic;
    }

    public function create($title, $slug, $curriculum_id, $learning_order)
    {
        $sql = 'INSERT INTO topics (
              topic_id,
              title,
              slug,
              curriculum_id,
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
            $curriculum_id,
            $learning_order
        );
        $stmt->execute();
        $stmt->close();

        return $this->dbh->insert_id;
    }

    public function findSkills($topic_id)
    {
        $sql = 'SELECT *
                FROM   skills
                WHERE  topic_id = ?
                ORDER BY learning_order';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $topic_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function findSkillsAndMasteryLevels($topic_id, $user_id)
    {
        $sql = "SELECT s.*,
                ifnull(ml.mastery_level_desc, 'Not started') as mastery_level_desc
                FROM   skills s
                LEFT JOIN skill_mastery sm ON sm.skill_id = s.skill_id AND sm.user_id = ?
                LEFT JOIN mastery_levels ml on ml.mastery_level_id = sm.mastery_level_id
                WHERE  s.topic_id = ?
                ORDER BY s.learning_order";
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('ii', $user_id, $topic_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
