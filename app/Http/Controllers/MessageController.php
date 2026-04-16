<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // Get messages between current user and $user
    public function index(User $user)
    {
        $messages = Message::where(function($query) use ($user) {
            $query->where('sender_id', Auth::id())
                  ->where('receiver_id', $user->id);
        })->orWhere(function($query) use ($user) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', Auth::id());
        })->orderBy('created_at')->get();

        return response()->json($messages);
    }

    // Store new message
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return response()->json($message);
    }

    // get all users u have messaged before
    public function contacts()
    {
        $user = Auth::user();
        //get all ids where im sender_id and reciever_id
        $contactIds = Message::where('sender_id', $user->id)
                        ->pluck('receiver_id')
                        ->merge(
                            Message::where('receiver_id', $user->id)->pluck('sender_id')
                        )
                        ->unique()
                        ->toArray();

        $contacts = User::whereIn('id', $contactIds)->get(['id', 'name']);

        return response()->json($contacts);
    }
}

