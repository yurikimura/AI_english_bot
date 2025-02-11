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

        return redirect()->route('thread.show', ['thread_id' => $thread->id]);
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
            'messages' => $thread->messages
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
}
