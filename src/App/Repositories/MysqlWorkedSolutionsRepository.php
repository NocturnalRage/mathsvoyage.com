<?php

namespace App\Repositories;

class MysqlWorkedSolutionsRepository implements WorkedSolutionsRepository
{
    protected $dbh;

    public function __construct(\Mysqli $dbh)
    {
        $this->dbh = $dbh;
    }

    public function create($skill_id)
    {
        $sql = 'INSERT INTO worked_solutions (
              worked_solution_id,
              skill_id,
              question,
              answer,
              created_at,
              updated_at
            )
            VALUES (NULL, ?, NULL, NULL, now(), now())';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $skill_id);
        $stmt->execute();
        $stmt->close();

        return $this->dbh->insert_id;
    }

    public function update($worked_solution_id, $question, $answer)
    {
        $sql = 'UPDATE worked_solutions
                SET    question = ?,
                       answer = ?,
                       updated_at = now()
                WHERE  worked_solution_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param(
            'ssi',
            $question,
            $answer,
            $worked_solution_id
        );
        $stmt->execute();
        $rowsUpdated = $this->dbh->affected_rows;
        $stmt->close();

        return $rowsUpdated;
    }
}
