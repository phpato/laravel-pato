<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/citas',"CitaController@getCitas");
Route::post('/cita',"CitaController@store");
Route::put('/cita',"CitaController@update");
Route::delete('/cita/{id}',"CitaController@destroy");
Route::post('/evaluar_cita',"CitaController@evaluarCita");
