<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'nom'     => 'required|string|max:100',
            'email'   => 'required|email|max:100',
            'sujet'   => 'required|string|max:200',
            'message' => 'required|string|max:2000',
        ]);

        try {
            Mail::to(config('mail.from.address'))
                ->send(new ContactMail(
                    $validated['nom'],
                    $validated['email'],
                    $validated['sujet'],
                    $validated['message']
                ));
        } catch (\Throwable $e) {
            Log::error('Échec de l\'envoi du mail de contact : '.$e->getMessage());
            return back()->with('error', 'Votre message n\'a pas pu être envoyé pour le moment. Réessayez plus tard.');
        }

        return back()->with('success', 'Votre message a bien été envoyé ! Nous vous répondrons sous 48h.');
    }
}
