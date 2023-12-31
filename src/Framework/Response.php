<?php
/**
 * This file is part of "Modernizing Legacy Applications in PHP".
 *
 * @copyright 2014-2016 Paul M. Jones <pmjones88@gmail.com>
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

namespace Framework;

/**
 * Encapsulates a plain old PHP response.
 */
class Response
{
    /**
     * A base path prefix for view files.
     *
     * @var string
     */
    protected $base;

    /**
     * The buffer for HTTP header calls.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * The callable and arguments to be invoked with `call_user_func_array()`
     * as the last step in the `send()` process.
     *
     * @var array
     */
    protected $last_call;

    /**
     * Variables to extract into the view scope.
     *
     * @var array
     */
    protected $vars = [];

    /**
     * A view file to require in its own scope.
     *
     * @var string
     */
    protected $view;

    /**
     * Should we render a view or redirect to another page.
     * true means we render, false we redirect
     *
     * @var bool
     */
    protected $render = true;

    /**
     * Constructor.
     *
     * @param  string  $base A base path prefix for view files.
     */
    public function __construct($base = null)
    {
        $this->setBase($base);
    }

    /**
     * Sets the base path prefix for view files.
     *
     * @param  string  $view The view file.
     */
    public function setBase($base)
    {
        $this->base = $base;
    }

    /**
     * Gets the base path prefix for view files.
     *
     * @return string
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * Sets the view file.
     *
     * @param  string  $view The view file.
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    /**
     * Gets the view file.
     *
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Returns the full path to the view.
     *
     * @return string
     */
    public function getViewPath()
    {
        if (! $this->base) {
            return $this->view;
        }

        return rtrim($this->base, DIRECTORY_SEPARATOR)
             .DIRECTORY_SEPARATOR
             .ltrim($this->view, DIRECTORY_SEPARATOR);
    }

    /**
     * Sets the variables to be extracted into the view scope.
     *
     * @param  array  $vars The variables to be extracted into the view scope.
     */
    public function setVars(array $vars)
    {
        //        unset($vars['this']);
        $this->vars = $vars;
    }

    /**
     * Adds to the variables to be extracted into the view scope.
     *
     * @param  array  $vars The variables to be extracted into the view scope.
     */
    public function addVars(array $vars)
    {
        $this->vars = array_merge($this->vars, $vars);
    }

    /**
     * Gets the variables to be extracted into the view scope.
     *
     * @return array
     */
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * Sets the callable to be invoked with `call_user_func_array()` as the
     * last step in the `send()` process; extra arguments are passed to the
     * call.
     *
     * @param  callable  $func The callable to be invoked.
     */
    public function setLastCall($func)
    {
        $this->last_call = func_get_args();
    }

    /**
     * Gets the callable to be invoked with `call_user_func_array()` as the
     * last step in the `send()` process.
     *
     * @return callable
     */
    public function getLastCall()
    {
        return $this->last_call;
    }

    /**
     * Escapes output for HTML tag contents, or for a **quoted** HTML
     * attribute. Unquoted attributes are not made safe by using this method,
     * nor is non-HTML content.
     *
     * @param  string  $string The unescaped string.
     * @return string
     */
    public function esc($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Buffers a call to `header()`.
     */
    public function header()
    {
        $args = func_get_args();
        array_unshift($args, 'header');
        $this->headers[] = $args;
    }

    /**
     * Sets header for a redirect
     * Updates render to false
     * so that only a redirect occurs
     */
    public function redirect($url)
    {
        $this->headers[] = ['header', 'Location: '.$url];
        $this->render = false;
    }

    /**
     * Buffers a call to `setcookie()`.
     *
     * @return bool
     */
    public function setCookie()
    {
        $args = func_get_args();
        array_unshift($args, 'setcookie');
        $this->headers[] = $args;

        return true;
    }

    /**
     * Buffers a call to `setrawcookie()`.
     *
     * @return bool
     */
    public function setRawCookie()
    {
        $args = func_get_args();
        array_unshift($args, 'setrawcookie');
        $this->headers[] = $args;

        return true;
    }

    /**
     * Returns the buffer for HTTP header calls.
     *
     * @return bool
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Outputs the buffered headers, buffered view, and calls the user function.
     */
    public function send()
    {
        if ($this->render) {
            $buffered_output = $this->requireView();
            $this->sendHeaders();
            echo $buffered_output;
            $this->invokeLastCall();
        } else {
            $this->sendHeaders();
        }
    }

    /**
     * Requires the view in its own scope with extracted variables and returns
     * the buffered output.
     *
     * @return string
     */
    public function requireView()
    {
        if (! $this->view) {
            return '';
        }

        extract($this->vars);
        ob_start();
        require $this->getViewPath();

        return ob_get_clean();
    }

    /**
     * Outputs the buffered calls to `header`, `setcookie`, etc.
     */
    public function sendHeaders()
    {
        foreach ($this->headers as $args) {
            $func = array_shift($args);
            call_user_func_array($func, $args);
        }
    }

    /**
     * Invokes `$this->call`.
     */
    public function invokeLastCall()
    {
        if (! $this->last_call) {
            return;
        }
        $args = $this->last_call;
        $func = array_shift($args);
        call_user_func_array($func, $args);
    }

    public function crsfToken()
    {
        echo '<input type="hidden" name="crsfToken" value="'.$this->esc($this->vars['crsfToken']).'" />';
    }

    public function formMethod($method)
    {
        echo '<input type="hidden" name="_method" value="'.$method.'" />';
    }
}
