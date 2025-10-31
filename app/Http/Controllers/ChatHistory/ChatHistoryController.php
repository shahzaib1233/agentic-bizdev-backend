<?php

namespace App\Http\Controllers\ChatHistory;

use App\Http\Controllers\Controller;
use App\Models\ChatHistory\ChatHistoryModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatHistoryController extends Controller
{
    //

     
    public function store(Request $request)
    {
        $user = $request->user(); // current logged-in user
        abort_unless($user, 401, 'Unauthenticated');

        $validated = $request->validate([
            'chat_id'  => ['nullable', 'integer', 'min:1'],
            'question' => ['required', 'string'],
            'answer'   => ['required', 'string'],
        ]);

        // if no chat_id in JSON → generate next chat_id automatically
        $chatId = $validated['chat_id'] ?? null;

        if ($chatId === null) {
            $chatId = DB::transaction(function () use ($user) {
                $maxChatId = ChatHistoryModel::where('user_id', $user->id)
                    ->lockForUpdate()
                    ->max('chat_id');

                return (int)($maxChatId ?? 0) + 1;
            }, 3);
        }

        // create record
        $entry = ChatHistoryModel::create([
            'user_id'  => $user->id,
            'chat_id'  => $chatId,
            'question' => $validated['question'],
            'answer'   => $validated['answer'],
        ]);

        // return JSON with assigned chat_id included
        return response()->json([
            'message'  => 'Saved successfully',
            'chat_id'  => $chatId,
            'data'     => $entry,
        ], 201);
    }

    public function getAll(Request $request)
    {
         $user = $request->user();
        $chats = ChatHistoryModel::where('user_id',$user->id);
        return response()->json([
            'chats' => $chats
        ]);
    }

}
