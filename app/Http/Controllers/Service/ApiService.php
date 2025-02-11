<?php

namespace App\Http\Controllers\Service;

use Illuminate\Support\Facades\Http;

class ApiService
{
    public function callWisperApi($audioFilePath)
    {
        $response = Http::attach('file',
        file_get_contents(
            strage_path('app/public/', $audioFilePath)
        ),
        'audio.mp3'
        )
        -> wihtHeader([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            //'Content-Type' => 'multipart/form-data',
        ])
        -> post('http://api.openai.com/v1/audio/transcriptions', [
            mdel => 'whisper-1',
            language => 'en',
        ]);
        return $response->json();
    }
}
