<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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


Route::get('/test', function () {
    return response()->json(['status' => 'API Funcionando']);
});

Route::post('login', 'Auth\ApiAuthController@login');
Route::post('logout', 'Auth\ApiAuthController@logout');
Route::get('export-library', 'ExportController@exportData');
Route::get('me', 'Auth\ApiAuthController@me');

Route::group(['middleware' => 'auth.jwt'], function () {
    Route::resource('authors', 'AuthorController', [
        'except' => ['create', 'edit']
    ]);
    Route::resource('books', 'BookController', [
        'except' => ['create', 'edit']
    ]);
});

