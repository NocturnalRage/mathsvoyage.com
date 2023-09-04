<?php

namespace Tests\src\App\Controllers;

use App\Controllers\AuthenticationController;
use Framework\Request;
use Framework\Response;
use Mockery;
use PHPUnit\Framework\TestCase;

class AuthenticationControllerTest extends TestCase
{
    protected $request;

    protected $response;

    protected $users;

    protected $recaptcha;

    protected $recaptchaKey;

    protected $controller;

    public function setUp(): void
    {
        $this->request = new Request();
        $this->response = new Response('views');
        $this->users = Mockery::mock('App\Repositories\UserRepository');
        $this->recaptcha = Mockery::mock('Framework\Recaptcha\RecaptchaClient');
        $this->recaptchaKey = 'recaptcha';
        $this->controller = new AuthenticationController(
            $this->request,
            $this->response,
            $this->users,
            $this->recaptcha,
            $this->recaptchaKey
        );
        session_start();
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @runInSeparateProcess
     */
    public function testAuthenticationControllerCanBeInstantiated()
    {
        $this->assertSame(get_class($this->controller), 'App\Controllers\AuthenticationController');
    }

    /**
     * @runInSeparateProcess
     */
    public function testLoginFormRedirectsHomeWhenLoggedIn()
    {
        $this->loginUser();
        $response = $this->controller->loginForm($this->recaptcha);
        $expectedHeaders = [['header', 'Location: /']];
        $this->assertSame($expectedHeaders, $response->getHeaders());
        $this->assertSame('You are already logged in!', $this->request->session['flash']['message']);
    }

    public function testLoginFormView()
    {
        $this->recaptcha->shouldReceive('getRecaptchaKey');
        $this->response = $this->controller->loginForm($this->recaptcha);
        $responseVars = $this->response->getVars();
        $expectedPageTitle = 'Login';
        $expectedMetaDescription = 'Login to my website.';
        $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
        $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testLoginValidateRedirectsHomeWhenLoggedIn()
    {
        $this->loginUser();

        $this->response = $this->controller->loginValidate(
            $this->users,
            $this->recaptcha
        );
        $expectedHeaders = [['header', 'Location: /']];
        $this->assertSame($expectedHeaders, $this->response->getHeaders());
        $this->assertSame('You are already logged in!', $this->request->session['flash']['message']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testLoginValidateWhenNoPostData()
    {
        $recaptchaResponse = 'Fake Response';
        $ipAddress = '127.0.0.1';
        $serverName = 'cubeshack.com';
        $this->request->server['REMOTE_ADDR'] = $ipAddress;
        $this->request->server['SERVER_NAME'] = $serverName;
        $this->expectException('Framework\ValidationException');
        $this->recaptcha->shouldReceive('verified')
            ->once()
            ->with($serverName, 'loginwithversion3', $recaptchaResponse, $ipAddress)
            ->andReturn(true);
        $this->request->post = [
            'g-recaptcha-response' => $recaptchaResponse,
        ];
        $this->response = $this->controller->loginValidate(
            $this->users,
            $this->recaptcha
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testLoginValidateNoRecaptcha()
    {
        $email = 'johndoe@example.com';
        $password = 'secret';
        $this->request->post = [
            'email' => $email,
            'loginPassword' => $password,
        ];
        $this->request->request = $this->request->post;
        $this->response = $this->controller->loginValidate(
            $this->users,
            $this->recaptcha
        );
        $expectedHeaders = [['header', 'Location: /login']];
        $this->assertSame($expectedHeaders, $this->response->getHeaders());
        $expectedFlashMessage = 'No validation provided. Please ensure you are human!';
        $this->assertEquals($expectedFlashMessage, $this->request->session['flash']['message']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testLoginValidateInvalidRecaptcha()
    {
        $recaptchaResponse = 'Fake Response';
        $ipAddress = '127.0.0.1';
        $serverName = 'jeffplumb.com';
        $this->request->server['REMOTE_ADDR'] = $ipAddress;
        $this->request->server['SERVER_NAME'] = $serverName;
        $email = 'fred@example.com';
        $password = 'secret';
        $this->request->post = [
            'email' => $email,
            'loginPassword' => $password,
            'g-recaptcha-response' => $recaptchaResponse,
        ];
        $this->request->request = $this->request->post;
        $this->recaptcha->shouldReceive('verified')
            ->once()
            ->with($serverName, 'loginwithversion3', $recaptchaResponse, $ipAddress)
            ->andReturn(false);
        $this->response = $this->controller->loginValidate(
            $this->users,
            $this->recaptcha
        );
        $expectedHeaders = [['header', 'Location: /login']];
        $this->assertSame($expectedHeaders, $this->response->getHeaders());
        $expectedFlashMessage = 'Could not validate your request. Please ensure you are human!';
        $this->assertEquals($expectedFlashMessage, $this->request->session['flash']['message']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testLoginValidateUserNotActivated()
    {
        $recaptchaResponse = 'Fake Response';
        $ipAddress = '127.0.0.1';
        $serverName = 'jeffplumb.com';
        $this->request->server['REMOTE_ADDR'] = $ipAddress;
        $this->request->server['SERVER_NAME'] = $serverName;
        $email = 'fred@example.com';
        $password = 'secret';
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $user = [
            'user_id' => 10,
            'email' => $email,
            'given_name' => 'John',
            'password' => $passwordHash,
            'admin' => 0,
            'activated_at' => null,
        ];
        $this->request->post = [
            'email' => $email,
            'loginPassword' => $password,
            'g-recaptcha-response' => $recaptchaResponse,
        ];
        $this->request->request = $this->request->post;
        $this->recaptcha->shouldReceive('verified')
            ->once()
            ->with($serverName, 'loginwithversion3', $recaptchaResponse, $ipAddress)
            ->andReturn(true);
        $this->users->shouldReceive('findByEmail')->with($email)->andReturn($user);
        $this->response = $this->controller->loginValidate(
            $this->users,
            $this->recaptcha
        );
        $expectedHeaders = [['header', 'Location: /register']];
        $this->assertSame($expectedHeaders, $this->response->getHeaders());
        $expectedFlashMessage = 'You must confirm your email address before you can login. An email has been sent with instructions. You can register again to receive a new email.';
        $this->assertEquals($expectedFlashMessage, $this->request->session['flash']['message']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testLoginValidateRedirectWhenInvalidUsernameOrPassword()
    {
        $recaptchaResponse = 'Fake Response';
        $ipAddress = '127.0.0.1';
        $serverName = 'jeffplumb.com';
        $this->request->server['REMOTE_ADDR'] = $ipAddress;
        $this->request->server['SERVER_NAME'] = $serverName;
        $email = 'johndoe@example.com';
        $password = 'secret';
        $this->request->post = [
            'email' => $email,
            'loginPassword' => $password,
            'g-recaptcha-response' => $recaptchaResponse,
        ];
        $this->request->request = $this->request->post;
        $this->recaptcha->shouldReceive('verified')
            ->once()
            ->with($serverName, 'loginwithversion3', $recaptchaResponse, $ipAddress)
            ->andReturn(true);
        $this->users->shouldReceive('findByEmail')->with($email);
        $this->response = $this->controller->loginValidate(
            $this->users,
            $this->recaptcha
        );
        $expectedHeaders = [['header', 'Location: /login']];
        $this->assertSame($expectedHeaders, $this->response->getHeaders());
        $expectedFlashMessage = 'The email and password do not match. Login failed.';
        $this->assertEquals($expectedFlashMessage, $this->request->session['flash']['message']);
        $this->assertEquals($this->request->post, $this->request->session['formVars']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testLoginValidateLoginWithoutRememberMe()
    {
        $recaptchaResponse = 'Fake Response';
        $ipAddress = '127.0.0.1';
        $serverName = 'jeffplumb.com';
        $this->request->server['REMOTE_ADDR'] = $ipAddress;
        $this->request->server['SERVER_NAME'] = $serverName;
        $email = 'johndoe@example.com';
        $password = 'secretshh';
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $user = [
            'user_id' => 10,
            'email' => $email,
            'given_name' => 'John',
            'password' => $passwordHash,
            'admin' => 0,
            'activated_at' => '20230904',
        ];
        $this->request->post = [
            'email' => $email,
            'loginPassword' => $password,
            'g-recaptcha-response' => $recaptchaResponse,
        ];
        $this->request->request = $this->request->post;
        $this->users->shouldReceive('findByEmail')->with($email)->andReturn($user);
        $this->users->shouldReceive('updateLastLogin');
        $this->recaptcha->shouldReceive('verified')
            ->once()
            ->with($serverName, 'loginwithversion3', $recaptchaResponse, $ipAddress)
            ->andReturn(true);
        $this->response = $this->controller->loginValidate(
            $this->users,
            $this->recaptcha
        );
        $expectedHeaders = [['header', 'Location: /']];
        $this->assertSame($expectedHeaders, $this->response->getHeaders());
        $expectedFlash = 'You are now logged in';
        $this->assertEquals($expectedFlash, $this->request->session['flash']['message']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testLoginValidateLoginWithRedirectWithoutRememberMe()
    {
        $recaptchaResponse = 'Fake Response';
        $ipAddress = '127.0.0.1';
        $serverName = 'jeffplumb.com';
        $this->request->server['REMOTE_ADDR'] = $ipAddress;
        $this->request->server['SERVER_NAME'] = $serverName;
        $redirect = '/';
        $this->request->session['redirect'] = $redirect;
        $email = 'fred@example.com';
        $password = 'secretshh';
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $user = [
            'user_id' => 10,
            'email' => $email,
            'given_name' => 'John',
            'password' => $passwordHash,
            'admin' => 0,
            'activated_at' => '20230904',
        ];
        $this->request->post = [
            'email' => $email,
            'loginPassword' => $password,
            'g-recaptcha-response' => $recaptchaResponse,
        ];
        $this->request->request = $this->request->post;
        $this->users->shouldReceive('findByEmail')->with($email)->andReturn($user);
        $this->users->shouldReceive('updateLastLogin');
        $this->recaptcha->shouldReceive('verified')
            ->once()
            ->with($serverName, 'loginwithversion3', $recaptchaResponse, $ipAddress)
            ->andReturn(true);
        $this->response = $this->controller->loginValidate(
            $this->users,
            $this->recaptcha
        );
        $expectedHeaders = [['header', 'Location: '.$redirect]];
        $this->assertSame($expectedHeaders, $this->response->getHeaders());
        $expectedFlash = 'You are now logged in';
        $this->assertEquals($expectedFlash, $this->request->session['flash']['message']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testLoginValidateLoginSuccessWithRememberMe()
    {
        $recaptchaResponse = 'Fake Response';
        $ipAddress = '127.0.0.1';
        $serverName = 'jeffplumb.com';
        $this->request->server['REMOTE_ADDR'] = $ipAddress;
        $this->request->server['SERVER_NAME'] = $serverName;
        $userId = 99;
        $email = 'fred@example.com';
        $password = 'secretshh';
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $user = [
            'user_id' => $userId,
            'email' => $email,
            'given_name' => 'John',
            'password' => $passwordHash,
            'admin' => 0,
            'activated_at' => '20230904',
        ];
        $this->request->post = [
            'user_id' => $userId,
            'email' => $email,
            'loginPassword' => $password,
            'rememberme' => 'y',
            'g-recaptcha-response' => $recaptchaResponse,
        ];
        $this->request->request = $this->request->post;
        $this->users->shouldReceive('findByEmail')->with($email)->andReturn($user);
        $this->users->shouldReceive('updateLastLogin');
        $this->recaptcha->shouldReceive('verified')
            ->once()
            ->with($serverName, 'loginwithversion3', $recaptchaResponse, $ipAddress)
            ->andReturn(true);
        $this->users->shouldReceive('insertRememberMe')
            ->once()
            ->andReturn(1);
        $this->response = $this->controller->loginValidate(
            $this->users,
            $this->recaptcha
        );
        $expectedRedirectHeader = ['header', 'Location: /'];
        $headers = $this->response->getHeaders();
        // Cookies should be set in header
        $expectedFunction = $headers[0][0];
        $expectedCookieName = $headers[0][1];
        $expectedUserId = $headers[0][2];
        $this->assertSame($expectedFunction, 'setcookie');
        $this->assertSame($expectedCookieName, 'rememberme');
        $this->assertSame($expectedUserId, $userId);
        $expectedFunction = $headers[1][0];
        $expectedCookieName = $headers[1][1];
        $this->assertSame($expectedFunction, 'setcookie');
        $this->assertSame($expectedCookieName, 'remembermesession');
        $expectedFunction = $headers[2][0];
        $expectedCookieName = $headers[2][1];
        $this->assertSame($expectedFunction, 'setcookie');
        $this->assertSame($expectedCookieName, 'remembermetoken');
        $redirectHeader = $headers[3];
        $this->assertSame($expectedRedirectHeader, $redirectHeader);
        $expectedFlash = 'You are now logged in';
        $this->assertEquals($expectedFlash, $this->request->session['flash']['message']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testLogoutRedirectsToHomePageWhenAlreadyLoggedOut()
    {
        $this->response = $this->controller->logout($this->users);
        $expectedHeaders = [['header', 'Location: /']];
        $this->assertSame($expectedHeaders, $this->response->getHeaders());
        $this->assertSame('You are already logged out!', $this->request->session['flash']['message']);
    }

    /**
     ** @runInSeparateProcess
     **/
    public function testLogoutWhenLoggedIn()
    {
        $this->loginUser();
        $this->users->shouldReceive('deleteRememberMe')
            ->once()
            ->andReturn(1);
        $this->response = $this->controller->logout($this->users);
        $expectedRedirectHeader = ['header', 'Location: /login'];
        $actualHeaders = $this->response->getHeaders();
        $this->assertSame($expectedRedirectHeader, $actualHeaders[3]);
        $this->assertSame('rememberme', $actualHeaders[0][1]);
        $this->assertSame('', $actualHeaders[0][2]);
        $this->assertTrue($actualHeaders[0][7]);
        $this->assertSame('remembermesession', $actualHeaders[1][1]);
        $this->assertSame('', $actualHeaders[1][2]);
        $this->assertTrue($actualHeaders[1][7]);
        $this->assertSame('remembermetoken', $actualHeaders[2][1]);
        $this->assertSame('', $actualHeaders[2][2]);
        $this->assertTrue($actualHeaders[2][7]);
        $this->assertEmpty($this->request->session);
    }

    /**
     ** @runInSeparateProcess
     **/
    public function testRegisterFormWhenLoggedIn()
    {
        $this->loginUser();
        $response = $this->controller->loginForm($this->recaptcha);
        $expectedHeaders = [['header', 'Location: /']];
        $this->assertSame($expectedHeaders, $response->getHeaders());
        $this->assertSame('You are already logged in!', $this->request->session['flash']['message']);
    }

    /**
     ** @runInSeparateProcess
     **/
    public function testRegisterFormView()
    {
        $this->recaptcha->shouldReceive('getRecaptchaKey');
        $this->response = $this->controller->registerForm($this->recaptcha);
        $responseVars = $this->response->getVars();
        $expectedPageTitle = 'Register';
        $expectedMetaDescription = 'Register';
        $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
        $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
    }

    /**
     ** @runInSeparateProcess
     **/
    public function testRegisterConfirmWhenLoggedIn()
    {
        $this->loginUser();
        $response = $this->controller->registerConfirm();
        $expectedHeaders = [['header', 'Location: /']];
        $this->assertSame($expectedHeaders, $response->getHeaders());
        $this->assertSame('You are already logged in!', $this->request->session['flash']['message']);
    }

    /**
     ** @runInSeparateProcess
     **/
    public function testRegisterConfirmView()
    {
        $this->response = $this->controller->registerConfirm();
        $responseVars = $this->response->getVars();
        $expectedPageTitle = 'Confirm Your Email';
        $expectedMetaDescription = 'Please confirm your email address to complete your registration';
        $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
        $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
    }

    /**
     ** @runInSeparateProcess
     **/
    public function testRegisterActivateWhenLoggedIn()
    {
        $this->loginUser();
        $response = $this->controller->registerActivate($this->users);
        $expectedHeaders = [['header', 'Location: /']];
        $this->assertSame($expectedHeaders, $response->getHeaders());
        $this->assertSame('You are already logged in!', $this->request->session['flash']['message']);
    }

    /**
     ** @runInSeparateProcess
     **/
    public function testRegisterActivateWhenNoToken()
    {
        $response = $this->controller->registerActivate($this->users);
        $expectedHeaders = [['header', 'Location: /register']];
        $this->assertSame($expectedHeaders, $response->getHeaders());
        $this->assertSame('Your registration token was missing. Please start the registration process again.', $this->request->session['flash']['message']);
    }

    /**
     ** @runInSeparateProcess
     **/
    public function testRegisterActivateWhenTokenNotProcessed()
    {
        $email = 'johndoe@example.com';
        $invalidToken = 'invalid-token';
        $this->users->shouldReceive('findByToken')->with($invalidToken)->andReturn(null);
        $this->request->get['token'] = $invalidToken;
        $response = $this->controller->registerActivate($this->users);
        $expectedHeaders = [['header', 'Location: /register']];
        $this->assertSame($expectedHeaders, $response->getHeaders());
        $this->assertSame('We could not activate your free membership. Please start the registration process again.', $this->request->session['flash']['message']);
    }

    /**
     ** @runInSeparateProcess
     **/
    public function testRegisterActivateNew()
    {
        $listId = 1;
        $userId = 10;
        $email = 'johndoe@example.com';
        $validToken = 'valid-token';
        $user = [
            'user_id' => $userId,
            'email' => $email,
            'given_name' => 'John',
            'token' => $validToken,
        ];
        $this->users->shouldReceive('findByToken')->with($validToken)->andReturn($user);
        $this->users->shouldReceive('activate')->with($userId)->andReturn(1);
        $this->users->shouldReceive('findSubscriber')->with($listId, $userId)->andReturn(null);
        $this->users->shouldReceive('createSubscriber')->with($listId, $userId)->andReturn(1);
        $this->request->get['token'] = $validToken;
        $response = $this->controller->registerActivate($this->users);
        $expectedHeaders = [['header', 'Location: /thanks-for-registering']];
        $this->assertSame($expectedHeaders, $response->getHeaders());
    }

    /**
     ** @runInSeparateProcess
     **/
    public function testRegisterActivateExisting()
    {
        $listId = 1;
        $userId = 10;
        $email = 'johndoe@example.com';
        $validToken = 'valid-token';
        $user = [
            'user_id' => $userId,
            'email' => $email,
            'given_name' => 'John',
            'token' => $validToken,
        ];
        $subscriber = [
            'list_id' => $listId,
            'user_id' => $userId,
            'subscriber_status_id' => 1,
        ];
        $this->users->shouldReceive('findByToken')->with($validToken)->andReturn($user);
        $this->users->shouldReceive('activate')->with($userId)->andReturn(1);
        $this->users->shouldReceive('findSubscriber')->with($listId, $userId)->andReturn($subscriber);
        $this->users->shouldReceive('updateSubscriber')->with($listId, $userId, 1)->andReturn(1);
        $this->request->get['token'] = $validToken;
        $response = $this->controller->registerActivate($this->users);
        $expectedHeaders = [['header', 'Location: /thanks-for-registering']];
        $this->assertSame($expectedHeaders, $response->getHeaders());
    }

    /**
     * @runInSeparateProcess
     */
    public function testRegisterThanks()
    {
        $this->loginUser();
        $response = $this->controller->registerThanks();
        $responseVars = $this->response->getVars();
        $expectedPageTitle = 'Thanks for joining Cube Shack';
        $expectedMetaDescription = 'Thanks for joining the Cube Shack website.';
        $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
        $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
    }

    private function loginUser()
    {
        $email = 'johndoe@example.com';
        $password = 'secretshh';
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $user = [
            'user_id' => 10,
            'email' => $email,
            'given_name' => 'John',
            'password' => $passwordHash,
            'admin' => 0,
        ];
        $this->request->loginUser($user['email']);
        $this->request->user = $user;
    }
}
