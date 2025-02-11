<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string, mixed>
     */
    protected $fillable = [
        'thread_id',
        'message_en',
        'message_ja',
        'sender',
        'audio_file_path',
    ];

    /**
     * 送信者の定数
     */
    public const SENDER_USER = 1;
    public const SENDER_AI = 2;

    /**
     * このメッセージが属するスレッドを取得
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }
}
