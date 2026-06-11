<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatController extends Controller
{
    public function chat(Request $request): StreamedResponse
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'history' => 'nullable|array|max:20',
        ]);

        $userMessage = $request->input('message');
        $history     = $request->input('history', []);

        // Construit l'historique au format Gemini (user/model)
        $contents = [];
        foreach ($history as $h) {
            if (!isset($h['role'], $h['content'])) continue;
            $role = $h['role'] === 'assistant' ? 'model' : 'user';
            $contents[] = [
                'role'  => $role,
                'parts' => [['text' => (string) $h['content']]],
            ];
        }
        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => $userMessage]],
        ];

        $apiKey = config('services.gemini.key');
        $url    = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:streamGenerateContent?alt=sse&key={$apiKey}";

        $body = [
            'system_instruction' => [
                'parts' => [[
                    'text' => "Tu es un assistant IA intégré à la plateforme AntiGaspiCI, une marketplace anti-gaspillage alimentaire en Côte d'Ivoire. Tu aides les utilisateurs avec toutes leurs questions : utilisation de la plateforme, annonces, réservations, messagerie, avis, mais aussi toute autre question générale. Réponds toujours en français, de manière claire, concise et bienveillante.",
                ]],
            ],
            'contents'           => $contents,
            'generationConfig'   => ['maxOutputTokens' => 1024],
        ];

        return response()->stream(function () use ($url, $body) {

            try {
                $client = new \GuzzleHttp\Client();

                $response = $client->post($url, [
                    'json'    => $body,
                    'stream'  => true,
                    'timeout' => 60,
                ]);

                $stream = $response->getBody();
                $buffer = '';

                while (!$stream->eof()) {
                    $chunk  = $stream->read(1024);
                    $buffer .= $chunk;
                    $lines  = explode("\n", $buffer);
                    $buffer = array_pop($lines);

                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (!str_starts_with($line, 'data: ')) continue;

                        $json = substr($line, 6);
                        $data = json_decode($json, true);
                        if (!$data) continue;

                        // Erreur JSON renvoyée dans le corps SSE (HTTP 200 mais avec erreur)
                        if (isset($data['error'])) {
                            $errMsg  = $data['error']['message'] ?? 'Erreur inconnue';
                            $errCode = $data['error']['code']    ?? 0;
                            \Illuminate\Support\Facades\Log::error("Gemini SSE body error {$errCode}: {$errMsg}");
                            echo 'data: ' . json_encode(['error' => "Gemini {$errCode} : {$errMsg}"]) . "\n\n";
                            ob_flush(); flush();
                            break 2;
                        }

                        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                        if ($text !== null) {
                            echo 'data: ' . json_encode(['text' => $text]) . "\n\n";
                            ob_flush();
                            flush();
                        }
                    }
                }
            } catch (\GuzzleHttp\Exception\ConnectException $e) {
                \Illuminate\Support\Facades\Log::error('Gemini ConnectException: ' . $e->getMessage());
                echo 'data: ' . json_encode(['error' => 'Impossible de joindre le service IA (réseau).']) . "\n\n";
                ob_flush(); flush();
            } catch (\GuzzleHttp\Exception\BadResponseException $e) {
                // Catch 4xx ET 5xx (ClientException + ServerException)
                $code = $e->getResponse()->getStatusCode();
                $body = (string) $e->getResponse()->getBody();
                \Illuminate\Support\Facades\Log::error("Gemini HTTP {$code}: {$body}");
                if ($code === 429) {
                    $msg = 'Limite de requêtes atteinte. Réessayez dans quelques secondes.';
                } elseif ($code === 400) {
                    $msg = "Requête invalide (HTTP 400) — clé ou modèle incorrect.";
                } elseif ($code === 403) {
                    $msg = "Accès refusé (HTTP 403) — vérifiez GEMINI_API_KEY dans Render.";
                } elseif ($code === 404) {
                    $msg = "Modèle introuvable (HTTP 404). Vérifiez le nom du modèle Gemini.";
                } else {
                    $msg = "Erreur Gemini HTTP {$code}.";
                }
                echo 'data: ' . json_encode(['error' => $msg]) . "\n\n";
                ob_flush(); flush();
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                // Toute autre exception Guzzle (sans réponse HTTP)
                \Illuminate\Support\Facades\Log::error('Gemini RequestException: ' . $e->getMessage());
                $debug = config('app.debug') ? ' [RequestException: ' . $e->getMessage() . ']' : '';
                echo 'data: ' . json_encode(['error' => 'Erreur requête Gemini.' . $debug]) . "\n\n";
                ob_flush(); flush();
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Gemini Exception: ' . get_class($e) . ': ' . $e->getMessage());
                $debug = config('app.debug') ? ' [' . get_class($e) . ': ' . $e->getMessage() . ']' : '';
                echo 'data: ' . json_encode(['error' => 'Service IA indisponible.' . $debug]) . "\n\n";
                ob_flush(); flush();
            }

            echo "data: [DONE]\n\n";
            ob_flush();
            flush();

        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection'        => 'keep-alive',
        ]);
    }
}
