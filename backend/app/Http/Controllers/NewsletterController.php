<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:150',
        ]);

        $subscriber = NewsletterSubscriber::firstOrCreate(['email' => $validated['email']]);

        if (!$subscriber->wasRecentlyCreated) {
            return back()->with('newsletter_success', 'Cet e-mail est déjà inscrit aux alertes AntiGaspiCI.');
        }

        return back()->with('newsletter_success', 'Inscription confirmée ! Vous recevrez les nouvelles annonces par e-mail.');
    }
}
