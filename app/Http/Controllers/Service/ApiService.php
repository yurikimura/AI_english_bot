<?php

namespace App\Http\Controllers\Service;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\Message;

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

    /**
     * @param Collection<Messages>
     */

    public function callGptApi($modelMessages)
    {
        $systemMessage = [
            'role' => 'system',
            'content' => 'You are a helpful English teacher. Please speak English'
        ];

        $message = $modelMessages->map(function($message){
            return [
                'role' => $message->sender === Message::SENDER_USER ? 'user' : 'assistant',
                'content' => $message->message_en
            ];
        })->toArray();

        $messages = array_merge([$systemMessage], $message);

        /**
         * @param Collection<Messages>
         */
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 1000,
        ]);
        return $response->json();
        }
        /**
         * @param string $aiMessageText
         */
        public function callTtsApi($aiMessageText)
        {
            $response = Http::withHeaders([
                'content-type' => 'application/json',
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            ])->post('https://api.openai.com/v1/audio/speech', [
                'model' => 'tts-1',
                'input' => $aiMessageText,
                'voice' => 'nova',
            ]);

            // 音声ファイルを保存
            $filename = 'speech_' . now()->format('Ymd_His') . '.mp3';
            $filePath = storage_path('app/public/ai_audio/' . $filename);
            file_put_contents($filePath, $response->body());

            return 'ai_audio/' . $filename;
        }
    }
