

<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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
$router->get('/version', function () use ($router) {
    return $router->app->version();
});

//Users
$router->get('/users', "UserController@getUsers");
$router->post('/users', "UserController@createUser");
$router->put('/users/{user_id}', "UserController@updateUser");
$router->delete('/users/{user_id}', "UserController@deleteUser");

//Messages
$router->get('/users/{user_id}/messages', "MessageController@getMessages");
$router->post('/users/{user_id}/messages', "MessageController@createMessage");
$router->delete('/users/{user_id}/messages/{message_id}', "MessageController@DeleteMessage");

//Conversations
$router->get('/users/{user_id1}/conversations/{user_id2}', "MessageController@getMessagesBetweenUsers");