<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NouveauMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $conversations = Conversation::with(['user1', 'user2', 'dernierMessage'])
            ->where('user_1_id', $userId)->orWhere('user_2_id', $userId)
            ->latest()->get();
        return view('messages.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        $userId = Auth::id();
        if ($conversation->user_1_id !== $userId && $conversation->user_2_id !== $userId) abort(403);
        $conversation->messages()->where('user_id', '!=', $userId)->update(['lu' => true]);
        $messages      = $conversation->messages()->with('auteur')->get();
        $interlocuteur = $conversation->user_1_id === $userId ? $conversation->user2 : $conversation->user1;
        $conversations = Conversation::with(['user1', 'user2', 'dernierMessage'])
            ->where('user_1_id', $userId)->orWhere('user_2_id', $userId)
            ->latest()->get();
        return view('messages.show', compact('conversation', 'conversations', 'messages', 'interlocuteur'));
    }

    public function ouvrirOuCreer(Request $request, User $user)
    {
        $moi = Auth::id();
        $conversation = Conversation::where(function ($q) use ($moi, $user) {
            $q->where('user_1_id', $moi)->where('user_2_id', $user->id);
        })->orWhere(function ($q) use ($moi, $user) {
            $q->where('user_1_id', $user->id)->where('user_2_id', $moi);
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_1_id'  => $moi,
                'user_2_id'  => $user->id,
                'annonce_id' => $request->integer('annonce_id') ?: null,
            ]);
        }
        return redirect()->route('messages.show', $conversation);
    }

    public function envoyer(Request $request, Conversation $conversation)
    {
        $userId = Auth::id();
        if ($conversation->user_1_id !== $userId && $conversation->user_2_id !== $userId) abort(403);
        $request->validate(['contenu' => 'required|string|max:1000']);
        $msg = Message::create(['conversation_id' => $conversation->id, 'user_id' => $userId, 'contenu' => $request->contenu]);

        // Notifier l'interlocuteur
        $destinataire = $conversation->user_1_id === $userId ? $conversation->user2 : $conversation->user1;
        $destinataire->notify(new NouveauMessage(Auth::user(), $msg));

        return back();
    }
}
