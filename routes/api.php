<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
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
// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/running-process', [UserController::class, 'runningProcess']);
    Route::get('/create-directory/{name}', [UserController::class, 'createDirectory']);
    Route::get('/create-file/{name}', [UserController::class, 'createFile']);
    Route::get('/list-of-directories/', [UserController::class, 'listOfDirectories']);
    Route::get('/list-of-files/', [UserController::class, 'listOfFiles']);
});
