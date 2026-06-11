<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserActive
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::check() && Auth::user()->statut === 'suspendu') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('connexion')
                ->withErrors(['email' => 'Votre compte a été suspendu. Contactez l\'administrateur.']);
        }

        return $next($request);
    }
}
