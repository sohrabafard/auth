<?php

use App\Http\Controllers\Api\v1\AuthController;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Middleware\CheckScopes;

Route::group(['prefix' => 'v1'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::get('logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:api', 'scope:read-data'])->get('/read', 'DataController@read');
Route::middleware(['auth:api', 'role:admin', 'scope:write-data'])->get('/write', 'DataController@write');