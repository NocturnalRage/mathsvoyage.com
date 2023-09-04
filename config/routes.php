<?php

return [
    ['GET', '/page-not-found', 'PageNotFoundController@index', 'PageNotFoundIndex'],
    ['GET', '/', 'StaticPageController@home', 'StaticPageHome'],
    ['GET', '/login', 'AuthenticationController@loginForm', 'AuthenticationLoginForm'],
    ['POST', '/login', 'AuthenticationController@loginValidate', 'AuthenticationLoginValidate'],
    ['POST', '/logout', 'AuthenticationController@logout', 'AuthenticationLogout'],
    ['GET', '/register', 'AuthenticationController@registerForm', 'AuthenticationRegisterForm'],
    ['POST', '/register', 'AuthenticationController@registerValidate', 'AuthenticationRegisterValidate'],
    ['GET', '/confirm-registration', 'AuthenticationController@registerConfirm', 'AuthenticationRegisterConfirm'],
    ['GET', '/activate-free-membership', 'AuthenticationController@registerActivate', 'AuthenticationRegisterActivate'],
    ['GET', '/thanks-for-registering', 'AuthenticationController@registerThanks', 'AuthenticationRegisterThanks'],
    ['GET', '/profile', 'AuthenticationController@profile', 'AuthenticationProfile'],
    ['GET', '/profile/edit', 'AuthenticationController@profileEdit', 'AuthenticationProfileEdit'],
    ['PATCH', '/profile/edit', 'AuthenticationController@profileUpdate', 'AuthenticationProfileUpdate'],
];
