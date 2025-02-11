<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Thread;

class ThreadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Thread::create([
            'title' => '英語学習',
        ]);
        Thread::create([
            'title' => '英会話で議論',
        ]);
        Thread::create([
            'title' => '英語でメールを書く1',
        ]);
        Thread::create([
            'title' => '英語でメールを書く2',
        ]);
        Thread::create([
            'title' => '英語でメールを書く3',
        ]);
        Thread::create([
            'title' => '英語でメールを書く4',
        ]);
    }
}
