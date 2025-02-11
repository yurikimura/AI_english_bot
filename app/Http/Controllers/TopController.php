<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Thread;

class TopController extends Controller
{
    public function index()
    {
        $threads = Thread::all();
        return Inertia::render('Top', [
            'threads' => $threads
        ]);
    }
}
