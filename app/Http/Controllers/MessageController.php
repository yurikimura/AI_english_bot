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
            //音声データの保存
            if ($request->hasFile('audio')) {
                $audio = $request->file('audio');
                $timestamp = now()->format('YmdHis');
                $filename = "audio_{$timestamp}.mp3";
                $path = $audio->storeAs('audio', $filename, 'public');

                // ここでメッセージをデータベースに保存する処理を追加
                $message = Message::create([
                    'thread_id' => $thread_id,
                    'message_en' => 'dummy',
                    'message_ja' => '',
                    'sender' => 1, // ユーザーからの送信
                    'audio_file_path' => $path,
                ]);

                // 音声データをAPIに連携
                $apiService = new ApiService();
                $response = $apiService->callWisperApi($path);
                $message_en = $response['text'];

                // 英語から日本語に翻訳
                $message_ja = $apiService->translateText($message_en, 'ja');

                // メッセージを更新
                $message->update([
                    'message_en' => $message_en,
                    'message_ja' => $message_ja
                ]);

                // チャット表示用のレスポンスを返す
                return response()->json([
                    'success' => true,
                    'message' => [
                        'id' => $message->id,
                        'thread_id' => $thread_id,
                        'message_en' => $message_en,
                        'message_ja' => $message_ja,
                        'sender' => 1,
                        'audio_file_path' => $path,
                        'created_at' => $message->created_at
                    ]
                ]);
            }

            return response()->json(['success' => false, 'message' => 'オーディオファイルが見つかりません'], 400);

        } catch (\Exception $e) {
            Log::error('メッセージ保存エラー: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'サーバーエラーが発生しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
