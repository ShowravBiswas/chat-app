<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function fetchMessages($userId)
    {
        return Message::where(function ($q) use ($userId) {
            $q->where('sender_id', auth()->id())->where('receiver_id', $userId);
        })->orWhere(function ($q) use ($userId) {
            $q->where('sender_id', $userId)->where('receiver_id', auth()->id());
        })->get();
    }

    public function sendMessage(Request $request)
    {
        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        broadcast(new MessageSent($message));

        Log::info('Sent message and broadcasted', ['message' => $message]); // debug log

        return $message;
    }

    public function userType(Request $request){

        broadcast(new \App\Events\UserTyping(
        $request->sender_id,
        $request->receiver_id,
        $request->sender_name
    ))->toOthers();

    return response()->json(['status' => 'Typing event broadcasted']);
    }
}
