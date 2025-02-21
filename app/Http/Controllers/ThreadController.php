<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use App\Http\Requests\StoreThreadRequest;
use App\Http\Requests\UpdateThreadRequest;
use App\Models\Message;

class ThreadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $threads = Thread::all();
        return Inertia::render('Thread/Index', [
            'threads' => $threads
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        $thread = Thread::create([
            'title' => now()->format('Y/m/d H:i')
        ]);

        return redirect()->route('thread.show', ['thread' => $thread->id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Thread $thread)
    {
        $threads = Thread::all(); // サイドメニュー用にすべてのスレッドを取得
        return Inertia::render('Thread/Show', [
            'thread' => $thread,
            'threads' => $threads,
            'initialMessages' => $thread->messages,
            'threadId' => $thread->id,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Thread $thread)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateThreadRequest $request, Thread $thread)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Thread $thread)
    {
        //
    }

    public function events(Thread $thread)
    {
        return response()->stream(function () use ($thread) {
            while (true) {
                // メッセージの更新をチェック
                $updates = Message::where('thread_id', $thread->id)
                    ->where('updated_at', '>', now()->subSeconds(3))
                    ->get();

                if ($updates->isNotEmpty()) {
                    foreach ($updates as $update) {
                        echo "data: " . json_encode([
                            'type' => 'message_update',
                            'message' => $update
                        ]) . "\n\n";
                    }
                }

                ob_flush();
                flush();
                sleep(2);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
    }
}
