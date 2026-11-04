<?php

namespace App\Notifications\Channels;

use App\Services\TelegramService;
use Illuminate\Notifications\Notification;

class TelegramChannel
{
    public function __construct(private readonly TelegramService $telegramService) {}

    /**
     * Kirim notifikasi melalui bot Telegram.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toTelegram')) {
            return;
        }

        $chatId = $notifiable->routeNotificationFor('telegram', $notification)
            ?? ($notifiable->telegram_id ?? null);

        if (blank($chatId)) {
            return;
        }

        $this->telegramService->sendMessage(
            (string) $chatId,
            (string) $notification->toTelegram($notifiable),
        );
    }
}
