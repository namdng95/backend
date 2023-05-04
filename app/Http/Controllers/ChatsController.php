<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use \App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ChatsController extends Controller
{
    /**
     * View chat
     *
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        return view('chat');
    }

    /**
     * Fetch messages
     *
     * @return Collection|array
     */
    public function fetchMessages(): Collection|array
    {
        return Message::with('user')->get();
    }

    /**
     * Send message
     *
     * @param Request $request Request
     *
     * @return array
     */
    public function sendMessage(Request $request): array
    {
        $user = User::find(authId());

        $message = $user->messages()->create([
            'message' => $request->input('message')
        ]);

        broadcast(new MessageSent($user, $message))->toOthers();

        return [
            'status' => 'Message Sent!',
            'user' => $user,
            'message' => $message
        ];
    }
}

