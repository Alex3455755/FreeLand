<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
use App\Http\COntrollers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CategoryRequestController;
use App\Http\Controllers\AdminController;




Route::options('/{any}', function() {
    return response()->json([], 200);
})->where('any', '.*');

Route::get('/test', function() {
    return response()->json(['message' => 'API работает']);
});

Route::get('/commission-rates', [AdminController::class, 'getCommissionSettings']);

//Авторизация
Route::middleware(['auth:sanctum', 'not_banned'])->group(function () {
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
    });
});
Route::post('/login', [LoginController::class, 'login'])
    ->middleware([\Illuminate\Session\Middleware\StartSession::class]);
//Регистрация
Route::post('/registred',[RegisterController::class,'registred']);
Route::post('/verify-email-code',[RegisterController::class,'verifyEmailCode']);
Route::post('/resend-email-code',[RegisterController::class,'resendEmailCode']);

//Users
Route::get('/users',[UserController::class,'index']);
Route::post('/users/add',[RegisterController::class,'registred']);
Route::get('/users/{id}',[UserController::class,'show']);
Route::post('/users/edit',[UserController::class,'update']);
Route::get('/users/destroy/{user}',[UserController::class,'destroy']);

//Projects
Route::get('/projects',[ProjectController::class,'index']);
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
Route::get('/comments/user/{user}',[CommentController::class,'userComments']);

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


Route::middleware(['auth:sanctum', 'not_banned'])->group(function () {
    // Профиль
    Route::get('/profil/{user}', [ProfileController::class, 'index']);
    Route::post('/profile/update', [ProfileController::class, 'update']);
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);
    Route::post('/profile/deposit', [ProfileController::class, 'deposit']);
    Route::post('/profile/withdraw', [ProfileController::class, 'withdraw']);
    Route::post('/profile/send-money/{user}', [ProfileController::class, 'sendMoney']);
    Route::get('/profile/payments', [ProfileController::class, 'payments']);
});


Route::get('/reviews', [ReviewController::class, 'index']);
Route::post('/reviews', [ReviewController::class, 'store']);
Route::get('/reviews/latest', [ReviewController::class, 'getLatest']);
Route::get('/reviews/{id}', [ReviewController::class, 'show']);

// Маршруты для администратора (с middleware auth и admin)
Route::middleware(['auth:sanctum', 'not_banned', 'admin'])->group(function () {
    Route::get('/admin/users', [AdminController::class, 'users']);
    Route::patch('/admin/users/{user}/ban', [AdminController::class, 'setUserBanned']);
    Route::get('/admin/settings/commissions', [AdminController::class, 'getCommissionSettings']);
    Route::put('/admin/settings/commissions', [AdminController::class, 'updateCommissionSettings']);
    Route::get('/admin/statistics', [AdminController::class, 'statistics']);

    Route::get('/admin/reviews/pending', [ReviewController::class, 'getPending']);
    Route::get('/admin/reviews/stats', [ReviewController::class, 'getStats']);
    Route::post('/admin/reviews/search', [ReviewController::class, 'search']);
    Route::put('/admin/reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('/admin/reviews/{id}', [ReviewController::class, 'destroy']);
    Route::patch('/admin/reviews/{id}/approve', [ReviewController::class, 'approve']);
    Route::patch('/admin/reviews/{id}/reject', [ReviewController::class, 'reject']);

    // заявки на новые категории
    Route::get('/admin/category-requests/pending', [CategoryRequestController::class, 'pending']);
    Route::patch('/admin/category-requests/{categoryRequest}/approve', [CategoryRequestController::class, 'approve']);
    Route::patch('/admin/category-requests/{categoryRequest}/reject', [CategoryRequestController::class, 'reject']);
});

Route::middleware(['auth:sanctum', 'not_banned'])->group(function () {

    // Чаты
    Route::get('/chats/unread-count', [ChatController::class, 'unreadCount']);
    Route::get('/chats', [ChatController::class, 'index']);
    Route::post('/chats', [ChatController::class, 'store']);
    Route::get('/chats/{chat}', [ChatController::class, 'show']);
    Route::delete('/chats/{chat}', [ChatController::class, 'destroy']);

    // Сообщения в чате
    Route::post('/chats/{chat}/message', [ChatController::class, 'sendMessage']);

});

Route::middleware(['auth:sanctum', 'not_banned'])->group(function () {

    // заявки пользователя / все (если нужно)
    Route::get('/applications', [ApplicationController::class, 'index']);

    // создать заявку
    Route::post('/applications', [ApplicationController::class, 'store']);

    // посмотреть заявку
    Route::get('/applications/{application}', [ApplicationController::class, 'show']);

    // удалить заявку (отмена)
    Route::delete('/applications/{application}', [ApplicationController::class, 'destroy']);

    // ===== управление статусом (только владелец проекта) =====

    Route::patch('/applications/{application}/accept', [ApplicationController::class, 'accept']);

    Route::patch('/applications/{application}/reject', [ApplicationController::class, 'reject']);

    Route::post('/projects/add', [ProjectController::class, 'store']);
    Route::post('/projects/{project}/pay', [ProjectController::class, 'pay']);
    Route::post('/projects/{project}/cancel', [ProjectController::class, 'cancel']);

    // заявка на добавление новой категории (от пользователя)
    Route::post('/category-requests', [CategoryRequestController::class, 'store']);
});