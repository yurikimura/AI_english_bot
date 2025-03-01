<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Thread;
use App\Models\Message;

class TopController extends Controller
{
    public function index()
    {
        $threads = Thread::all();
        $studyDates = Message::selectRaw('DATE(created_at) as date')
            ->distinct()
            ->pluck('date')
            ->toArray();

        // スレッドの作成日を追加
        $threadDates = $threads->pluck('created_at')->map(function ($date) {
            return $date->format('Y-m-d');
        })->toArray();

        $studyDates = array_unique(array_merge($studyDates, $threadDates));

        return Inertia::render('Top', [
            'threads' => $threads,
            'studyDates' => $studyDates
        ]);
    }
}
