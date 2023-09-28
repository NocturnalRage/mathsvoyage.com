<?php

namespace App\Controllers;

use App\Repositories\TimesTablesRepository;

class TimesTablesController extends Controller
{
    public function index(TimesTablesRepository $times)
    {
        if (! $this->loggedIn()) {
            $this->request->session['redirect'] = '/times-tables';

            return $this->redirectTo('/login');
        }
        $attempt = $this->getOrCreateAttempt($times);
        $scores = $times->getScoresForAttempt($attempt['id']);
        $pastScores = $times->getPastScores($this->request->user['user_id']);
        $this->response->setVars([
            'pageTitle' => 'Times Tables',
            'metaDescription' => 'Practice your times tables',
            'activeLink' => 'Arithmetic',
            'attempt' => $attempt,
            'scores' => $scores,
            'pastScores' => $pastScores,
        ]);

        return $this->response;
    }

    public function quiz(TimesTablesRepository $times)
    {
        if (! $this->loggedIn()) {
            $this->request->session['redirect'] = '/times-tables';

            return $this->redirectToHomePage('Login to attempt your times tables!');
        }
        $attempt = $this->getOrCreateAttempt($times);
        $this->response->setVars([
            'pageTitle' => 'Times Tables Quiz',
            'metaDescription' => 'Take a quiz on your times tables.',
            'activeLink' => 'Arithmetic',
            'attempt' => $attempt,
        ]);

        return $this->response;
    }

    public function record_score(TimesTablesRepository $times)
    {
        $this->response->setView('TimesTables/record-score.json.php');
        $this->response->header('Content-Type: application/json');

        if (! $this->loggedIn()) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must be logged in to record the score.'),
            ]);

            return $this->response;
        }

        if (! isset($this->request->post['attemptId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must select an attempt to record the score.'),
            ]);

            return $this->response;
        }

        if (! isset($this->request->post['timesTablesId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must select a times tables to record the score.'),
            ]);

            return $this->response;
        }

        if (! isset($this->request->post['attempt'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must select an attempt to record the score.'),
            ]);

            return $this->response;
        }

        if (! isset($this->request->post['score'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must provide a score.'),
            ]);

            return $this->response;
        }

        if (! isset($this->request->post['questionCount'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must provide the question count.'),
            ]);

            return $this->response;
        }

        if (! isset($this->request->post['quizStartTime'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must provide a score.'),
            ]);

            return $this->response;
        }

        if (! isset($this->request->post['quizEndTime'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must provide a score.'),
            ]);

            return $this->response;
        }

        if (! $attempt = $times->findAttempt($this->request->post['attemptId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must select a valid attempt to record the score.'),
            ]);

            return $this->response;
        }

        if ($this->request->user['user_id'] != $attempt['user_id']) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must select your own attempt to record the score.'),
            ]);

            return $this->response;
        }

        if (! $times->recordScore(
            $this->request->post['attemptId'],
            $this->request->post['timesTablesId'],
            $this->request->post['attempt'],
            $this->request->post['score'],
            $this->request->post['questionCount'],
            $this->request->post['quizStartTime'],
            $this->request->post['quizEndTime']
        )
        ) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('Could not record score.'),
            ]);

            return $this->response;
        }

        $this->response->setVars([
            'status' => json_encode('success'),
            'message' => json_encode('Score recorded.'),
        ]);

        return $this->response;
    }

    public function complete_attempt(TimesTablesRepository $times)
    {
        $this->response->setView('TimesTables/complete-attempt.json.php');
        $this->response->header('Content-Type: application/json');

        if (! $this->loggedIn()) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must be logged in to complete an attempt.'),
            ]);

            return $this->response;
        }

        if (! isset($this->request->post['attemptId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must select an attempt to complete.'),
            ]);

            return $this->response;
        }

        if (! $attempt = $times->findAttempt($this->request->post['attemptId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must select a valid attempt to complete.'),
            ]);

            return $this->response;
        }

        if ($this->request->user['user_id'] != $attempt['user_id']) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must select your own attempt to complete.'),
            ]);

            return $this->response;
        }

        if (! $times->completeAttempt($this->request->post['attemptId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('Could not complete attempt.'),
            ]);

            return $this->response;
        }

        $this->response->setVars([
            'status' => json_encode('success'),
            'message' => json_encode('Attempt incremented'),
        ]);

        return $this->response;
    }

    public function increment_attempt(TimesTablesRepository $times)
    {
        $this->response->setView('TimesTables/increment-attempt.json.php');
        $this->response->header('Content-Type: application/json');

        if (! $this->loggedIn()) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must be logged in to complete a times tables quiz.'),
            ]);

            return $this->response;
        }

        if (! isset($this->request->post['attemptId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must select an attempt to increment.'),
            ]);

            return $this->response;
        }

        if (! $attempt = $times->findAttempt($this->request->post['attemptId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must select a valid times tables attempt to increment.'),
            ]);

            return $this->response;
        }

        if ($this->request->user['user_id'] != $attempt['user_id']) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must select your own attempt to increment.'),
            ]);

            return $this->response;
        }

        if (! $times->incrementAttempt($this->request->post['attemptId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('Could not increment attempt.'),
            ]);

            return $this->response;
        }

        $this->response->setVars([
            'status' => json_encode('success'),
            'message' => json_encode('Attempt incremented'),
        ]);

        return $this->response;
    }

    public function increment_times_table(TimesTablesRepository $times)
    {
        $this->response->setView('TimesTables/increment-times-table.json.php');
        $this->response->header('Content-Type: application/json');

        if (! $this->loggedIn()) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must be logged in to complete a times tables quiz.'),
            ]);

            return $this->response;
        }

        if (! isset($this->request->post['attemptId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must select an attempt to increment.'),
            ]);

            return $this->response;
        }

        if (! $attempt = $times->findAttempt($this->request->post['attemptId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must select a valid times tables attempt to increment.'),
            ]);

            return $this->response;
        }

        if ($this->request->user['user_id'] != $attempt['user_id']) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('You must select your own attempt to increment.'),
            ]);

            return $this->response;
        }

        if (! $times->incrementTimesTable($this->request->post['attemptId'])) {
            $this->response->setVars([
                'status' => json_encode('error'),
                'message' => json_encode('Could not increment attempt.'),
            ]);

            return $this->response;
        }

        $this->response->setVars([
            'status' => json_encode('success'),
            'message' => json_encode('Times Table incremented'),
        ]);

        return $this->response;
    }

    private function getOrCreateAttempt($times)
    {
        if (! $attempt = $times->findAttemptByUser($this->request->user['user_id'])) {
            $id = $times->createAttempt($this->request->user['user_id']);
            $attempt = $times->findAttempt($id);
        }

        return $attempt;
    }
}
