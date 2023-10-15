<?php

namespace App\Repositories;

class MysqlQuizzesRepository implements QuizzesRepository
{
    protected $dbh;

    public function __construct(\Mysqli $dbh)
    {
        $this->dbh = $dbh;
    }

    public function findIncompleteSkillsQuiz($skill_id, $user_id)
    {
        $sql = 'SELECT q.*
                FROM   quizzes q
                WHERE  q.skill_id = ?
                AND    q.user_id = ?
                AND    q.completed_at is NULL
                AND    q.quiz_type_id = 1';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('ii', $skill_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    public function findQuestionsBySkillId($skill_id)
    {
        $sql = 'SELECT sq.skill_question_id,
                   sq.skill_id
            FROM   skill_questions sq
            WHERE  sq.skill_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $skill_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function createSkillsQuiz($skill_id, $user_id)
    {
        $sql = 'INSERT INTO quizzes (
              quiz_id,
              quiz_type_id,
              skill_id,
              user_id,
              created_at,
              started_at,
              completed_at
            )
            VALUES (NULL, 1, ?, ?, now(), NULL, NULL)';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('ii', $skill_id, $user_id);
        $stmt->execute();
        $quizId = $this->dbh->insert_id;
        $stmt->close();

        return $quizId;
    }

    public function createTopicQuiz($topic_id, $user_id)
    {
        $sql = 'INSERT INTO quizzes (
              quiz_id,
              quiz_type_id,
              topic_id,
              user_id,
              created_at,
              started_at,
              completed_at
            )
            VALUES (NULL, 2, ?, ?, now(), NULL, NULL)';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('ii', $topic_id, $user_id);
        $stmt->execute();
        $quizId = $this->dbh->insert_id;
        $stmt->close();

        return $quizId;
    }

    public function createCurriculumQuiz($curriculum_id, $user_id)
    {
        $sql = 'INSERT INTO quizzes (
              quiz_id,
              quiz_type_id,
              curriculum_id,
              user_id,
              created_at,
              started_at,
              completed_at
            )
            VALUES (NULL, 3, ?, ?, now(), NULL, NULL)';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('ii', $curriculum_id, $user_id);
        $stmt->execute();
        $quizId = $this->dbh->insert_id;
        $stmt->close();

        return $quizId;
    }

    public function createQuizQuestion($quiz_id, $skill_question_id)
    {
        $sql = 'INSERT INTO quiz_questions (
              quiz_id,
              skill_question_id,
              skill_question_option_id,
              created_at,
              started_at,
              answered_at
            )
            VALUES (?, ?, NULL, now(), NULL, NULL)';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('ii', $quiz_id, $skill_question_id);
        $stmt->execute();
        $rowsInserted = $this->dbh->affected_rows;
        $stmt->close();

        return $rowsInserted;
    }

    public function findIncompleteSkillQuizInfo($skill_id, $user_id)
    {
        $sql = 'SELECT q.quiz_id,
                       count(*) question_count
                FROM   quizzes q
                JOIN   quiz_questions qq ON qq.quiz_id = q.quiz_id
                WHERE  q.skill_id = ?
                AND    q.user_id = ?
                AND    q.completed_at is NULL
                AND    q.quiz_type_id = 1
                GROUP BY q.quiz_id';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('ii', $skill_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    public function findQuiz($quizId)
    {
        $sql = 'SELECT q.*
              FROM   quizzes q
              WHERE  q.quiz_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $quizId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    public function getQuizOptions($quizId)
    {
        $sql = 'SELECT q.quiz_id,
                     q.user_id,
                     sq.skill_question_id,
                     sq.skill_question_type_id,
                     sq.randomise_options,
                     sq.question,
                     sq.question_image,
                     sqo.skill_question_option_id,
                     sqo.option_text,
                     sqo.option_order,
                     sqo.correct,
                     sqk.answer,
                     sqk.form,
                     sqk.simplify
              FROM   quizzes q
              JOIN   quiz_questions qq on qq.quiz_id = q.quiz_id
              JOIN   skill_questions sq on sq.skill_question_id = qq.skill_question_id
              LEFT JOIN   skill_question_options sqo on sqo.skill_question_id = sq.skill_question_id
              LEFT JOIN   skill_question_kas sqk on sqk.skill_question_id = sq.skill_question_id
              JOIN   skills s on s.skill_id = sq.skill_id
              JOIN   topics t on s.topic_id = t.topic_id
              JOIN   curricula c on t.curriculum_id = c.curriculum_id
              WHERE  q.quiz_id = ?
              ORDER BY sq.skill_question_id, sqk.skill_question_kas_id';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $quizId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateQuizMultipleChoiceQuestion(
        $quizId,
        $skillQuestionId,
        $skillQuestionOptionId,
        $correctUnaided,
        $questionStartTime,
        $questionEndTime
    ) {
        $sql = 'UPDATE quiz_questions
                SET    skill_question_option_id = ?,
                       correct_unaided = ?,
                       started_at = ?,
                       answered_at = ?
                WHERE  quiz_id = ?
                AND    skill_question_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param(
            'iissii',
            $skillQuestionOptionId,
            $correctUnaided,
            $questionStartTime,
            $questionEndTime,
            $quizId,
            $skillQuestionId
        );
        $stmt->execute();
        $rowsUpdated = $this->dbh->affected_rows;
        $stmt->close();

        return $rowsUpdated;
    }

    public function updateQuizNumericQuestion(
        $quizId,
        $skillQuestionId,
        $answer,
        $correctUnaided,
        $questionStartTime,
        $questionEndTime
    ) {
        $sql = 'UPDATE quiz_questions
                SET    answer = ?,
                       correct_unaided = ?,
                       started_at = ?,
                       answered_at = ?
                WHERE  quiz_id = ?
                AND    skill_question_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param(
            'dissii',
            $answer,
            $correctUnaided,
            $questionStartTime,
            $questionEndTime,
            $quizId,
            $skillQuestionId
        );
        $stmt->execute();
        $rowsUpdated = $this->dbh->affected_rows;
        $stmt->close();

        return $rowsUpdated;
    }

    public function updateQuiz(
        $quizId,
        $quizStartTime,
        $quizEndTime
    ) {
        $sql = 'UPDATE quizzes
                SET    started_at = ?,
                       completed_at = ?
                WHERE  quiz_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param(
            'ssi',
            $quizStartTime,
            $quizEndTime,
            $quizId
        );
        $stmt->execute();
        $rowsUpdated = $this->dbh->affected_rows;
        $stmt->close();

        return $rowsUpdated;
    }

    public function getQuizResultsAndSkillMastery($quizId)
    {
        $sql = "SELECT s.skill_id,
                       s.title,
                       q.quiz_type_id,
                       IFNULL(sm.mastery_level_id, 0) mastery_level_id,
                       IFNULL(ml.mastery_level_desc, 'Not started') mastery_level_desc,
                       sum(qq.correct_unaided) correct,
                       count(*) total
                FROM   quizzes q
                JOIN   quiz_questions qq on q.quiz_id = qq.quiz_id
                JOIN   skill_questions sq on sq.skill_question_id = qq.skill_question_id
                JOIN   skills s on s.skill_id = sq.skill_id
                LEFT JOIN skill_mastery sm on sm.skill_id = s.skill_id and sm.user_id = q.user_id
                LEFT JOIN mastery_levels ml on ml.mastery_level_id = sm.mastery_level_id
                WHERE  q.quiz_id = ?
                GROUP BY s.skill_id, s.title, q.quiz_type_id";
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $quizId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function insertOrUpdateSkillMastery($skillId, $userId, $masteryLevelId)
    {
        $sql = 'INSERT INTO skill_mastery (
                skill_id,
                user_id,
                mastery_level_id,
                created_at,
                updated_at
              )
              VALUES (?, ?, ?, now(), now())
              ON DUPLICATE KEY UPDATE mastery_level_id = ?, updated_at = now()';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param(
            'iiii',
            $skillId,
            $userId,
            $masteryLevelId,
            $masteryLevelId
        );
        $stmt->execute();
        $skillId = $this->dbh->insert_id;
        $stmt->close();

        return $skillId;
    }

    public function findIncompleteTopicQuiz($topicId, $userId)
    {
        $sql = 'SELECT q.*
                FROM   quizzes q
                WHERE  q.topic_id = ?
                AND    q.user_id = ?
                AND    q.completed_at is NULL
                AND    q.quiz_type_id = 2';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('ii', $topicId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    public function findIncompleteTopicQuizInfo($topicId, $userId)
    {
        $sql = 'SELECT q.quiz_id,
                       count(*) question_count
                FROM   quizzes q
                JOIN   quiz_questions qq ON qq.quiz_id = q.quiz_id
                WHERE  q.topic_id = ?
                AND    q.user_id = ?
                AND    q.completed_at is NULL
                AND    q.quiz_type_id = 2
                GROUP BY q.quiz_id';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('ii', $topicId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    public function getAllTopicQuestions($topicId)
    {
        $sql = 'SELECT sq.skill_question_id,
                       sq.skill_id
                FROM   skill_questions sq
                JOIN   skills s on s.skill_id = sq.skill_id
                WHERE  s.topic_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $topicId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function findIncompleteCurriculumQuiz($curriculumId, $userId)
    {
        $sql = 'SELECT q.*
                FROM   quizzes q
                WHERE  q.curriculum_id = ?
                AND    q.user_id = ?
                AND    q.completed_at is NULL
                AND    q.quiz_type_id = 3';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('ii', $curriculumId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    public function findIncompleteCurriculumQuizInfo($curriculumId, $userId)
    {
        $sql = 'SELECT q.quiz_id,
                       count(*) question_count
                FROM   quizzes q
                JOIN   quiz_questions qq ON qq.quiz_id = q.quiz_id
                WHERE  q.curriculum_id = ?
                AND    q.user_id = ?
                AND    q.completed_at is NULL
                AND    q.quiz_type_id = 3
                GROUP BY q.quiz_id';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('ii', $curriculumId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    public function getAllCurriculumQuestions($curriculumId)
    {
        $sql = 'SELECT sq.skill_question_id,
                       sq.skill_id
                FROM   skill_questions sq
                JOIN   skills s on s.skill_id = sq.skill_id
                JOIN   topics t on s.topic_id = t.topic_id
                WHERE  t.curriculum_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $curriculumId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getSkillQuestionHints($skillQuestionId)
    {
        $sql = 'SELECT h.hint_id,
                   h.skill_question_id,
                   h.hint,
                   h.hint_order
            FROM   skill_question_hints h
            WHERE  h.skill_question_id = ?
            ORDER BY h.hint_order';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $skillQuestionId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_all(MYSQLI_ASSOC);

    }
}
