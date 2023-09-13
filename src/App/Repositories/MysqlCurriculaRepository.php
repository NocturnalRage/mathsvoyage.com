<?php

namespace App\Repositories;

use Framework\RepositoryNotFoundException;

class MysqlCurriculaRepository implements CurriculaRepository
{
    protected $dbh;

    public function __construct(\Mysqli $dbh)
    {
        $this->dbh = $dbh;
    }

    public function find($curriculumId)
    {
        $sql = 'SELECT *
                FROM   curricula
                WHERE  curriculum_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $curriculumId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    public function findOrFail($curriculumId)
    {
        $curriculum = $this->find($curriculumId);
        if (! $curriculum) {
            throw new RepositoryNotFoundException();
        }

        return $curriculum;
    }

    public function findByName($name)
    {
        $sql = 'SELECT *
                FROM   curricula
                WHERE  curriculum_name = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('s', $name);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    public function findByNameOrFail($name)
    {
        $curriculum = $this->findByName($name);
        if (! $curriculum) {
            throw new RepositoryNotFoundException();
        }

        return $curriculum;
    }

    public function findBySlug($slug)
    {
        $sql = 'SELECT *
                FROM   curricula
                WHERE  curriculum_slug = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    public function findBySlugOrFail($slug)
    {
        $curriculum = $this->findBySlug($slug);
        if (! $curriculum) {
            throw new RepositoryNotFoundException();
        }

        return $curriculum;
    }

    public function all()
    {
        $sql = 'SELECT *
                FROM   curricula
                ORDER BY display_order';
        $result = $this->dbh->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function findTopics($curriculumId, $userId)
    {
        $sql = 'SELECT t.*
                      ,count(*) * 100 as total_points
                      ,ifnull(sum(ml.points), 0) as user_points
                      ,round((ifnull(sum(ml.points), 0)) / (count(*) * 100) * 100) as percent_complete
                FROM   topics t
                JOIN   skills s on t.topic_id = s.topic_id
                LEFT JOIN skill_mastery sm ON s.skill_id = sm.skill_id AND user_id = ?
                LEFT JOIN mastery_levels ml on sm.mastery_level_id = ml.mastery_level_id
                WHERE  curriculum_id = ?
                GROUP BY t.topic_id
                ORDER BY t.learning_order';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('ii', $userId, $curriculumId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function findTopicsOrFail($curriculumId, $userId)
    {
        $topics = $this->findTopics($curriculumId, $userId);
        if (! $topics) {
            throw new RepositoryNotFoundException();
        }

        return $topics;
    }

    public function create($name, $slug, $displayOrder)
    {
        $sql = 'INSERT INTO curricula (
              curriculum_id,
              curriculum_name,
              curriculum_slug,
              display_order,
              created_at,
              updated_at
            )
            VALUES (NULL, ?, ?, ?, now(), now())';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param(
            'ssi',
            $name,
            $slug,
            $displayOrder
        );
        $stmt->execute();
        $stmt->close();

        return $this->dbh->insert_id;
    }

    public function updateBySlug($name, $slug, $displayOrder)
    {
        $sql = 'UPDATE curricula
                SET    curriculum_name = ?,
                       display_order = ?,
                       updated_at = now()
                WHERE  curriculum_slug = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param(
            'sis',
            $name,
            $displayOrder,
            $slug
        );
        $stmt->execute();
        $rowsUpdated = $this->dbh->affected_rows;
        $stmt->close();

        return $rowsUpdated;
    }
}
