<?php

namespace Tests\Framework;

use Framework\Request;
use Mockery;

class RequestTest extends \PHPUnit\Framework\TestCase
{
    protected $nonAdminPersonId = 1000;

    protected $nonAdminLoginEmail = 'johndoe@example.com';

    protected $nonAdminPerson;

    protected $adminPersonId = 1;

    protected $adminLoginEmail = 'admin@nocturnalrage.com';

    protected $adminPerson;

    protected $password = 'secret';

    protected $passwordHash;

    public function setUp(): void
    {
        $this->passwordHash = password_hash($this->password, PASSWORD_DEFAULT);

        $this->nonAdminPerson = [
            'user_id' => $this->nonAdminPersonId,
            'email' => $this->nonAdminLoginEmail,
            'given_name' => 'John',
            'password' => $this->passwordHash,
            'admin' => 0,
        ];

        $this->adminPerson = [
            'user_id' => $this->adminPersonId,
            'email' => $this->adminLoginEmail,
            'given_name' => 'Jeff',
            'password' => $this->passwordHash,
            'admin' => 1,
        ];
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function newRequest()
    {
        return new Request();
    }

    public function testCookie()
    {
        $_COOKIE['foo'] = 'bar';
        $request = $this->newRequest();
        $this->assertSame('bar', $request->cookie['foo']);
    }

    public function testEnv()
    {
        $_ENV['foo'] = 'bar';
        $request = $this->newRequest();
        $this->assertSame('bar', $request->env['foo']);
    }

    public function testFiles()
    {
        $_FILES['foo'] = 'bar';
        $request = $this->newRequest();
        $this->assertSame('bar', $request->files['foo']);
    }

    public function testGet()
    {
        $_GET['foo'] = 'bar';
        $request = $this->newRequest();
        $this->assertSame('bar', $request->get['foo']);
    }

    public function testPost()
    {
        $_POST['foo'] = 'bar';
        $request = $this->newRequest();
        $this->assertSame('bar', $request->post['foo']);
    }

    public function testRequest()
    {
        $_REQUEST['foo'] = 'bar';
        $request = $this->newRequest();
        $this->assertSame('bar', $request->request['foo']);
    }

    public function testServer()
    {
        $_SERVER['foo'] = 'bar';
        $request = $this->newRequest();
        $this->assertSame('bar', $request->server['foo']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testSession()
    {
        $request = $this->newRequest();

        // session not started yet
        $this->assertFalse(isset($_SESSION));
        $this->assertFalse(isset($request->session));

        // session started
        session_start();
        $this->assertTrue(isset($_SESSION));

        // check the reference from $_SESSION to $request ...
        $_SESSION['foo'] = 'bar';
        $this->assertSame('bar', $request->session['foo']);

        // ... and from $request back to $_SESSION
        $request->session['baz'] = 'dib';
        $this->assertSame('dib', $_SESSION['baz']);

        // unset both property and superglobals
        unset($request->session);
        $this->assertFalse(isset($_SESSION));
        $this->assertFalse(isset($request->session));

    }

    /**
     * @runInSeparateProcess
     */
    public function testIssetLinksToSession()
    {
        $request = $this->newRequest();

        // session not started yet
        $this->assertFalse(isset($_SESSION));
        $this->assertFalse(isset($request->session));

        // session started
        session_start();

        // this should attach property to $_SESSION
        $this->assertTrue(isset($request->session));
        $request->session['baz'] = 'dib';
        $this->assertSame('dib', $_SESSION['baz']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetSessionBeforeStarted()
    {
        $request = $this->newRequest();
        $this->assertFalse(isset($GLOBALS['_SESSION']));
        $this->expectException('DomainException');
        $request->session['foo'] = 'bar';
    }

    public function testGetWrongName()
    {
        $request = $this->newRequest();
        $this->expectException('InvalidArgumentException');
        $request->notSession;
    }

    public function testIssetWrongName()
    {
        $request = $this->newRequest();
        $this->expectException('InvalidArgumentException');
        isset($request->notSession);
    }

    public function testUnsetWrongName()
    {
        $request = $this->newRequest();
        $this->expectException('InvalidArgumentException');
        unset($request->notSession);
    }

    /**
     * @runInSeparateProcess
     */
    public function testcreateCrsfToken()
    {
        session_start();
        $request = $this->newRequest();
        $crsfToken = $request->createCrsfToken();
        $this->assertSame($crsfToken, $request->session['crsfToken']);
        $crsfToken2 = $request->createCrsfToken();
        $this->assertSame($crsfToken2, $crsfToken);
    }

    /**
     * @runInSeparateProcess
     */
    public function testCrsfTokenIsNotValidNoCrsf()
    {
        session_start();
        $request = $this->newRequest();

        $this->assertFalse($request->crsfTokenIsValid());
    }

    /**
     * @runInSeparateProcess
     */
    public function testCrsfTokenIsNotValidNoCrsfPost()
    {
        session_start();
        $request = $this->newRequest();

        $crsfToken = $request->createCrsfToken();
        $this->assertFalse($request->crsfTokenIsValid());
    }

    /**
     * @runInSeparateProcess
     */
    public function testCrsfTokenIsNotValidNoCrsfSession()
    {
        session_start();
        $request = $this->newRequest();

        $request->post['crsfToken'] = 'fake-token';
        $this->assertFalse($request->crsfTokenIsValid());
    }

    /**
     * @runInSeparateProcess
     */
    public function testCrsfTokenIsNotValidNoMatch()
    {
        session_start();
        $request = $this->newRequest();

        $crsfToken = $request->createCrsfToken();
        $request->post['crsfToken'] = 'fake-token';
        $this->assertFalse($request->crsfTokenIsValid());
    }

    /**
     * @runInSeparateProcess
     */
    public function testCrsfTokenIsValid()
    {
        session_start();
        $request = $this->newRequest();

        $crsfToken = $request->createCrsfToken();
        $request->post['crsfToken'] = $crsfToken;

        $this->assertTrue($request->crsfTokenIsValid());
    }

    public function testUserNotLoggedIn()
    {
        $request = $this->newRequest();
        $this->assertFalse($request->loggedIn);
    }

    /**
     * @runInSeparateProcess
     */
    public function testUserIsLoggedInWithSessionVariable()
    {
        session_start();
        $_SESSION['loginEmail'] = $this->nonAdminLoginEmail;
        $request = $this->newRequest();
        $this->assertTrue($request->loggedIn);
    }

    public function testAnonymousUserIsNotAdmin()
    {
        $request = $this->newRequest();
        $this->assertFalse($request->isAdmin());
    }

    /**
     * @runInSeparateProcess
     */
    public function testRequestIsAdmin()
    {
        session_start();
        $_SESSION['loginEmail'] = $this->adminLoginEmail;
        $request = $this->newRequest();
        $request->user['admin'] = 1;
        $this->assertTrue($request->isAdmin());
    }

    /**
     * @runInSeparateProcess
     */
    public function testUserWhenNotLoggedIn()
    {
        session_start();
        $request = $this->newRequest();

        $user = $request->user;
        $this->assertNull($user);
    }

    /**
     * @runInSeparateProcess
     */
    public function testSetLoginStatusNotLoggedIn()
    {
        session_start();
        $request = $this->newRequest();
        $this->assertFalse($request->setLoginStatus());
        $this->assertFalse($request->loggedIn);
    }

    /**
     * @runInSeparateProcess
     */
    public function testSetLoginStatusLoggedInThroughSessionVariable()
    {
        session_start();
        $request = $this->newRequest();
        $_SESSION['loginEmail'] = $this->nonAdminLoginEmail;
        $this->assertTrue($request->setLoginStatus());
        $this->assertTrue($request->loggedIn);
    }

    /**
     * @runInSeparateProcess
     */
    public function testRequestLoginUser()
    {
        session_start();
        $request = $this->newRequest();
        $request->loginUser('johndoe@example.com');
        $this->assertEquals($request->session['loginEmail'], 'johndoe@example.com');
        $this->assertTrue($request->loggedIn);
    }

    public function testRequestRememberMeCookiesNotSet()
    {
        $request = $this->newRequest();
        $this->assertFalse($request->rememberMeCookiesSet());
    }

    public function testRequestRememberMeCookiesSet()
    {
        $request = $this->newRequest();
        $request->cookie['rememberme'] = 1;
        $request->cookie['remembermesession'] = 'session';
        $request->cookie['remembermetoken'] = 'token';
        $this->assertTrue($request->rememberMeCookiesSet());
    }

    public function testRequestAuthenticateRememberMeTokenFails()
    {
        $request = $this->newRequest();
        $request->cookie['remembermetoken'] = 'random-token';
        $this->assertFalse($request->authenticateRememberMeToken('not-token'));
    }

    public function testRequestAuthenticateRememberMeToken()
    {
        $request = $this->newRequest();
        $request->cookie['remembermetoken'] = 'random-token';
        $tokenHash = password_hash('random-token', PASSWORD_DEFAULT);
        $this->assertTrue($request->authenticateRememberMeToken($tokenHash));
    }

    /**
     * @runInSeparateProcess
     */
    public function testRequestValidate()
    {
        session_start();
        $this->expectException('Framework\ValidationException');
        $request = $this->newRequest();
        $request->request['email'] = 'invalid_email';
        $request->request['loginPassword'] = 'short';
        $request->request['title'] = 'too-long';
        $expectedErrors = [
            'email' => 'Not a valid email address',
            'loginPassword' => 'Too short. Cannot be less than 6 characters.',
            'name' => 'Required field',
            'title' => 'Too long. Cannot be more than 5 characters.',
        ];

        try {
            $request->validate([
                'email' => ['required', 'min:4', 'max:100', 'email'],
                'loginPassword' => ['required', 'min:6', 'max:100'],
                'name' => ['required', 'min:10', 'max:100'],
                'title' => ['max:5'],
            ]);
        } finally {
            $this->assertEquals($request->session['errors'], $expectedErrors);
            $this->assertEquals($request->session['flash']['message'], 'Please fix the displayed errors and resubmit');
        }
    }
}
