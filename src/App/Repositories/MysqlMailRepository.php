<?php

namespace App\Repositories;

class MysqlMailRepository implements MailRepository
{
    protected $dbh;

    public function __construct(\Mysqli $dbh)
    {
        $this->dbh = $dbh;
    }

    public function find($id)
    {
        $sql = 'SELECT *
            FROM   mail_queue
            WHERE  mail_queue_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows == 0) {
            return false;
        }
        $mail = $result->fetch_assoc();

        return $mail;
    }

    public function findQueued($limit)
    {
        $sql = 'SELECT *
            FROM   mail_queue
            WHERE  status_id = 1
            ORDER BY created_at
            LIMIT ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function create(
        $fromEmail, $replyEmail, $toEmail, $subject, $bodyText, $bodyHtml
    ) {
        $sql = 'INSERT INTO mail_queue
            (mail_queue_id, from_email, reply_email, to_email,
             subject, body_text, body_html, created_at, sent_at,
             status_id)
            VALUES
            (NULL, ?, ?, ?, ?, ?, ?, now(), NULL, 1)';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('ssssss', $fromEmail, $replyEmail, $toEmail, $subject, $bodyText, $bodyHtml);
        $stmt->execute();
        $mailQueueId = $this->dbh->insert_id;
        $stmt->close();

        return $mailQueueId;
    }

    public function updateEmailStatus($statusId, $mailQueueId)
    {
        $sql = 'UPDATE mail_queue
            SET    status_id = ?,
                   sent_at = now()
            WHERE  mail_queue_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('ii', $statusId, $mailQueueId);
        $stmt->execute();
        $rowsUpdated = $this->dbh->affected_rows;
        $stmt->close();

        return $rowsUpdated;
    }
}
