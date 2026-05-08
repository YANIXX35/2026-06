<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use Illuminate\Http\Request;
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

        Mail::to(config('mail.from.address'))
            ->send(new ContactMail(
                $validated['nom'],
                $validated['email'],
                $validated['sujet'],
                $validated['message']
            ));

        return back()->with('success', 'Votre message a bien été envoyé ! Nous vous répondrons sous 48h.');
    }
}
