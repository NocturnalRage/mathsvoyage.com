<?php

namespace App\Repositories;

class MysqlTimesTablesRepository implements TimesTablesRepository
{
    protected $dbh;

    public function __construct(\Mysqli $dbh)
    {
        $this->dbh = $dbh;
    }

    public function findAttempt($attemptId)
    {
        $sql = 'SELECT tta.id, tta.user_id, tta.current_times_tables_id,
                       tta.attempt, tta.started_at, tta.completed_at,
                       tt.id as times_tables_id, tt.title, tt.min_number,
                       tt.max_number, tt.total_questions, tt.repetitions
                FROM   times_tables_attempts tta
                JOIN   times_tables tt on tt.id = tta.current_times_tables_id
                WHERE  tta.id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $attemptId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    public function findAttemptByUser($userId)
    {
        $sql = 'SELECT tta.id, tta.user_id, tta.current_times_tables_id,
                       tta.attempt, tta.started_at, tta.completed_at,
                       tt.id as times_tables_id, tt.title, tt.min_number,
                       tt.max_number, tt.total_questions, tt.repetitions
                FROM   times_tables_attempts tta
                JOIN   times_tables tt on tt.id = tta.current_times_tables_id
                WHERE  tta.user_id = ?
                AND    tta.completed_at is NULL';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    public function getScoresForAttempt($attemptId)
    {
        $sql = 'SELECT date_format(tts.completed_at, "%M %d %Y") as quiz_date,
                       tts.id, tts.attempt_id, tts.times_tables_id,
                       tts.attempt, tts.score, tts.started_at, tts.completed_at,
                       TIMESTAMPDIFF(SECOND, tts.started_at, tts.completed_at) as time_in_seconds,
                       round(tts.score/tt.total_questions * 100) as percent,
                       tt.id as times_tables_id, tt.title, tt.min_number,
                       tt.max_number, tt.total_questions, tt.repetitions
                FROM   times_tables_scores tts
                JOIN   times_tables tt on tt.id = tts.times_tables_id
                WHERE  tts.attempt_id = ?
                ORDER BY tts.times_tables_id desc, tts.attempt desc';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $attemptId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function createAttempt($userId)
    {
        $sql = 'INSERT INTO times_tables_attempts (
              id,
              user_id,
              current_times_tables_id,
              attempt,
              started_at,
              completed_at
            )
            VALUES (NULL, ?, 1, 1, now(), NULL)';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $stmt->close();

        return $this->dbh->insert_id;
    }

    public function completeAttempt($attemptId)
    {
        $sql = 'UPDATE times_tables_attempts
                SET    completed_at = now()
                WHERE  id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $attemptId);
        $stmt->execute();
        $rowsUpdated = $this->dbh->affected_rows;
        $stmt->close();

        return $rowsUpdated;
    }

    public function incrementAttempt($attemptId)
    {
        $sql = 'UPDATE times_tables_attempts
                SET    attempt = attempt + 1
                WHERE  id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $attemptId);
        $stmt->execute();
        $rowsUpdated = $this->dbh->affected_rows;
        $stmt->close();

        return $rowsUpdated;
    }

    public function incrementTimesTable($attemptId)
    {
        $sql = 'UPDATE times_tables_attempts
                SET    current_times_tables_id = current_times_tables_id + 1,
                       attempt = 1
                WHERE  id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $attemptId);
        $stmt->execute();
        $rowsUpdated = $this->dbh->affected_rows;
        $stmt->close();

        return $rowsUpdated;
    }

    public function recordScore($attemptId, $timesTablesId, $attempt, $score, $startTime, $endTime)
    {
        $sql = 'INSERT INTO times_tables_scores (
              id,
              attempt_id,
              times_tables_id,
              attempt,
              score,
              started_at,
              completed_at
            )
            VALUES (NULL, ?, ?, ?, ?, ?, ?)';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('iiiiss', $attemptId, $timesTablesId, $attempt, $score, $startTime, $endTime);
        $stmt->execute();
        $stmt->close();

        return $this->dbh->insert_id;
    }
    public function getPastScores($userId)
    {
        $sql = 'SELECT tta.id,
                       date_format(tta.completed_at, "%M %d %Y") as finish_date,
                       sum(tts.score) as total_score,
                       count(*) * 50 as question_count,
                       round(sum(tts.score) / (count(*) * 50) * 100) as percentage,
                       round(avg(TIMESTAMPDIFF(SECOND, tts.started_at, tts.completed_at))) as average_time
                FROM   times_tables_attempts tta
                JOIN   times_tables_scores tts ON tta.id = tts.attempt_id
                WHERE  tta.user_id = ?
                AND    tta.completed_at is NOT NULL
                GROUP BY tta.id
                ORDER BY tta.id desc';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
