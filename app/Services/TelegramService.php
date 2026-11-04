<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TelegramService
{
    /**
     * Periksa apakah token bot Telegram sudah dikonfigurasi.
     */
    public function isConfigured(): bool
    {
        return filled(config('services.telegram.bot_token'));
    }

    /**
     * Kirim pesan teks ke chat Telegram tertentu.
     */
    public function sendMessage(string $chatId, string $message): bool
    {
        $token = config('services.telegram.bot_token');

        if (blank($token) || blank($chatId)) {
            return false;
        }

        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => true,
                ]);

            if ($response->successful() && $response->json('ok') === true) {
                return true;
            }

            Log::warning('Gagal mengirim pesan Telegram.', [
                'chat_id' => $chatId,
                'status' => $response->status(),
                'deskripsi' => $response->json('description') ?? $response->body(),
            ]);
        } catch (Throwable $exception) {
            Log::error('Kesalahan saat mengirim pesan Telegram.', [
                'chat_id' => $chatId,
                'pesan' => $exception->getMessage(),
            ]);
        }

        return false;
    }
}
