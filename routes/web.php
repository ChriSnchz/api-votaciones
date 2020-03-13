<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Clases
use App\Http\Middleware\ApiAuthMiddleware;

//Route::get('/', function () {return view('welcome');});

//Rutas del controlador de usuarios
Route::post('/api/register', 'UserController@register');
Route::post('/api/login', 'UserController@login');
Route::put('/api/user/update', 'UserController@update');
Route::post('/api/user/upload', 'UserController@upload')->middleware(ApiAuthMiddleware::class);
Route::get('/api/user/avatar/{filename}', 'UserController@getImage');
Route::get('/api/user/detail/{email}', 'UserController@detail');

//Rutas del controlador de elecciones
Route::apiResources([
    '/api/eleccion' => 'EleccionController',
    '/api/candidato' => 'CandidatoController',
    '/api/voto' => 'VotoController'
]);

Route::get('/api/elecciones/activas', 'EleccionController@activas');
Route::get('/api/elecciones/inactivas', 'EleccionController@inactivas');