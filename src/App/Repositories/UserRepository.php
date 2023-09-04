<?php

namespace App\Repositories;

interface userRepository
{
    public function create(
        $givenName,
        $familyName,
        $email,
        $loginPassword
    );

    public function update(
        $userId,
        $givenName,
        $familyName,
        $email
    );

    public function updatePassword($userId, $password);

    public function updateLastLogin($userId);

    public function find($userId);

    public function findOrFail($userId);

    public function findByEmail($email);

    public function findByToken($token);

    public function activate($userId);

    public function insertRememberMe($userId, $session, $tokenHash);

    public function getRememberMe($userId, $sessionId);

    public function deleteRememberMe($userId);

    public function insertPasswordResetRequest($userId, $token);

    public function getPasswordResetRequest($token);

    public function processPasswordResetRequest($userId, $token);

    public function createSubscriber($listId, $userId);

    public function updateSubscriber($listId, $userId, $statusId);

    public function findSubscriber($listId, $userId);
}
