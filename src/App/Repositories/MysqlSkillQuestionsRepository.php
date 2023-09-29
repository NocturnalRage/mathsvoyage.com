<?php

namespace App\Repositories;

class MysqlSkillQuestionsRepository implements SkillQuestionsRepository
{
    protected $dbh;

    public function __construct(\Mysqli $dbh)
    {
        $this->dbh = $dbh;
    }

    public function find($skill_question_id)
    {
        $sql = 'SELECT *
                FROM   skill_questions
                WHERE  skill_question_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $skill_question_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    public function findOrFail($skill_question_id)
    {
        $question = $this->find($skill_question_id);
        if (! $question) {
            throw new RepositoryNotFoundException();
        }

        return $question;
    }

    public function findOptions($skill_question_id)
    {
        $sql = 'SELECT *
                FROM   skill_question_options
                WHERE  skill_question_id = ?
                ORDER BY option_order';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $skill_question_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function create($question, $skill_id, $skill_question_type_id, $skill_question_category_id)
    {
        $sql = 'INSERT INTO skill_questions (
              skill_question_id,
              skill_id,
              skill_question_type_id,
              skill_question_category_id,
              question,
              question_image,
              created_at,
              updated_at
            )
            VALUES (NULL, ?, ?, ?, ?, NULL, now(), now())';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param(
            'iiis',
            $skill_id,
            $skill_question_type_id,
            $skill_question_category_id,
            $question
        );
        $stmt->execute();
        $stmt->close();

        return $this->dbh->insert_id;
    }

    public function createOption(
        $skill_question_id,
        $option_text,
        $option_order,
        $correct
    ) {
        $sql = 'INSERT INTO skill_question_options (
              skill_question_option_id,
              skill_question_id,
              option_text,
              option_order,
              correct,
              created_at,
              updated_at
            )
            VALUES (NULL, ?, ?, ?, ?, now(), now())';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param(
            'isii',
            $skill_question_id,
            $option_text,
            $option_order,
            $correct
        );
        $stmt->execute();
        $stmt->close();

        return $this->dbh->insert_id;
    }

    public function createNumber($skill_question_id, $answer)
    {
        $sql = 'INSERT INTO skill_question_numbers (
              skill_question_number_id,
              skill_question_id,
              answer,
              created_at,
              updated_at
            )
            VALUES (NULL, ?, ?, now(), now())';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('id', $skill_question_id, $answer);
        $stmt->execute();
        $stmt->close();

        return $this->dbh->insert_id;
    }

    public function updateImage($skill_question_id, $question_image)
    {
        $sql = 'UPDATE skill_questions
                SET    question_image = ?,
                       updated_at = now()
                WHERE  skill_question_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param(
            'si',
            $question_image,
            $skill_question_id
        );
        $stmt->execute();
        $rowsUpdated = $this->dbh->affected_rows;
        $stmt->close();

        return $rowsUpdated;
    }
}
