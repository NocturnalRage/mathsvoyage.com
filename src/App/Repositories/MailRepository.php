<?php

namespace App\Repositories;

interface MailRepository
{
    public function find($id);

    public function findQueued($limit);

    public function create(
        $fromEmail, $replyEmail, $toEmail, $subject, $bodyText, $bodyHtml
    );

    public function updateEmailStatus($statusId, $mailQueueId);
}
