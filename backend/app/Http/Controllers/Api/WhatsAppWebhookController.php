<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Vérification du Webhook WhatsApp (Meta / Twilio API)
     */
    public function verify(Request $request)
    {
        $mode      = $request->query('hub_mode');
        $token     = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $verifyToken = config('services.whatsapp.verify_token', 'antigaspi_sec_token_2026');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return response($challenge, 200);
        }

        return response()->json(['message' => 'Token de vérification invalide.'], 403);
    }

    /**
     * Traitement et Proxying des Messages WhatsApp Entrants
     * Reçoit le message du Webhook, le stocke dans la BDD et le relaie.
     */
    public function receive(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('WhatsApp Webhook Entrant:', $data);

            // Extraction de l'expéditeur et du contenu (Format Twilio / Meta API)
            $fromNumber = $request->input('From') ?? $request->input('entry.0.changes.0.value.messages.0.from');
            $body       = $request->input('Body') ?? $request->input('entry.0.changes.0.value.messages.0.text.body') ?? '[Audio/Média]';
            $annonceId  = $request->input('annonce_id'); // Transmis si bouton d'annonce cliqué

            if (!$fromNumber) {
                return response()->json(['status' => 'ignored', 'reason' => 'Expéditeur introuvable'], 200);
            }

            // Normalisation du téléphone
            $cleanPhone = preg_replace('/[^0-9]/', '', $fromNumber);

            // Recherche de l'utilisateur expéditeur
            $expediteur = User::where('telephone', 'like', "%$cleanPhone%")->first();

            if (!$expediteur) {
                Log::info("WhatsApp Proxy: Utilisateur non trouvé pour le numéro $cleanPhone");
                return response()->json(['status' => 'user_not_found'], 200);
            }

            // Trouver ou créer la conversation liée
            $conversation = null;
            if ($annonceId) {
                $annonce = Annonce::find($annonceId);
                if ($annonce) {
                    $conversation = Conversation::firstOrCreate([
                        'user_1_id'  => $expediteur->id,
                        'user_2_id'  => $annonce->user_id,
                        'annonce_id' => $annonce->id,
                    ]);
                }
            }

            if (!$conversation) {
                $conversation = Conversation::where('user_1_id', $expediteur->id)
                    ->orWhere('user_2_id', $expediteur->id)
                    ->latest()
                    ->first();
            }

            if ($conversation) {
                // Enregistrement de la preuve intégrale dans la BDD (Audit Trace)
                $messageObj = Message::create([
                    'conversation_id' => $conversation->id,
                    'user_id'         => $expediteur->id,
                    'contenu'         => "[WhatsApp Proxy] " . $body,
                    'lu'              => false,
                ]);

                Log::info("Message WhatsApp archivé dans la BDD. Conversation ID: {$conversation->id}, Message ID: {$messageObj->id}");
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Message WhatsApp traité et archivé par le proxy AntiGaspiCI.'
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Erreur WhatsApp Proxy Webhook:', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
