<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\COntrollers\ProjectController;
use App\Http\COntrollers\RegisterController;
use App\Http\COntrollers\LoginController;
use App\Http\COntrollers\CategoryController;
use App\Http\COntrollers\CommentController;

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

//Categories
Route::get('/categories',[CategoryController::class,'index']);
Route::post('/categories/add',[CategoryController::class,'store']);
Route::post('/categories/edit',[CategoryController::class,'update']);
Route::get('categories/destroy/{categries}',[CategoryController::class,'destroy']);

//Comments
Route::get('/comments',[CommentController::class,'index']);
Route::post('/comments/add',[CommentController::class,'store']);
Route::post('/comments/edit',[CommentController::class,'update']);
Route::get('comments/destroy/{categries}',[CommentController::class,'destroy']);


//Авторизация
Route::post('/login',[LoginController::class,'login']);


//Регистрация
Route::post('/registred',[RegisterController::class,'registred']);