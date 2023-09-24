<?php

namespace App\Repositories;

class MysqlGeneralArithmeticRepository implements GeneralArithmeticRepository
{
    protected $dbh;

    public function __construct(\Mysqli $dbh)
    {
        $this->dbh = $dbh;
    }

    public function getScores($userId)
    {
        $sql = 'SELECT date_format(completed_at, "%M %d %Y") as quiz_date,
                       correct,
                       question_count,
                       round(correct/question_count * 100) as percentage,
                       TIMESTAMPDIFF(SECOND, started_at, completed_at) as seconds
                FROM   general_arithmetic_scores
                WHERE  user_id = ?
                ORDER BY completed_at desc';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function recordScore($userId, $correct, $questionCount, $startTime, $endTime)
    {
        $sql = 'INSERT INTO general_arithmetic_scores (
              id,
              user_id,
              correct,
              question_count,
              started_at,
              completed_at
            )
            VALUES (NULL, ?, ?, ?, ?, ?)';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('iiiss', $userId, $correct, $questionCount, $startTime, $endTime);
        $stmt->execute();
        $stmt->close();

        return $this->dbh->insert_id;
    }
}
