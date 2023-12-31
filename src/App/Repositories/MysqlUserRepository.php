<?php

namespace App\Repositories;

use Framework\RepositoryNotFoundException;

class MysqlUserRepository implements UserRepository
{
    protected $dbh;

    public function __construct(\Mysqli $dbh)
    {
        $this->dbh = $dbh;
    }

    public function create(
        $givenName,
        $familyName,
        $email,
        $loginPassword
    ) {
        $hashedPassword = password_hash($loginPassword, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(32));
        $sql = 'INSERT INTO users (
              user_id,
              given_name,
              family_name,
              email,
              password,
              email_status_id,
              bounce_count,
              admin,
              token,
              activated_at,
              last_login_at,
              created_at,
              updated_at
            )
            VALUES (NULL, ?, ?, ?, ?, 1, 0, 0, ?, NULL, NULL, now(), now())';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param(
            'sssss',
            $givenName,
            $familyName,
            $email,
            $hashedPassword,
            $token
        );
        $stmt->execute();
        $stmt->close();

        return $this->dbh->insert_id;
    }

    public function update(
        $userId,
        $givenName,
        $familyName,
        $email
    ) {
        $sql = 'UPDATE users
            SET    given_name = ?,
                   family_name = ?,
                   email = ?,
                   updated_at = now()
            WHERE  user_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param(
            'sssi',
            $givenName,
            $familyName,
            $email,
            $userId
        );
        $stmt->execute();
        $rowsUpdated = $this->dbh->affected_rows;
        $stmt->close();

        return $rowsUpdated;
    }

    public function updatePassword($userId, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = 'UPDATE users
            SET    password = ?,
                   updated_at = now()
            WHERE  user_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param(
            'si',
            $hashedPassword,
            $userId
        );
        $stmt->execute();
        $rowsUpdated = $this->dbh->affected_rows;
        $stmt->close();

        return $rowsUpdated;
    }

    public function updateLastLogin($userId)
    {
        $sql = 'update users set last_login_at = now() where user_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $rowsUpdated = $this->dbh->affected_rows;
        $stmt->close();

        return $rowsUpdated;
    }

    public function find($userId)
    {
        $sql = 'SELECT *
            FROM   users
            WHERE  user_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    public function findOrFail($userId)
    {
        $user = $this->find($userId);
        if (! $user) {
            throw new RepositoryNotFoundException();
        }

        return $user;
    }

    public function findByEmail($email)
    {
        $sql = 'SELECT *
	          FROM   users
            WHERE  email = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    public function findByToken($token)
    {
        $sql = 'SELECT *
	          FROM   users
            WHERE  token = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    public function activate($userId)
    {
        $sql = 'UPDATE users
            SET    activated_at = now()
            WHERE  user_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $rowsUpdated = $this->dbh->affected_rows;
        $stmt->close();

        return $rowsUpdated;
    }

    public function insertRememberMe($userId, $session, $tokenHash)
    {
        $sql = 'insert into remember_me
            (user_id, session_id, token, created_at, expires_at)
            values
            (?, ?, ?, now(), date_add(now(), INTERVAL 12 MONTH))';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('iss', $userId, $session, $tokenHash);
        $stmt->execute();
        $rowsInserted = $this->dbh->affected_rows;
        $stmt->close();

        return $rowsInserted;
    }

    public function getRememberMe($userId, $sessionId)
    {
        $sql = 'SELECT *
            FROM   remember_me
            WHERE  user_id = ?
            AND    session_id = ?
            AND    expires_at > now()';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('is', $userId, $sessionId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    public function deleteRememberMe($userId)
    {
        $sql = 'DELETE FROM remember_me
            WHERE  user_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $rowsDeleted = $this->dbh->affected_rows;
        $stmt->close();

        return $rowsDeleted;
    }

    public function insertPasswordResetRequest($userId, $token)
    {
        $sql = 'insert into password_reset_requests
            (user_id, token, created_at, processed_at)
            values
            (?, ?, now(), NULL)';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('is', $userId, $token);
        $stmt->execute();
        $rowsInserted = $this->dbh->affected_rows;
        $stmt->close();

        return $rowsInserted;
    }

    public function getPasswordResetRequest($token)
    {
        $sql = 'SELECT user_id, processed_at, created_at
            FROM   password_reset_requests
            WHERE  token = ?
            AND    processed_at is NULL
            AND    created_at > date_sub(now(), INTERVAL 24 HOUR)';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    public function processPasswordResetRequest($userId, $token)
    {
        $sql = 'update password_reset_requests
            set    processed_at = now()
            where  user_id = ?
            and    token = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('is', $userId, $token);
        $stmt->execute();
        $rowsUpdated = $this->dbh->affected_rows;
        $stmt->close();

        return $rowsUpdated;
    }

    public function createSubscriber($listId, $userId)
    {
        $active = 1;
        $sql = 'insert into subscribers
            (list_id, user_id, subscriber_status_id, created_at, updated_at,
             subscribed_at, last_campaign_at, last_autoresponder_at)
            values
            (?, ?, ?, now(), now(), now(), now(), now())';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('iii', $listId, $userId, $active);
        $stmt->execute();
        $rowsInserted = $this->dbh->affected_rows;
        $stmt->close();

        return $rowsInserted;
    }

    public function updateSubscriber($listId, $userId, $statusId)
    {
        $sql = 'UPDATE subscribers
            SET    subscriber_status_id = ?,
                   updated_at = now()
            WHERE  list_id = ?
            AND    user_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('iii', $statusId, $listId, $userId);
        $stmt->execute();
        $rowsUpdated = $this->dbh->affected_rows;
        $stmt->close();

        return $rowsUpdated;
    }

    public function findSubscriber($listId, $userId)
    {
        $sql = 'SELECT *
            FROM   subscribers
            WHERE  list_id = ?
            AND    user_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('ii', $listId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    public function getQuizResultsSummary($userId)
    {
        $sql = 'SELECT c.curriculum_name,
                       t.title as topic_title,
                       s.title as skill_title,
                       sum(qq.correct_unaided) as correct_unaided,
                       count(*) as question_count,
                       round(sum(qq.correct_unaided) / count(*) * 100) as percentage
                FROM   quiz_questions qq
                JOIN   quizzes q on q.quiz_id = qq.quiz_id
                JOIN   skill_questions sq on sq.skill_question_id = qq.skill_question_id
                JOIN   skills s on s.skill_id = sq.skill_id
                JOIN   topics t on t.topic_id = s.topic_id
                JOIN   curricula c on c.curriculum_id = t.curriculum_id
                WHERE  q.user_id = ?
                GROUP BY c.curriculum_name, t.title, s.title
                ORDER BY c.display_order desc, t.learning_order desc, s.learning_order desc';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTimesTablesResultsSummary($userId)
    {
        $sql = 'SELECT sum(tts.score) as correct,
                       sum(tts.question_count) as attempted,
                       round(sum(tts.score) / sum(tts.question_count) * 100) as percent
                FROM   times_tables_attempts tta
                JOIN   times_tables_scores tts ON tta.id = tts.attempt_id
                WHERE  tta.user_id = ?
                GROUP BY tta.user_id';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    public function getGeneralArithmeticResultsSummary($userId)
    {
        $sql = 'SELECT sum(correct) as correct,
                       sum(question_count) as attempted,
                       round(sum(correct) / sum(question_count) * 100) as percent
                FROM   general_arithmetic_scores
                WHERE  user_id = ?
                GROUP BY user_id';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }
}
