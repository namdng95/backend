<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ChatsController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Auth::routes();

Route::group([], function () {
//    Route::post('/chat/message', [\App\Http\Controllers\ChatController::class, 'sendMessage']);
//    Route::get('/chat/messages', [\App\Http\Controllers\ChatController::class, 'getMessages']);

//Route::get('/', [ChatsController::class => 'index']);
    Route::get('messages', [ChatsController::class, 'fetchMessages']);
    Route::post('messages', [ChatsController::class, 'sendMessage']);

    Route::get('/home', [HomeController::class, 'index'])->name('home');
});
Route::get('/chat', function () {
    return view('chat');
})->middleware('auth')->name('chat');


//
//Route::group(['middleware' => 'auth.custom'], function () {
//    Route::any('test', [TestController::class, 'index'])->name('test');
//
//    Route::get('/chat', [ChatsController::class, 'index'])->name('chat');
//    Route::get('/messages', [ChatsController::class, 'fetchMessages'])->name('fetch_messages');
//    Route::post('/messages', [ChatsController::class, 'sendMessage'])->name('send_message');
//});

