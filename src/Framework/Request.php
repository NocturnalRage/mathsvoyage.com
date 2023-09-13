<?php
/**
 * This file is part of "Modernizing Legacy Applications in PHP".
 *
 * @copyright 2014-2022 Paul M. Jones <pmjones88@gmail.com>
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

namespace Framework;

use DomainException;
use InvalidArgumentException;

/**
 * A data structure object to encapsulate superglobal references.
 *
 * This version of the Request object is for use on PHP 8.1 and later, but
 * should work back to as far as PHP 5.6 (or even earlier).
 */
class Request
{
    /**
     * A copy of $_COOKIE.
     *
     * @var array
     */
    public $cookie = [];

    /**
     * A copy of $_ENV.
     *
     * @var array
     */
    public $env = [];

    /**
     * A copy of $_FILES.
     *
     * @var array
     */
    public $files = [];

    /**
     * A copy of $_GET.
     *
     * @var array
     */
    public $get = [];

    /**
     * A copy of $_POST.
     *
     * @var array
     */
    public $post = [];

    /**
     * A copy of $_REQUEST.
     *
     * @var array
     */
    public $request = [];

    /**
     * A copy of $_SERVER.
     *
     * @var array
     */
    public $server = [];

    /**
     * Holds the current user informatoin.
     *
     * @var array
     */
    public $user;

    /**
     * Determines if a user is logged in.
     *
     * @var bool
     */
    public $loggedIn = false;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // mention the superglobals by name to invoke auto_globals_jit, thereby
        // forcing them to be populated; cf. <http://php.net/auto-globals-jit>.

        if (isset($_COOKIE)) {
            $this->cookie = $_COOKIE;
        }

        if (isset($_ENV)) {
            $this->env = $_ENV;
        }

        if (isset($_FILES)) {
            $this->files = $_FILES;
        }

        if (isset($_GET)) {
            $this->get = $_GET;
        }

        if (isset($_POST)) {
            $this->post = $_POST;
        }

        if (isset($_REQUEST)) {
            $this->request = $_REQUEST;
        }

        if (isset($_SERVER)) {
            $this->server = $_SERVER;
        }

        $this->setLoginStatus();
    }

    /**
     * Provides a magic **reference** to $_SESSION.
     *
     * @param  string  $property The property name; must be 'session'.
     * @return array A reference to $_SESSION.
     *
     * @throws InvalidArgumentException for any $name other than 'session'.
     * @throws DomainException when $_SESSION is not set.
     */
    public function &__get($name)
    {
        if ($name != 'session') {
            throw new InvalidArgumentException($name);
        }

        if (! isset($_SESSION)) {
            throw new DomainException('$_SESSION is not set');
        }

        return $_SESSION;
    }

    /**
     * Provides magic isset() for $_SESSION and the related property.
     *
     * @param  string  $name The property name; must be 'session'.
     * @return bool
     */
    public function __isset($name)
    {
        if ($name != 'session') {
            throw new InvalidArgumentException;
        }

        return isset($_SESSION);
    }

    /**
     * Provides magic unset() for $_SESSION; unsets both the property and the
     * superglobal.
     *
     * @param  string  $name The property name; must be 'session'.
     */
    public function __unset($name)
    {
        if ($name != 'session') {
            throw new InvalidArgumentException;
        }

        unset($_SESSION);
    }

    /**
     * Creates a crsf token for this session
     *
     * @return string
     * @return string The CRSF token.
     */
    public function createCrsfToken()
    {
        if (isset($this->session['crsfToken'])) {
            return $this->session['crsfToken'];
        }
        $this->session['crsfToken'] = bin2hex(random_bytes(32));

        return $this->session['crsfToken'];
    }

    /**
     * Check if a valid crsf token was posted
     *
     * @return string
     * @return book
     */
    public function crsfTokenIsValid()
    {
        // Validate the CRSF token
        if (
            empty($this->post['crsfToken']) ||
            empty($this->session['crsfToken']) ||
            $this->post['crsfToken'] != $this->session['crsfToken']
        ) {
            return false;
        } else {
            return true;
        }
    }

    public function setLoginStatus()
    {
        if ($this->loggedInThroughSession()) {
            $this->loggedIn = true;
        } else {
            $this->loggedIn = false;
        }

        return $this->loggedIn;
    }

    public function loginUser($email)
    {
        $this->session['loginEmail'] = $email;
        $this->setLoginStatus();
    }

    public function rememberMeCookiesSet()
    {
        return isset($this->cookie['rememberme']) &&
               isset($this->cookie['remembermesession']) &&
               isset($this->cookie['remembermetoken']);
    }

    public function authenticateRememberMeToken($token)
    {
        if (password_verify($this->cookie['remembermetoken'], $token)) {
            return true;
        }

        return false;
    }

    public function isAdmin()
    {
        if ($this->loggedIn) {
            return $this->user['admin'] == 1 ? true : false;
        }

        return false;
    }

    public function flash($message, $messageType = 'info')
    {
        $this->session['flash'] = [
            'message' => $message,
            'type' => $messageType,
        ];
    }

    /**
     * Validates request data
     *
     * @param  string  $name The property name; must be 'session'.
     */
    public function validate($validationData)
    {
        $errors = [];
        foreach ($validationData as $field => $validation) {
            foreach ($validation as $rule) {
                if ($rule == 'required') {
                    if (empty($this->request[$field])) {
                        $errors[$field] = 'Required field';
                        break;
                    }
                } elseif (substr($rule, 0, 4) == 'min:') {
                    $ruleAndMin = explode(':', $rule);
                    $min = intval($ruleAndMin[1]);
                    if (strlen($this->request[$field]) < $min) {
                        $errors[$field] = 'Too short. Cannot be less than '.$min.' characters.';
                        break;
                    }
                } elseif (substr($rule, 0, 4) == 'max:') {
                    $ruleAndMax = explode(':', $rule);
                    $max = intval($ruleAndMax[1]);
                    if (strlen($this->request[$field]) > $max) {
                        $errors[$field] = 'Too long. Cannot be more than '.$max.' characters.';
                        break;
                    }
                } elseif ($rule == 'email') {
                    if (filter_var($this->request[$field], FILTER_VALIDATE_EMAIL) === false) {
                        $errors[$field] = 'Not a valid email address';
                        break;
                    }
                } elseif ($rule == 'int') {
                    if (filter_var($this->request[$field], FILTER_VALIDATE_INT) === false) {
                        $errors[$field] = 'Not an integer';
                        break;
                    }
                }
            }
        }
        if (! empty($errors)) {
            $this->session['errors'] = $errors;
            $this->session['formVars'] = $this->request;
            $this->session['flash'] = [
                'message' => 'Please fix the displayed errors and resubmit',
                'type' => 'warning',
            ];
            throw new ValidationException('Validation Failed');
        }
    }

    private function loggedInThroughSession()
    {
        return isset($this->session['loginEmail']);
    }
}
