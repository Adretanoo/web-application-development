<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $messages = Message::with('user')->latest()->take(100)->get()->reverse();
        return view('chat', compact('messages'));
    }

    public function store(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $message = Auth::user()->messages()->create([
            'message' => $request->message
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json(['status' => 'ok']);
    }
}
