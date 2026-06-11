<?php

namespace App\Http\Controllers;

use App\Mail\OtpResetMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    // Étape 1 : formulaire email
    public function showEmailForm()
    {
        return view('auth.mot-de-passe-oublie');
    }

    // Étape 1 : envoi OTP
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
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $mailSent = false;
        $mailError = null;

        try {
            Mail::to($request->email)->send(new OtpResetMail($otp));
            $mailSent = true;
        } catch (\Exception $e) {
            $mailError = $e->getMessage();
            \Illuminate\Support\Facades\Log::error('OTP mail failed: ' . $mailError);
        }

        session(['otp_email' => $request->email]);

        // En debug : affiche le code OTP dans le flash si le mail a échoué
        if (!$mailSent && config('app.debug')) {
            return redirect()->route('password.otp.form')
                ->with('warning', "⚠️ Email non envoyé (SMTP non configuré). Votre code OTP de test : <strong>{$otp}</strong>");
        }

        return redirect()->route('password.otp.form')
            ->with('success', 'Un code à 6 chiffres a été envoyé à ' . $request->email);
    }

    // Étape 2 : formulaire OTP
    public function showOtpForm()
    {
        if (!session('otp_email')) {
            return redirect()->route('password.email.form');
        }
        return view('auth.verifier-otp');
    }

    // Étape 2 : vérification OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

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

    // Étape 3 : formulaire nouveau mot de passe
    public function showNewPasswordForm()
    {
        if (!session('otp_email') || !session('otp_verified')) {
            return redirect()->route('password.email.form');
        }
        return view('auth.nouveau-mot-de-passe');
    }

    // Étape 3 : mise à jour du mot de passe
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
            return redirect()->route('password.email.form');
        }

        User::where('email', $email)->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_reset_otps')->where('email', $email)->delete();

        session()->forget(['otp_email', 'otp_verified']);

        return redirect()->route('connexion')
            ->with('success', 'Mot de passe réinitialisé avec succès ! Vous pouvez vous connecter.');
    }
}
