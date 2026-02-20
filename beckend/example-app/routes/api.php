<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\COntrollers\ProjectController;
use App\Http\COntrollers\RegisterController;
use App\Http\COntrollers\LoginController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//Users
Route::get('/users',[UserController::class,'index']);

//Projects
Route::get('/projects',[ProjectController::class,'index']);
Route::post('/projects/add',[ProjectController::class,'store']);
Route::post('/projects/edit',[ProjectController::class,'update']);
Route::get('projects/destroy/{project}',[ProjectController::class,'destroy']);

//Авторизация
Route::post('/login',[LoginController::class,'login']);


//Регистрация
Route::post('/registred',[RegisterController::class,'registred']);