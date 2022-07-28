<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Models\User;
use \App\Http\Controllers\UserController;
use \App\Http\Controllers\TimeRecordController;
use \App\Http\Controllers\ProjectController;
use \App\Http\Controllers\ClientController;
use \App\Http\Controllers\ProjectStageController;
use \App\Http\Controllers\DepartmentController;

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


Route::post('/login', [UserController::class, 'login']);

Route::group(['middleware' => ['auth:api', 'checkSoftDeletedUser']], function () {
    Route::get('/user', [UserController::class, 'authUser']);
    Route::get('/logout', [UserController::class, 'logout']);

    Route::get('/timeRecords/date/{date}', [TimeRecordController::class, 'getTimeRecordsByDate']);
    Route::put('/timeRecord/create', [TimeRecordController::class, 'create']);
    Route::delete('/timeRecord/{id}/delete', [TimeRecordController::class, 'destroy']);

    Route::get('/projects', [ProjectController::class, 'index']);

    // Admin middleware
    Route::group(['middleware' => ['admin']], function () { 
        Route::delete('/user/{id}/delete', [UserController::class, 'softDelete']);
        Route::post('/user/{userId}/restore', [UserController::class, 'restore']);
        Route::put('/user/create', [UserController::class, 'register']);
        Route::get('/user/{id}', [UserController::class, 'get']);
        Route::get('/users', [UserController::class, 'index']);
    
        Route::put('/project/create', [ProjectController::class, 'create']);
        Route::delete('/project/{id}/delete', [ProjectController::class, 'destroy']);
        Route::get('/project/{id}', [ProjectController::class, 'get']);
        Route::get('/project/{id}/analytics', [ProjectController::class, 'getAnalytics']);

        Route::put('/projectStage/create', [ProjectStageController::class, 'create']);
        Route::delete('/projectStage/{id}/delete', [ProjectStageController::class, 'destroy']);

        Route::get('/clients', [ClientController::class, 'index']);
        Route::put('/client/create', [ClientController::class, 'create']);
        Route::delete('/client/{id}/delete', [ClientController::class, 'destroy']);

        Route::get('/departments', [DepartmentController::class, 'index']);
        Route::put('/department/create', [DepartmentController::class, 'create']);
        Route::post('/department/{id}/addUsers', [DepartmentController::class, 'addUsers']);
        Route::post('/department/{id}/addClients', [DepartmentController::class, 'addClients']);
        Route::delete('/department/{id}/delete', [DepartmentController::class, 'destroy']);
    });
});
