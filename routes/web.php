<?php

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="Economizza",
 *         version="1.0.0",
 *         description="Gestão de pagamentos"
 *     ),
 *     @OA\Server(
 *         url="http://localhost:8000",
 *         description="Servidor de Desenvolvimento"
 *     ),
 *     @OA\SecurityScheme(
 *         securityScheme="bearerAuth",
 *         type="http",
 *         scheme="bearer",
 *         bearerFormat="JWT"
 *     )
 * )
 */


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
$router->get('/', 'UserController@ini');

$router->group(['prefix' => 'api/v1'], function () use ($router) {

    $router->post('/user', 'UserController@store');
    $router->get('/users', 'UserController@showAll');
    $router->post('/login', 'UserController@login');

    $router->get('/meetings', 'MeetingController@index');
    $router->post('/meetings', 'MeetingController@store');

    $router->group(['middleware' => 'jwt'], function () use ($router) {

        $router->put('/users/{id}', 'UserController@update');
        $router->get('/user/{id}', 'UserController@show');
    });
});
