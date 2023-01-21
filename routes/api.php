<?php

use App\Http\Controllers\Api\Admin\BookController as AdminBookController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
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

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('refresh', [AuthController::class, 'refreshToken']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('books', [BookController::class, 'index']);
    Route::get('books/{id}', [BookController::class, 'view']);

    // Admin Routes
    Route::group(['middleware' => ['admin'], 'prefix' => 'admin'], function () {
        Route::get('books', [AdminBookController::class, 'index']);
        Route::post('books', [AdminBookController::class, 'store']);
        Route::get('books/{id}', [AdminBookController::class, 'view']);
        Route::post('books/{id}', [AdminBookController::class, 'update']);
        Route::delete('books/{id}', [AdminBookController::class, 'destroy']);
    });
});
