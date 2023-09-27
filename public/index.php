<?php

require dirname(__FILE__).'/../vendor/autoload.php';
require dirname(__FILE__).'/../config/services.php';

session_start();

// Load environment variables using dotenv
$dotenv = $di->get('Dotenv');
$dotenv->load();

// Load request, response, and UserRepository objects
$request = $di->get('Request');
$response = $di->get('Response');
$users = $di->get('UserRepository');
if ($request->loggedIn) {
    $request->user = $users->findByEmail($request->session['loginEmail']);
} else {
    if ($request->rememberMeCookiesSet()) {
        $rememberMeToken = $users->getRememberMe(
            $request->cookie['rememberme'],
            $request->cookie['remembermesession']
        );
        if (! is_null($rememberMeToken) && $request->authenticateRememberMeToken($rememberMeToken['token'])) {
            $request->user = $users->find($request->cookie['rememberme']);
            $request->loginUser($request->user['email']);
        }
    } else {
        $request->user = ['user_id' => 0];
    }
}
// Load router
$router = $di->newInstance('Router');
$matchRequestMethod = strtoupper($_POST['_method'] ?? $_SERVER['REQUEST_METHOD'] ?? 'GET');

// Validate CRSF if not GET
if ($matchRequestMethod != 'GET' && ! $request->crsfTokenIsValid()) {
    $request->session['flash'] = [
        'message' => 'Please perform these actions directly from our website',
        'type' => 'danger',
    ];
    $response->redirect($_SERVER['HTTP_REFERER']);
    $response->send();
} else {
    $match = $router->match(requestMethod: $matchRequestMethod);
    if (isset($match['params'])) {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        foreach ($match['params'] as $key => $param) {
            $request->request[$key] = $param;
            if ($requestMethod == 'POST') {
                $request->post[$key] = $param;
            } else {
                $request->get[$key] = $param;
            }
        }
    }

    if (! $match) {
        $match = [];
        $match['target'] = 'PageNotFoundController';
    }
    $controllerAndAction = explode('@', $match['target']);
    $controllerName = $controllerAndAction[0];
    if (count($controllerAndAction) == 1) {
        $action = 'index';
    } else {
        $action = $controllerAndAction[1];
    }
    $controller = $di->newInstance($controllerName);
    if (method_exists($controller, $action)) {
        $viewDir = substr($controllerName, 0, strrpos($controllerName, 'Controller'));
        $viewName = $viewDir.'/'.$action.'.html.php';
        $response->setView($viewName);
        try {
            $ref = new ReflectionClass('App\\Controllers\\'.$controllerName);
            $methods = $ref->getMethods();
            $injectedParams = [];
            foreach ($methods as $method) {
                if ($method->name == $action) {
                    $params = $method->getParameters();
                    foreach ($params as $param) {
                        if ($param->getType()) {
                            $diCheck = (basename(str_replace('\\', DIRECTORY_SEPARATOR, $param->getType()->getName())));
                            if ($di->has($diCheck)) {
                                $instance = $di->get($diCheck);
                                $injectedParams[] = $instance;
                            } else {
                                foreach ($match['params'] as $key => $value) {
                                    if ($key == $param->name) {
                                        $injectedParams[] = $value;
                                    }
                                }
                            }
                        } else {
                            foreach ($match['params'] as $key => $value) {
                                if ($key == $param->name) {
                                    $injectedParams[] = $value;
                                }
                            }
                        }
                    }
                }
            }
            $response = $controller->$action(...$injectedParams);
            $response->addVars([
                'crsfToken' => $request->createCrsfToken(),
                'loggedIn' => $request->loggedIn,
                'user' => $request->user,
                'isAdmin' => $request->isAdmin(),
            ]);
            $response->send();
        } catch (Framework\ValidationException $e) {
            $response->redirect($_SERVER['HTTP_REFERER']);
            $response->send();
        } catch (Framework\RepositoryNotFoundException $e) {
            $response->redirect('/page-not-found');
            $response->send();
        }
    } else {
        throw new \Exception("$controllerName does not support $action");
    }
}
