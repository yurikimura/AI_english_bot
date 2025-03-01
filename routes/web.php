<?php

use App\Http\Controllers\ThreadController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TopController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

require __DIR__ . '/auth.php';

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'threads' => \App\Models\Thread::all()
    ]);
})->name('welcome');

Route::middleware(['auth'])->group(function () {
    // トップページ
    Route::get('/top', [TopController::class, 'index'])->name('top.index');
    // 英会話画面を表示
    Route::get('/thread/{thread}', [ThreadController::class, 'show'])->name('thread.show');
    // 新規スレッドを作成
    Route::get('/thread', [ThreadController::class, 'store'])->name('thread.store');
    // メッセージを保存
    Route::post('/thread/{threadId}/message', [MessageController::class, 'store'])->name('message.store');
    // メッセージを日本語に翻訳
    Route::post('/thread/{threadId}/message/{messageId}/translate', [MessageController::class, 'translate'])
    ->name('message.translate')
    ->where('threadId', '[0-9]+')
    ->where('messageId', '[0-9]+');
    // SSEイベントを取得
    Route::get('/thread/{thread}/events', [ThreadController::class, 'events'])
        ->name('thread.events');
});

Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);
