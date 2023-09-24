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

$di->set('CurriculaController', function () use ($di) {
    return new App\Controllers\CurriculaController(
        $di->get('Request'),
        $di->get('Response')
    );
});

$di->set('SkillsController', function () use ($di) {
    return new App\Controllers\SkillsController(
        $di->get('Request'),
        $di->get('Response')
    );
});

$di->set('SkillQuestionsController', function () use ($di) {
    return new App\Controllers\SkillQuestionsController(
        $di->get('Request'),
        $di->get('Response')
    );
});

$di->set('VideosController', function () use ($di) {
    return new App\Controllers\VideosController(
        $di->get('Request'),
        $di->get('Response')
    );
});

$di->set('WorkedSolutionsController', function () use ($di) {
    return new App\Controllers\WorkedSolutionsController(
        $di->get('Request'),
        $di->get('Response')
    );
});

$di->set('TopicsController', function () use ($di) {
    return new App\Controllers\TopicsController(
        $di->get('Request'),
        $di->get('Response')
    );
});

$di->set('QuizzesController', function () use ($di) {
    return new App\Controllers\QuizzesController(
        $di->get('Request'),
        $di->get('Response')
    );
});

$di->set('TimesTablesController', function () use ($di) {
    return new App\Controllers\TimesTablesController(
        $di->get('Request'),
        $di->get('Response')
    );
});

$di->set('GeneralArithmeticController', function () use ($di) {
    return new App\Controllers\GeneralArithmeticController(
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

$di->set('CurriculaRepository', function () use ($di) {
    return new App\Repositories\MysqlCurriculaRepository($di->get('dbh'));
});

$di->set('QuizzesRepository', function () use ($di) {
    return new App\Repositories\MysqlQuizzesRepository($di->get('dbh'));
});

$di->set('SkillsRepository', function () use ($di) {
    return new App\Repositories\MysqlSkillsRepository($di->get('dbh'));
});

$di->set('SkillQuestionsRepository', function () use ($di) {
    return new App\Repositories\MysqlSkillQuestionsRepository($di->get('dbh'));
});

$di->set('VideosRepository', function () use ($di) {
    return new App\Repositories\MysqlVideosRepository($di->get('dbh'));
});

$di->set('WorkedSolutionsRepository', function () use ($di) {
    return new App\Repositories\MysqlWorkedSolutionsRepository($di->get('dbh'));
});

$di->set('TopicsRepository', function () use ($di) {
    return new App\Repositories\MysqlTopicsRepository($di->get('dbh'));
});

$di->set('TimesTablesRepository', function () use ($di) {
    return new App\Repositories\MysqlTimesTablesRepository($di->get('dbh'));
});

$di->set('GeneralArithmeticRepository', function () use ($di) {
    return new App\Repositories\MysqlGeneralArithmeticRepository($di->get('dbh'));
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

$di->set('ForgotPasswordEmail', function () use ($di) {
    return new App\Emails\ForgotPasswordEmail(
        $di->get('EmailTemplate'),
        $di->get('MailRepository'),
        $di->get('Mailer'),
        $_ENV['SUPPORT_EMAIL']
    );
});
