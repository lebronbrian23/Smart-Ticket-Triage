<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use OpenAI\Laravel\Facades\OpenAI;

class TicketClassifier
{
    /*
     * Copyright (c) 2025 Brian Ssekalegga
    * All rights reserved.
    * This code is submitted as part of a take-home assignment.
    * Redistribution or modification without author's permission is prohibited.
    */


    /**
     * @param Ticket $ticket
     * @return string[]
     */
    public function classifyTicket(Ticket $ticket): array
    {
        if ( !filter_var(config('services.openai.classify_enabled', env('OPENAI_CLASSIFY_ENABLED', false)),FILTER_VALIDATE_BOOLEAN)) {
            $fake = $this->dummy();
            return $fake;
        }

        $key = 'openai-classify:'.now()->format('YmdHi');
        $allowed_per_minute = (int) env('OPENAI_RPM', 30);

        $ok = RateLimiter::attempt($key, $allowed_per_minute, fn () => true);
        if ( !$ok ) {
            $fake = $this->dummy();
            return $fake;
          // throw new \RuntimeException('Rate limited classification.');
        }

        $user = "Subject: {$ticket->subject}\n Body: {$ticket->body}";
        $system = "You are a help-desk ticket classifier. Output strict JSON with keys: category (one of: bug, feature, billing, account, other), explanation (string), confidence (0..1 float).";

        $response = OpenAI::chat()->create([
            'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
            'response_format'   => ['type' => 'json_object'],
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $user],
            ]
        ]);

        $json = $response->choices[0]->message->content ?? '{}';
        $data = json_decode($json, true) ?: [];

        return [
            'category' => (string) ($data['category'] ?? 'other'),
            'explanation' => (string) ($data['explanation'] ?? 'Model returned no explanation'),
            'confidence' => (string) ($data['confidence'] ?? 0.5),
        ];
    }

    /** faker for if openai classify is not enabled
     * @return array
     */
    private function dummy(): array
    {
        $category = collect(['bug', 'feature', 'billing', 'account', 'other'])->random();
        return [
            'category' => $category,
            'explanation' => 'Dummy classification (OPENAI_CLASSIFY_ENABLED=false)',
            'confidence' => round(mt_rand(30,90) / 100,2),
        ];
    }
}