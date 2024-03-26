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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::controller(NewUserController::class)->group(function () {
//     Route::post('register', 'addUser');
//     Route::post('login', 'loginUser');
//     Route::get('logout', 'logout');
//     Route::get('findall', 'findAllUsers');
//     Route::get('find/{id}', 'findUser');  This route will be used when we are not using session.
//     Route::get('find', 'findUser');
//     Route::put('update/{id}', 'updateUser');
//     Route::delete('delete/{id}', 'deleteUser');
// });


//For session
// Route::middleware(['web'])->group(function () {
//     Route::post('register', [NewUserController::class, 'addUser']);
//     Route::post('login', [NewUserController::class, 'loginUser']);
//     Route::get('logout', [NewUserController::class, 'logout']);
//     Route::get('findall', [NewUserController::class, 'findAllUsers']);
//     Route::get('find', [NewUserController::class, 'findUser']);
//     Route::put('update/{id}', [NewUserController::class, 'updateUser']);
//     Route::delete('delete/{id}', [NewUserController::class, 'deleteUser']);
// });


//For tokens

Route::post('register', [NewUserController::class, 'addUser']);
Route::post('login', [NewUserController::class, 'loginUser']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('logout', [NewUserController::class, 'logout']);
    Route::get('find', [NewUserController::class, 'findUser']);
    Route::put('update', [NewUserController::class, 'updateUser']);
    Route::delete('delete', [NewUserController::class, 'deleteUser']);
    Route::get('findall', [NewUserController::class, 'findAllUsers']);
});
