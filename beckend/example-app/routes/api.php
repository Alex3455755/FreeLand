<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\COntrollers\ProjectController;
use App\Http\COntrollers\RegisterController;
use App\Http\COntrollers\LoginController;
use App\Http\COntrollers\CategoryController;
use App\Http\COntrollers\CommentController;
use App\Http\COntrollers\PaymentController;
use App\Http\COntrollers\AuthController;
use App\Http\COntrollers\MessageController;



Route::options('/{any}', function() {
    return response()->json([], 200);
})->where('any', '.*');

Route::get('/test', function() {
    return response()->json(['message' => 'API работает']);
});


//Авторизация
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    
    Route::post('/logout', function (Request $request) {
    // Удаляем все токены пользователя
    if ($request->user()) {
        $request->user()->tokens()->delete();
        
        // Если используете сессии
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
    
    return response()->json(['success' => true, 'message' => 'Выход выполнен']);
})->middleware('auth:sanctum');
});
Route::post('/login', [LoginController::class, 'login'])
    ->middleware([\Illuminate\Session\Middleware\StartSession::class]);
//Регистрация
Route::post('/registred',[RegisterController::class,'registred']);

//Users
Route::get('/users',[UserController::class,'index']);
Route::post('/users/add',[RegisterController::class,'registred']);
Route::get('/users/{id}',[UserController::class,'show']);
Route::post('/users/edit',[UserController::class,'update']);
Route::get('/users/destroy/{user}',[UserController::class,'destroy']);

//Projects
Route::get('/projects',[ProjectController::class,'index']);
Route::post('/projects/add',[ProjectController::class,'store']);
Route::post('/projects/edit',[ProjectController::class,'update']);
Route::get('/projects/{project}',[ProjectController::class,'show']);
Route::get('projects/destroy/{project}',[ProjectController::class,'destroy']);
Route::get('/my-projects/{user}', [ProjectController::class, 'myProjects']);

//Categories
Route::get('/categories',[CategoryController::class,'index']);
Route::post('/categories/add',[CategoryController::class,'store']);
Route::post('/categories/edit',[CategoryController::class,'update']);
Route::get('/categories/destroy/{categries}',[CategoryController::class,'destroy']);

//Comments
Route::get('/comments',[CommentController::class,'index']);
Route::post('/comments/add',[CommentController::class,'store']);
Route::post('/comments/edit',[CommentController::class,'update']);
Route::get('/comments/destroy/{comment}',[CommentController::class,'destroy']);

//Payments
Route::get('/payments',[PaymentController::class,'index']);
Route::post('/payments/add',[PaymentController::class,'store']);
Route::post('/payments/edit',[PaymentController::class,'update']);
Route::get('/payments/destroy/{payment}',[PaymentController::class,'destroy']);

//Messages
Route::get('/messages',[MessageController::class,'index']);
Route::post('/messages/add',[MessageController::class,'store']);
Route::post('/messages/edit',[MessageController::class,'update']);
Route::get('/messages/destroy/{payment}',[MessageController::class,'destroy']);