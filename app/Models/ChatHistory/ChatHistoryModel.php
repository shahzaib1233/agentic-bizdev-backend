<?php

namespace App\Models\ChatHistory;

use Illuminate\Database\Eloquent\Model;

class ChatHistoryModel extends Model
{
     protected $table = 'chat_histories';

    protected $fillable = [
        'user_id', 'chat_id', 'question', 'answer',
    ];
    
    //
}
