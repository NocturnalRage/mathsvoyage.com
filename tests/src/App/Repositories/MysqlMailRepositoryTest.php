<?php

namespace Tests\App\Repositories;

use App\Repositories\MysqlMailRepository;
use PHPUnit\Framework\TestCase;

class MysqlMailRepositoryTest extends TestCase
{
    protected $dbh;

    protected $mailRepository;

    public function setUp(): void
    {
        $dotenv = \Dotenv\Dotenv::createMutable(__DIR__.'/../../../../', '.env.test');
        $dotenv->load();
        $this->dbh = new \Mysqli(
            $_ENV['DB_HOST'],
            $_ENV['DB_USERNAME'],
            $_ENV['DB_PASSWORD'],
            $_ENV['DB_DATABASE']
        );
        $this->dbh->begin_transaction();
        $this->mailRepository = new MysqlMailRepository($this->dbh);
    }

    public function tearDown(): void
    {
        $this->dbh->rollback();
        $this->dbh->close();
    }

    public function testMailRepositoryFunctions()
    {
        $fromEmail = 'website@cubeshack.com';
        $replyEmail = 'website@cubeshack.com';
        $toEmail = 'johndoe@example.com';
        $subject = 'Cube Shack Website';
        $bodyText = 'It looks good';
        $bodyHtml = '<p>It looks good</p>';

        // Insert, retrieve, and delete data from the table
        $mailQueueId = $this->mailRepository->create(
            $fromEmail, $replyEmail, $toEmail, $subject, $bodyText, $bodyHtml
        );
        $mail = $this->mailRepository->find($mailQueueId);
        $this->assertEquals($fromEmail, $mail['from_email']);
        $this->assertEquals($replyEmail, $mail['reply_email']);
        $this->assertEquals($toEmail, $mail['to_email']);
        $this->assertEquals($subject, $mail['subject']);
        $this->assertEquals($bodyText, $mail['body_text']);
        $this->assertEquals($bodyHtml, $mail['body_html']);

        $emails = $this->mailRepository->findQueued(10);
        $this->assertCount(1, $emails);
        $this->assertEquals($subject, $emails[0]['subject']);

        $this->mailRepository->updateEmailStatus(2, $emails[0]['mail_queue_id']);
        $emails = $this->mailRepository->findQueued(10);
        $this->assertCount(0, $emails);
    }
}
