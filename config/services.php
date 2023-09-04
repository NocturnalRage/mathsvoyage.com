<?php

$di = new Framework\Di();

$di->set('Dotenv', function () {
    return Dotenv\Dotenv::createImmutable(__DIR__.'/..');
});

$di->set('routes', function () use ($di) {
    return require 'routes.php';
});

$di->set('Router', function () use ($di) {
    $router = new AltoRouter();
    $router->addRoutes($di->get('routes'));

    return $router;
});

$di->set('Request', function () {
    return new Framework\Request();
});

$di->set('Response', function () {
    return new Framework\Response('../views');
});

/* Controllers start here */
$di->set('PageNotFoundController', function () use ($di) {
    return new App\Controllers\PageNotFoundController(
        $di->get('Request'),
        $di->get('Response')
    );
});

$di->set('StaticPageController', function () use ($di) {
    return new App\Controllers\StaticPageController(
        $di->get('Request'),
        $di->get('Response')
    );
});

$di->set('AuthenticationController', function () use ($di) {
    return new App\Controllers\AuthenticationController(
        $di->get('Request'),
        $di->get('Response')
    );
});

/* Repositories start here */
$di->set('dbh', function () {
    return new \Mysqli(
        $_ENV['DB_HOST'],
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD'],
        $_ENV['DB_DATABASE']);
});

$di->set('UserRepository', function () use ($di) {
    return new App\Repositories\MysqlUserRepository($di->get('dbh'));
});

$di->set('MailRepository', function () use ($di) {
    return new App\Repositories\MysqlMailRepository($di->get('dbh'));
});

$di->set('ReCaptcha', function () {
    return new \ReCaptcha\ReCaptcha(
        $_ENV['RECAPTCHA_SECRET'],
        new \ReCaptcha\RequestMethod\CurlPost()
    );
});

$di->set('RecaptchaClient', function () use ($di) {
    return new Framework\Recaptcha\RecaptchaClient(
        $di->get('ReCaptcha'),
        $_ENV['RECAPTCHA_KEY'],
    );
});

/* Emails start here */
$di->set('Mailer', function () {
    if ($_ENV['MAIL_METHOD'] == 'LoggerMail') {
        return new Framework\Mail\LoggerMail(
            $_ENV['APPLICATION_LOG_FILE']
        );
    }

    return new Framework\Mail\AwsSesMail(
        $_ENV['AWS_SES_KEY'],
        $_ENV['AWS_SES_SECRET'],
        $_ENV['AWS_SES_REGION']);
});

$di->set('EmailTemplate', function () {
    return new App\Emails\EmailTemplate();
});

$di->set('ActivateMembershipEmail', function () use ($di) {
    return new App\Emails\ActivateMembershipEmail(
        $di->get('EmailTemplate'),
        $di->get('MailRepository'),
        $di->get('Mailer'),
        $_ENV['SUPPORT_EMAIL']
    );
});
