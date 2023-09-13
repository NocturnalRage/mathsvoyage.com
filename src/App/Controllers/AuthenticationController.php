<?php

namespace App\Controllers;

use App\Emails\ActivateMembershipEmail;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Framework\Recaptcha\RecaptchaClient;

class AuthenticationController extends Controller
{
    public function loginForm(RecaptchaClient $recaptcha)
    {
        if ($this->loggedIn()) {
            return $this->redirectToHomePage('You are already logged in!');
        }

        $this->response->setVars([
            'pageTitle' => 'Login',
            'metaDescription' => 'Login to my website.',
            'activeLink' => 'Login',
            'recaptchaKey' => $recaptcha->getRecaptchaKey(),
        ]);
        $this->addSessionVar('errors');
        $this->addSessionVar('formVars');
        $this->addSessionVar('flash');

        return $this->response;
    }

    public function loginValidate(
        UserRepository $users,
        RecaptchaClient $recaptcha
    ) {
        if ($this->loggedIn()) {
            return $this->redirectToHomePage('You are already logged in!');
        }

        if ($this->recaptchaInvalid($recaptcha)) {
            return $this->redirectToLoginForm();
        }

        $this->request->validate([
            'email' => ['required', 'min:4', 'max:100', 'email'],
            'loginPassword' => ['required', 'min:6', 'max:100'],
        ]);

        $user = $users->findByEmail($this->request->post['email']);
        if (! $user) {
            return $this->redirectToLoginFormWithFailedLoginMessage();
        }
        if (! $user['activated_at']) {
            $this->request->flash('You must confirm your email address before you can login. An email has been sent with instructions. You can register again to receive a new email.', 'danger');

            return $this->redirectToRegisterForm();
        }

        if (! password_verify(
            $this->request->post['loginPassword'],
            $user['password']
        )
        ) {
            return $this->redirectToLoginFormWithFailedLoginMessage();
        }

        // Log the user in
        $this->request->session['loginEmail'] = $user['email'];
        $userId = $user['user_id'];
        $users->updateLastLogin($userId);

        if (isset($this->request->post['rememberme'])) {
            if ($this->request->post['rememberme'] == 'y') {
                $session = bin2hex(random_bytes(32));
                $token = bin2hex(random_bytes(32));
                $tokenHash = password_hash($token, PASSWORD_DEFAULT);
                $users->insertRememberMe(
                    $userId,
                    $session,
                    $tokenHash
                );
                $this->response->setCookie('rememberme', $userId, time() + 60 * 60 * 24 * 365, null, null, null, true);
                $this->response->setCookie('remembermesession', $session, time() + 60 * 60 * 24 * 365, null, null, null, true);
                $this->response->setCookie('remembermetoken', $token, time() + 60 * 60 * 24 * 365, null, null, null, true);
            }
        }

        $this->request->flash('You are now logged in', 'success');
        if (isset($this->request->session['redirect'])) {
            $this->response->redirect($this->request->session['redirect']);
        } else {
            $this->response->redirect('/');
        }

        return $this->response;
    }

    public function logout(UserRepository $users)
    {
        if (! $this->loggedIn()) {
            return $this->redirectToHomePage('You are already logged out!');
        }

        $users->deleteRememberMe($this->request->user['user_id']);
        $this->deleteCookies();
        unset($this->request->session['loginEmail']);
        session_destroy();

        $this->response->redirect('/login');

        return $this->response;
    }

    public function registerForm(RecaptchaClient $recaptcha)
    {
        if ($this->loggedIn()) {
            return $this->redirectToHomePage('You are already logged in!');
        }

        $this->response->setVars([
            'pageTitle' => 'Register',
            'metaDescription' => 'Register',
            'activeLink' => 'Register',
            'recaptchaKey' => $recaptcha->getRecaptchaKey(),
        ]);
        $this->addSessionVar('errors');
        $this->addSessionVar('formVars');
        $this->addSessionVar('flash');

        return $this->response;
    }

    public function registerValidate(
        UserRepository $users,
        ActivateMembershipEmail $activateMembershipEmail,
        RecaptchaClient $recaptcha
    ) {
        if ($this->loggedIn()) {
            return $this->redirectToHomePage('You are already logged in!');
        }

        if ($this->recaptchaInvalid($recaptcha)) {
            return $this->redirectToRegisterForm();
        }

        $this->request->validate([
            'givenName' => ['required', 'max:100'],
            'familyName' => ['required', 'max:100'],
            'email' => ['required', 'max:100', 'email'],
            'loginPassword' => ['required', 'min:6', 'max:100'],
        ]);

        $user = $users->findByEmail($this->request->post['email']);
        if (! $user) {
            $userId = $users->create(
                $this->request->post['givenName'],
                $this->request->post['familyName'],
                $this->request->post['email'],
                $this->request->post['loginPassword']
            );
        } elseif (is_null($user['activated_at'])) {
            if ($this->previouslyBounced($user['email_status_id'])) {
                $this->request->flash('We have already sent an activation email to this address and it bounced. We suggest trying a different email address.');
                $this->request->session['errors'] = ['email' => 'This email has bounced. Please use a different email address'];

                return $this->redirectToRegisterForm();
            }
            $userId = $user['user_id'];
            $users->update(
                $userId,
                $this->request->post['givenName'],
                $this->request->post['familyName'],
                $this->request->post['email'],
            );
            $users->updatePassword(
                $userId,
                $this->request->post['loginPassword']
            );

        } else {
            $this->request->flash('Email already active. Please use a different email or login.');
            $this->request->session['errors'] = ['email' => 'Email already registered'];

            return $this->redirectToRegisterForm();
        }
        $user = $users->find($userId);
        $mailInfo = [
            'requestScheme' => $this->request->server['REQUEST_SCHEME'],
            'serverName' => $this->request->server['SERVER_NAME'],
            'token' => $user['token'],
            'givenName' => $user['given_name'],
            'toEmail' => $user['email'],
        ];
        $activateMembershipEmail->sendEmail($mailInfo);

        return $this->redirectToConfirmEmailPage();
    }

    public function registerConfirm()
    {
        if ($this->loggedIn()) {
            return $this->redirectToHomePage('You are already logged in!');
        }

        $this->response->setVars([
            'pageTitle' => 'Confirm Your Email',
            'metaDescription' => 'Please confirm your email address to complete your registration',
            'activeLink' => 'Register',
            'registeredEmail' => $this->request->get['email'] ?? 'the address you entered',
        ]);

        return $this->response;
    }

    public function registerActivate(UserRepository $users)
    {
        if ($this->loggedIn()) {
            return $this->redirectToHomePage('You are already logged in!');
        }

        if ($this->noTokenReceived()) {
            $this->request->flash('Your registration token was missing. Please start the registration process again.', 'danger');

            return $this->redirectToRegisterForm();
        }

        if (! $this->tokenProcessed($users)) {
            $this->request->flash('We could not activate your free membership. Please start the registration process again.', 'danger');

            return $this->redirectToRegisterForm();
        }

        $this->response->redirect('/thanks-for-registering');

        return $this->response;
    }

    public function registerThanks()
    {
        $this->response->setVars([
            'pageTitle' => 'Thanks for joining Cube Shack',
            'metaDescription' => 'Thanks for joining the Cube Shack website.',
            'activeLink' => 'Register',
        ]);

        return $this->response;
    }

    public function profile()
    {
        if (! $this->loggedIn()) {
            return $this->redirectToLoginForm();
        }
        $this->request->user['memberSince'] = Carbon::createFromFormat('Y-m-d H:i:s', $this->request->user['created_at'])->diffForHumans();
        $this->response->setVars([
            'pageTitle' => 'My Profile',
            'metaDescription' => 'My account information',
            'activeLink' => 'Profile',
        ]);
        $this->addSessionVar('flash');

        return $this->response;
    }

    public function profileEdit(RecaptchaClient $recaptcha)
    {
        if (! $this->loggedIn()) {
            return $this->redirectToLoginForm();
        }
        if (! isset($this->request->session['formVars'])) {
            $this->request->session['formVars'] = $this->request->user;
        }
        $this->response->setVars([
            'pageTitle' => 'Edit Profile',
            'metaDescription' => 'Edit your profile.',
            'activeLink' => 'Profile',
            'recaptchaKey' => $recaptcha->getRecaptchaKey(),
        ]);
        $this->addSessionVar('errors');
        $this->addSessionVar('formVars');
        $this->addSessionVar('flash');

        return $this->response;
    }

    public function profileUpdate(
        UserRepository $users,
        ActivateMembershipEmail $activateMembershipEmail,
        RecaptchaClient $recaptcha
    ) {
        if (! $this->loggedIn()) {
            return $this->redirectToLoginForm();
        }

        if ($this->recaptchaInvalid($recaptcha)) {
            return $this->redirectToRegisterForm();
        }

        $userId = $this->request->user['user_id'];

        $this->request->validate([
            'given_name' => ['required', 'max:100'],
            'family_name' => ['required', 'max:100'],
            'email' => ['required', 'max:100', 'email'],
        ]);

        $user = $users->findByEmail($this->request->post['email']);
        if (! $user or $user['user_id'] == $userId) {
            $userId = $users->update(
                $userId,
                $this->request->post['given_name'],
                $this->request->post['family_name'],
                $this->request->post['email']
            );
            // Login with potentially new email
            $this->request->session['loginEmail'] = $this->request->post['email'];

            return $this->redirectToProfilePage();
        }
        $this->request->flash('Email already active. Please use a different email or login.');
        $this->request->session['errors'] = ['email' => 'Email already registered'];

        return $this->redirectToProfileEdit();
    }

    private function deleteCookies()
    {
        $this->response->setCookie('rememberme', '', time() - 3600, null, null, null, true);
        $this->response->setCookie('remembermesession', '', time() - 3600, null, null, null, true);
        $this->response->setCookie('remembermetoken', '', time() - 3600, null, null, null, true);
    }

    private function redirectToHomePage($message)
    {
        $this->request->flash($message, 'warning');
        $this->response->redirect('/');

        return $this->response;
    }

    private function recaptchaInvalid($recaptcha)
    {
        if (empty($this->request->post['g-recaptcha-response'])) {
            $this->request->flash('No validation provided. Please ensure you are human!', 'danger');

            return true;
        }
        $expectedRecaptchaAction = 'loginwithversion3';
        if (! $recaptcha->verified(
            $this->request->server['SERVER_NAME'],
            $expectedRecaptchaAction,
            $this->request->post['g-recaptcha-response'],
            $this->request->server['REMOTE_ADDR']
        )) {
            $this->request->flash('Could not validate your request. Please ensure you are human!', 'danger');

            return true;
        }

        return false;
    }

    private function redirectToLoginForm()
    {
        $this->request->session['formVars'] = $this->request->post;
        $this->response->redirect('/login');

        return $this->response;
    }

    private function redirectToLoginFormWithFailedLoginMessage()
    {
        $this->request->session['formVars'] = $this->request->post;
        $this->request->flash('The email and password do not match. Login failed.', 'danger');
        $this->response->redirect('/login');

        return $this->response;
    }

    private function redirectToRegisterForm()
    {
        $this->request->session['formVars'] = $this->request->post;
        $this->response->redirect('/register');

        return $this->response;
    }

    private function redirectToConfirmEmailPage()
    {
        $location = '/confirm-registration?email='.urlencode($this->request->post['email']);
        $this->response->redirect($location);

        return $this->response;
    }

    private function redirectToProfileEdit()
    {
        $this->request->session['formVars'] = $this->request->post;
        $this->response->redirect('/profile/edit');

        return $this->response;
    }

    private function redirectToProfilePage()
    {
        $this->response->redirect('/profile');

        return $this->response;
    }

    private function previouslyBounced($emailStatusId)
    {
        return $emailStatusId == 1 ? false : true;
    }

    private function noTokenReceived()
    {
        if (isset($this->request->get['token'])) {
            return false;
        } else {
            return true;
        }
    }

    private function tokenProcessed($users)
    {
        $user = $users->findByToken($this->request->get['token']);
        if (! $user) {
            return false;
        }
        $rowsUpdated = $users->activate($user['user_id']);
        if ($rowsUpdated != 1) {
            return false;
        }
        $_SESSION['loginEmail'] = $user['email'];
        $this->request->setLoginStatus();
        $this->subscribeToNewsletter($users, $user['user_id']);

        return true;
    }

    private function subscribeToNewsletter($users, $userId)
    {
        $listId = 1;
        $activeStatus = 1;
        $subscriber = $users->findSubscriber($listId, $userId);
        if (! $subscriber) {
            $users->createSubscriber($listId, $userId);
        } else {
            $users->updateSubscriber($listId, $userId, $activeStatus);
        }
    }
}
