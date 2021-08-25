<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('register_user', 'AuthController@registerUser');
    $router->post('register_admin', 'AuthController@registerAdmin');
    $router->post('login', 'AuthController@authenticate');
    $router->post('activate_user', 'AuthController@activateUser');

    $router->group(['prefix' => 'invitation', 'middleware' => ['auth']], function () use ($router) {
        $router->post('create', 'InvitationController@create');
    });

    $router->group(['prefix' => 'profile', 'middleware' => ['auth']], function () use ($router) {
        $router->post('update', 'ProfileController@update');
    });
});
