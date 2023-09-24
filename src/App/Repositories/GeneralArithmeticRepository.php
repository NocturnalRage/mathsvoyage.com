<?php

namespace App\Repositories;

interface GeneralArithmeticRepository
{
    public function getScores($userId);

    public function recordScore($userId, $correct, $questionCount, $startTime, $endTime);
}
