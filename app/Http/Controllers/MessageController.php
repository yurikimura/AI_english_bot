<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Http\Controllers\Service\ApiService;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    public function store(Request $request, int $thread_id)
    {
        try {
            if (!$request->hasFile('audio')) {
                Log::error('音声ファイルが見つかりません');
                return response()->json(['success' => false, 'message' => 'オーディオファイルが見つかりません'], 400);
            }

            $audio = $request->file('audio');

            // ファイルのバリデーション
            if (!$audio->isValid()) {
                Log::error('無効な音声ファイル');
                return response()->json(['success' => false, 'message' => '無効な音声ファイルです'], 400);
            }

            $timestamp = now()->format('YmdHis');
            $filename = "audio_{$timestamp}.mp3";

            try {
                $path = $audio->storeAs('audio', $filename, 'public');
                Log::info('音声ファイル保存パス: ' . $path);
            } catch (\Exception $e) {
                Log::error('音声ファイル保存エラー: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => '音声ファイルの保存に失敗しました'], 500);
            }

            // 初期状態でメッセージを保存
            try {
                $message = Message::create([
                    'thread_id' => $thread_id,
                    'message_en' => 'dummy',  // 初期値を設定
                    'message_ja' => '',       // 初期値を設定
                    'sender' => 1,
                    'audio_file_path' => $path,
                ]);
            } catch (\Exception $e) {
                Log::error('メッセージ保存エラー: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'メッセージの保存に失敗しました'], 500);
            }

            // APIサービスの呼び出し
            try {
                $apiService = new ApiService();
                $response = $apiService->callWisperApi($path);

                if (!isset($response['text'])) {
                    throw new \Exception('Whisper APIからのレスポンスに text が含まれていません');
                }

                $message_en = $response['text'];
                $message_ja = '';

                // メッセージを更新
                $message->update([
                    'message_en' => $message_en,
                ]);

                $messages = Message::where('thread_id', $thread_id)->get();
                // GPTにAPIリクエストして応答を取得
                $gptResponse = $apiService->callGptApi($messages);

                $aiMessageText = $gptResponse['choices'][0]['message']['content'];
                // AIの応答をDBに保存
                $assistantMessage = Message::create([
                    'thread_id' => $thread_id,
                    'message_en' => $aiMessageText,
                    'message_ja' => '',
                    'sender' => 2,
                    'audio_file_path' => '',
                ]);

                // TTSにAPIリクエストして音声ファイルを生成
                $aiAudioFilePath = $apiService->callTtsApi($aiMessageText);
                // 音声ファイルをDBに保存
                $assistantMessage->update([
                    'audio_file_path' => $aiAudioFilePath, // 音声ファイルのパスを保存
                ]);

                return response()->json([
                    'success' => true,
                    'message' => [
                        'id' => $message->id,
                        'thread_id' => $thread_id,
                        'message_en' => $message_en,
                        'message_ja' => $message_ja,
                        'sender' => Message::SENDER_USER,
                        'audio_file_path' => $path,
                        'created_at' => $message->created_at,
                        'assistant_message' => [
                            'id' => $assistantMessage->id,
                            'thread_id' => $thread_id,
                            'message_en' => $assistantMessage->message_en,
                            'message_ja' => '',
                            'sender' => Message::SENDER_AI,
                            'audio_file_path' => null,
                            'created_at' => $assistantMessage->created_at
                        ]
                    ]
                ]);

            } catch (\Exception $e) {
                Log::error('API処理エラー: ' . $e->getMessage());
                return response()->json([
                    'success' => true,
                    'message' => [
                        'id' => $message->id,
                        'thread_id' => $thread_id,
                        'message_en' => 'dummy',
                        'message_ja' => 'API処理中にエラーが発生しました',
                        'sender' => Message::SENDER_USER,
                        'audio_file_path' => $path,
                        'created_at' => $message->created_at
                    ]
                ]);
            }

        } catch (\Exception $e) {
            Log::error('予期せぬエラー: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'サーバーエラーが発生しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function translate(Request $request, int $threadId, int $messageId)
    {
        return response()->json(['message' => 'これはダミーの日本語です']);
    }
}
