<?php

namespace Tests\App\Repositories;

use App\Repositories\MysqlUserRepository;
use PHPUnit\Framework\TestCase;

class MysqlUserRepositoryTest extends TestCase
{
    protected $dbh;

    protected $users;

    protected $givenName = 'John';

    protected $familyName = 'Doe';

    protected $email = 'johndoe@example.com';

    protected $password = 'secret';

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
        $this->users = new MysqlUserRepository($this->dbh);
    }

    public function tearDown(): void
    {
        $this->dbh->close();
    }

    public function testUsersCreateFindFindByEmailUpdatePasswordUpdate()
    {
        $this->dbh->begin_transaction();

        // Test create
        $userId = $this->users->create(
            $this->givenName,
            $this->familyName,
            $this->email,
            $this->password
        );

        // Test find
        $user = $this->users->find($userId);
        $this->assertEquals($user['given_name'], $this->givenName);
        $this->assertEquals($user['family_name'], $this->familyName);
        $this->assertEquals($user['email'], $this->email);
        $this->assertEquals($user['admin'], 0);

        // Test findByEmail
        $user = $this->users->findByEmail($this->email);
        $this->assertEquals($user['given_name'], $this->givenName);
        $this->assertEquals($user['family_name'], $this->familyName);
        $this->assertEquals($user['email'], $this->email);
        $this->assertEquals($user['admin'], 0);

        // Test findByToken
        $user = $this->users->findByToken($user['token']);
        $this->assertEquals($user['given_name'], $this->givenName);
        $this->assertEquals($user['family_name'], $this->familyName);
        $this->assertEquals($user['email'], $this->email);
        $this->assertEquals($user['admin'], 0);

        // Test activate
        $rowsUpdated = $this->users->activate($user['user_id']);
        $this->assertEquals($rowsUpdated, 1);

        // Test updatePassword
        $newPassword = 'testing123';
        $rowsUpdated = $this->users->updatePassword($userId, $newPassword);
        $this->assertEquals(1, $rowsUpdated);
        $user = $this->users->find($userId);
        $this->assertEquals(true, password_verify($newPassword, $user['password']));

        // Test update
        $rowsUpdated = $this->users->update(
            $userId,
            'Minnie',
            'Mouse',
            'minnie@example.com',
        );
        $this->assertEquals(1, $rowsUpdated);
        $user = $this->users->findByEmail('minnie@example.com');
        $this->assertEquals($user['given_name'], 'Minnie');
        $this->assertEquals($user['family_name'], 'Mouse');
        $this->assertEquals($user['email'], 'minnie@example.com');
        $this->assertEquals($user['admin'], 0);

        // Test updateLastLogin
        $this->users->updateLastLogin($userId);
        $this->assertEquals(1, $rowsUpdated);

        // Test createSubscriber
        $listId = 1;
        $rowsInserted = $this->users->createSubscriber($listId, $userId);
        $this->assertEquals($rowsInserted, 1);

        // Test findSubscriber
        $active = 1;
        $subscriber = $this->users->findSubscriber($listId, $userId);
        $this->assertEquals($subscriber['subscriber_status_id'], $active);

        // Test updateSubscriber
        $unsubscribed = 2;
        $rowsUpdated = $this->users->updateSubscriber($listId, $userId, $unsubscribed);
        $this->assertEquals($rowsUpdated, 1);
        $subscriber = $this->users->findSubscriber($listId, $userId);
        $this->assertEquals($subscriber['subscriber_status_id'], $unsubscribed);

        // Test insertRememberMe
        $rowsInserted = $this->users->insertRememberMe($userId, 'Session', 'Token');
        $this->assertEquals(1, $rowsUpdated);

        // Test getRememberMe
        $rememberMe = $this->users->getRememberMe($userId, 'Session');
        $this->assertEquals($rememberMe['token'], 'Token');

        // Test deleteRememberMe
        $rowsDeleted = $this->users->deleteRememberMe($userId);
        $this->assertEquals(1, $rowsDeleted);

        $this->dbh->rollback();
    }
}
