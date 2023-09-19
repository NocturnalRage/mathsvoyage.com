<?php

namespace App\Repositories;

interface TimesTablesRepository
{
    public function findAttempt($attemptId);

    public function findAttemptByUser($userId);

    public function getScoresForAttempt($attemptId);

    public function createAttempt($userId);

    public function completeAttempt($attemptId);

    public function incrementAttempt($attemptId);

    public function incrementTimesTable($attemptId);

    public function recordScore($attemptId, $timesTablesId, $attempt, $score, $startTime, $endTime);
}
