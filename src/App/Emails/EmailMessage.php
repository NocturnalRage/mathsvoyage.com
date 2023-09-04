<?php

namespace App\Emails;

use App\Repositories\MailRepository;
use Framework\Mail\MailContract;

abstract class EmailMessage
{
    private $fromEmail;

    private $replyEmail;

    private $toEmail;

    private $subject;

    private $bodyText;

    private $bodyHtml;

    protected $emailTemplate;

    protected $mailRepository;

    protected $mailer;

    protected $supportEmail;

    public function __construct(
        EmailTemplate $emailTemplate,
        MailRepository $mailRepository,
        MailContract $mailer,
        string $supportEmail
    ) {
        $this->emailTemplate = $emailTemplate;
        $this->mailRepository = $mailRepository;
        $this->mailer = $mailer;
        $this->toEmail = $supportEmail;
        $this->fromEmail = $supportEmail;
        $this->replyEmail = $supportEmail;
    }

    public function queueEmail(array $mailInfo)
    {
        $this->prepareEmail($mailInfo);
        $mailQueueId = $this->addToMailQueue();

        return $mailQueueId;
    }

    public function sendEmail(array $mailInfo)
    {
        $this->prepareEmail($mailInfo);
        $this->mailer->send(
            $this->fromEmail,
            $this->replyEmail,
            $this->toEmail,
            $this->subject,
            $this->bodyText,
            $this->bodyHtml,
            'UTF-8'
        );
    }

    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;
    }

    public function getReplyEmail()
    {
        return $this->replyEmail;
    }

    public function setReplyEmail($replyEmail)
    {
        $this->replyEmail = $replyEmail;
    }

    public function getToEmail()
    {
        return $this->toEmail;
    }

    public function setToEmail($toEmail)
    {
        $this->toEmail = $toEmail;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function getBodyText()
    {
        return $this->bodyText;
    }

    public function setBodyText($bodyText)
    {
        $this->bodyText = $bodyText;
    }

    public function getBodyHtml()
    {
        return $this->bodyHtml;
    }

    public function setBodyHtml($bodyHtml)
    {
        $this->bodyHtml = $bodyHtml;
    }

    private function addToMailQueue()
    {
        $mailQueueId = $this->mailRepository->insertTransactionalMail(
            $this->fromEmail,
            $this->replyEmail,
            $this->toEmail,
            $this->subject,
            $this->bodyText,
            $this->bodyHtml
        );

        return $mailQueueId;
    }

    abstract protected function prepareEmail(array $mailInfo);
}
