<?php

namespace App\Notifications;

use App\Models\Message;
use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

class NouveauMessage extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public User $expediteur, public Message $message) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $prenom = $this->expediteur->prenom ?? $this->expediteur->nom_structure ?? 'Quelqu\'un';
        return [
            'type'           => 'nouveau_message',
            'icone'          => '💬',
            'titre'          => 'Nouveau message',
            'message'        => $prenom . ' vous a envoyé un message : « ' . \Str::limit($this->message->contenu, 60) . ' »',
            'url'            => route('messages.show', $this->message->conversation_id),
            'conversation_id'=> $this->message->conversation_id,
        ];
    }
}
