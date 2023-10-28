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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {

    $router->post('/student', 'EMController@register_student');
    $router->post('/student_login', 'EMController@login');
    $router->patch('/student/{id}', 'EMController@update_student');
    $router->delete('/student/{id}', 'EMController@delete_Student');
    $router->get('/student/{id}', 'EMController@get_student');
    $router->get('/student', 'EMController@list_students');
    $router->post('/create_user_and_project','EMController@create_user_and_project');

    $router->post('/student_qb', 'QBController@register_student');
    $router->post('/student_login_qb', 'QBController@login');
    $router->patch('/student_qb/{id}', 'QBController@update_student');
    $router->delete('/student_qb/{id}', 'QBController@delete_Student');
    $router->get('/student_qb/{id}', 'QBController@get_student');
    $router->get('/student_qb', 'QBController@list_students');
    $router->post('/create_user_and_project_qb','QBController@create_user_and_project');

    $router->get('/other_controller_data','OtherController@other_controller_data');

});
