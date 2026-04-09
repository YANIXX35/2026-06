<?php

namespace App\Http\Controllers;

use App\Models\Signalement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SignalementController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'raison'      => 'required|string|max:200',
            'description' => 'nullable|string|max:500',
            'annonce_id'  => 'nullable|exists:annonces,id',
        ]);

        Signalement::create([
            'user_id'    => Auth::id(),
            'annonce_id' => $request->annonce_id,
            'raison'     => $request->raison,
            'description'=> $request->description,
        ]);

        return back()->with('success', 'Signalement envoyé à l\'administration.');
    }
}
