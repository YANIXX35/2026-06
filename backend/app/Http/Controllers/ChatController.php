<?php

namespace App\Http\Controllers;

use App\Models\ChatLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatController extends Controller
{
    public function chat(Request $request): StreamedResponse
    {
        Log::debug('CHAT START', [
            'message_len'   => strlen($request->input('message', '')),
            'history_count' => count($request->input('history', [])),
        ]);

        $request->validate([
            'message' => 'required|string|max:2000',
            'history' => 'nullable|array|max:20',
        ]);

        $userMessage = $request->input('message');
        $history     = $request->input('history', []);

        ChatLog::create([
            'user_id'  => Auth::id(),
            'question' => $userMessage,
        ]);

        $apiKey = config('services.gemini.key');
        Log::debug('GEMINI CONFIG', [
            'key_exists'  => !empty($apiKey),
            'key_length'  => strlen($apiKey ?? ''),
            'key_prefix'  => substr($apiKey ?? '', 0, 8) . '...',
        ]);

        $contents = [];
        foreach ($history as $h) {
            if (!isset($h['role'], $h['content'])) continue;
            $role       = $h['role'] === 'assistant' ? 'model' : 'user';
            $contents[] = ['role' => $role, 'parts' => [['text' => (string) $h['content']]]];
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $userMessage]]];

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:streamGenerateContent?alt=sse&key={$apiKey}";

        $body = [
            'system_instruction' => [
                'parts' => [[
                    'text' => "Tu es un assistant IA intégré à la plateforme AntiGaspiCI, une marketplace anti-gaspillage alimentaire en Côte d'Ivoire. Tu aides les utilisateurs avec toutes leurs questions : utilisation de la plateforme, annonces, réservations, messagerie, avis, mais aussi toute autre question générale. Réponds toujours en français, de manière claire, concise et bienveillante.",
                ]],
            ],
            'contents'         => $contents,
            'generationConfig' => ['maxOutputTokens' => 1024],
        ];

        return response()->stream(function () use ($url, $body) {

            // Flush sécurisé : ne lève pas d'exception si aucun buffer n'est actif.
            // ob_flush() échoue avec ErrorException sur Render (pas de buffer PHP).
            $safeFlush = static function (): void {
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
            };

            try {
                $client = new \GuzzleHttp\Client();

                Log::debug('BEFORE GEMINI CALL');

                $response = $client->post($url, [
                    'json'    => $body,
                    'stream'  => true,
                    'timeout' => 60,
                ]);

                Log::debug('AFTER GEMINI CALL', [
                    'http_status'  => $response->getStatusCode(),
                    'content_type' => $response->getHeaderLine('Content-Type'),
                ]);

                $stream     = $response->getBody();
                $buffer     = '';
                $textTokens = 0;

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

                        // Erreur JSON dans le corps SSE (HTTP 200 avec error body)
                        if (isset($data['error'])) {
                            $errMsg  = $data['error']['message'] ?? 'Erreur inconnue';
                            $errCode = $data['error']['code']    ?? 0;
                            Log::error('GEMINI SSE BODY ERROR', ['code' => $errCode, 'message' => $errMsg]);
                            echo 'data: ' . json_encode(['error' => "Gemini {$errCode} : {$errMsg}"]) . "\n\n";
                            $safeFlush();
                            break 2;
                        }

                        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                        if ($text !== null) {
                            $textTokens++;
                            echo 'data: ' . json_encode(['text' => $text]) . "\n\n";
                            $safeFlush();
                        }
                    }
                }

                Log::debug('STREAM FINISHED', ['text_tokens' => $textTokens]);

            } catch (\Throwable $e) {
                Log::error('CHATBOT CRASH', [
                    'class'   => get_class($e),
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                    'trace'   => $e->getTraceAsString(),
                ]);

                $errDisplay = get_class($e) . ': ' . $e->getMessage()
                    . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')';

                echo 'data: ' . json_encode(['error' => 'CRASH — ' . $errDisplay]) . "\n\n";
                $safeFlush();
            }

            echo "data: [DONE]\n\n";
            $safeFlush();

            Log::debug('CHAT END');

        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection'        => 'keep-alive',
        ]);
    }
}
