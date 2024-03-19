<?php

use App\Http\Controllers\NewUserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(NewUserController::class)->group(function () {
    Route::post('register', 'addUser');
    Route::post('login', 'loginUser');
    Route::get('findAll', 'findAllUsers');
    Route::get('find/{id}', 'findUser');
    Route::put('update/{id}', 'updateUser');
    Route::delete('delete/{id}', 'deleteUser');
});
