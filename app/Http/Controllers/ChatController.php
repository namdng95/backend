<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function getMessages(Request $request)
    {
        $messages = Message::with('user')->latest()->take(50)->get();

        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        $user = User::first(authId());

        $message = $user->messages()->create([
            'content' => $request->input('content')
        ]);

        event(new MessageSent($user, $message));

        return response()->json($message);
    }
}
