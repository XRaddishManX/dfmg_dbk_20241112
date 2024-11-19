<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use app\Http\Controllers\UserController;
/*
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');*/

Route::post('/nuevousuario', [UserController::class, 'create']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/usuario', [UserController::class, 'getUser'])->middleware('auth:api');


