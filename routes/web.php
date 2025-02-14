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
    Route::get('/top', [TopController::class, 'index'])->name('top.index');
    Route::get('/threads', [ThreadController::class, 'index'])->name('threads.index');
    Route::get('/threads/{thread}', [ThreadController::class, 'show'])->name('threads.show');
    Route::get('/thread', [ThreadController::class, 'store'])->name('thread.store');
    Route::post('/thread/{thread_id}/message', [MessageController::class, 'store'])->name('message.store');
});
