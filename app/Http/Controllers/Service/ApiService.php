<?php

namespace App\Http\Controllers\Service;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ApiService
{
    public function callWisperApi($audioFilePath)
    {
        $response = Http::attach(
            'file',
            file_get_contents(
                storage_path('app/public/' . $audioFilePath)
            ),
            'audio.mp3'
        )
        ->withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            //'Content-Type' => 'multipart/form-data',
        ])
        ->post('https://api.openai.com/v1/audio/transcriptions', [
            'model' => 'whisper-1',
            'language' => 'en',
        ]);
        return $response->json();
    }

    public function translateText($text, $targetLang)
    {
        // 翻訳の実装（必要に応じて）
        return $text;  // 仮の実装
    }
}
