<?php

use App\Http\Controllers\PasswordController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
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

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
});

Route::group(['prefix' => 'password'], function () {
    Route::post('/forgot', [PasswordController::class, 'forgot'])->name('password.forgot');
    Route::post('/reset', [PasswordController::class, 'reset'])->name('password.reset');
});

Route::group(['middleware' => ['auth:api', 'force.logout']], function () {
    // Change password
    Route::post('/password/change', [PasswordController::class, 'change'])->name('password.change');

    // Users
    Route::resource('users', UserController::class)->only([
        'index', 'show', 'store', 'update', 'destroy'
    ])->names([
        'index'   => 'users.index',
        'show'    => 'users.show',
        'store'   => 'users.store',
        'update'  => 'users.update',
        'destroy' => 'users.destroy'
    ]);

    // Messages
    Route::resource('messages', MessageController::class)->only([
        'index', 'show', 'store', 'update', 'destroy'
    ])->names([
        'index'   => 'messages.index',
        'show'    => 'messages.show',
        'store'   => 'messages.store',
        'update'  => 'messages.update',
        'destroy' => 'messages.destroy'
    ]);
});
