<?php

namespace App\Controllers;

use DateTime;
use Framework\Request;
use Framework\Response;

abstract class Controller
{
    protected $request;

    protected $response;

    public function __construct(
        Request $request,
        Response $response
    ) {
        $this->request = $request;
        $this->response = $response;
    }

    public function addSessionVar($varName)
    {
        if (isset($this->request->session[$varName])) {
            $this->response->addVars([
                $varName => $this->request->session[$varName],
            ]);
            unset($this->request->session[$varName]);
        }
    }

    public function logMessage($message, $logLevel)
    {
        if (! in_array($logLevel, ['DEBUG', 'INFO', 'WARNING', 'ERROR'])) {
            $logLevel = 'INFO';
        }

        $date = new DateTime();
        $logDate = $date->format('y-m-d h:i:s');

        $message = '['.$logDate.'] '.$logLevel.': '.$message.PHP_EOL;

        file_put_contents($_ENV['APPLICATION_LOG_FILE'], $message, FILE_APPEND);
    }

    public function isAdmin()
    {
        return $this->request->isAdmin();
    }

    public function loggedIn()
    {
        return $this->request->loggedIn;
    }

    public function generateSlug($text)
    {
        $slug = substr($text, 0, 240);
        $slug = strtolower($slug);
        $slug = str_replace('.', '-', $slug);
        $slug = preg_replace('/[^a-z0-9 ]/', '', $slug);
        $slug = preg_replace('/\s+/', '-', $slug);
        $slug = trim($slug, '-');

        return $slug;
    }

    public function redirectTo($link)
    {
        $this->response->redirect($link);

        return $this->response;
    }
}
