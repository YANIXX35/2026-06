<?php

namespace App\Http\Controllers;

use App\Mail\OtpResetMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    public function showEmailForm()
    {
        return view('auth.mot-de-passe-oublie');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Aucun compte trouvé avec cette adresse email.',
        ]);

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_reset_otps')->where('email', $request->email)->delete();
        DB::table('password_reset_otps')->insert([
            'email'      => $request->email,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(15),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        session(['otp_email' => $request->email]);

        $mailSent = false;
        try {
            Mail::to($request->email)->send(new OtpResetMail($otp));
            $mailSent = true;
        } catch (\Exception $e) {
            Log::error('OTP reset mail failed: ' . $e->getMessage());
        }

        if (!$mailSent && config('app.debug')) {
            return redirect()->route('password.otp.form')
                ->with('warning', "⚠️ Email non envoyé (SMTP non configuré). Code OTP : <strong>{$otp}</strong>");
        }

        return redirect()->route('password.otp.form')
            ->with('success', 'Un code à 6 chiffres a été envoyé à ' . $request->email . '. Valable 15 minutes.');
    }

    public function showOtpForm()
    {
        if (!session('otp_email')) {
            return redirect()->route('password.email.form');
        }
        return view('auth.verifier-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $email = session('otp_email');
        if (!$email) {
            return redirect()->route('password.email.form');
        }

        $record = DB::table('password_reset_otps')
            ->where('email', $email)
            ->where('otp', $request->otp)
            ->first();

        if (!$record) {
            return back()->withErrors(['otp' => 'Code incorrect. Veuillez réessayer.']);
        }

        if (now()->isAfter($record->expires_at)) {
            DB::table('password_reset_otps')->where('email', $email)->delete();
            return back()->withErrors(['otp' => 'Ce code a expiré. Veuillez recommencer.']);
        }

        session(['otp_verified' => true]);
        return redirect()->route('password.new.form');
    }

    public function showNewPasswordForm()
    {
        if (!session('otp_email') || !session('otp_verified')) {
            return redirect()->route('password.email.form');
        }
        return view('auth.nouveau-mot-de-passe');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ], [
            'password.min'       => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        $email = session('otp_email');

        if (!$email || !session('otp_verified')) {
            return redirect()->route('password.email.form')
                ->withErrors(['email' => 'Session expirée. Recommencez la procédure.']);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            Log::error("resetPassword: aucun utilisateur trouvé pour {$email}");
            return redirect()->route('password.email.form')
                ->withErrors(['email' => 'Compte introuvable. Contactez le support.']);
        }

        // Utilise le modèle Eloquent directement (le cast 'hashed' gère Hash::make)
        $user->password = $request->password;
        $user->save();

        // Vérification immédiate que le hash stocké correspond bien
        if (!Hash::check($request->password, $user->fresh()->password)) {
            Log::error("resetPassword: vérification hash échouée pour {$email}");
            return back()->withErrors(['password' => 'Erreur interne lors du reset. Réessayez.']);
        }

        Log::info("resetPassword: mot de passe mis à jour pour {$email}");

        DB::table('password_reset_otps')->where('email', $email)->delete();
        session()->forget(['otp_email', 'otp_verified']);

        return redirect()->route('connexion')
            ->with('success', '✅ Mot de passe réinitialisé ! Connectez-vous avec votre nouveau mot de passe.');
    }
}
