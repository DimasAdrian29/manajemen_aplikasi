<?php

namespace App\Notifications;

use App\Models\RapatProject;
use App\Notifications\Channels\TelegramChannel;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReminderRapatNotification extends Notification
{
    use Queueable;

    public function __construct(public RapatProject $rapat) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (filled($notifiable->telegram_id ?? null) && app(TelegramService::class)->isConfigured()) {
            $channels[] = TelegramChannel::class;
        }

        return $channels;
    }

    /**
     * Susun pesan reminder yang dikirim melalui Telegram.
     */
    public function toTelegram(object $notifiable): string
    {
        $namaProject = $this->rapat->project?->nama_project ?? 'Proyek Aplikasi';
        $tempat = filled($this->rapat->tempat_rapat)
            ? $this->rapat->tempat_rapat
            : 'Ruang Rapat Bidang APTIKA Lt. 2 Diskominfo Riau';
        $waktu = $this->rapat->waktu_rapat->format('l, d F Y - H:i');

        $pesan = "🔔 <b>Reminder Rapat</b>\n\n";
        $pesan .= 'Halo <b>'.e($notifiable->name)."</b>, rapat berikut akan segera dimulai:\n\n";
        $pesan .= '📌 <b>'.e($this->rapat->nama_rapat)."</b>\n";
        $pesan .= '🗂️ Proyek: '.e($namaProject)."\n";
        $pesan .= '🗓️ '.e($waktu)." WIB\n";
        $pesan .= '📍 '.e($tempat)."\n";

        if (filled($this->rapat->deskripsi)) {
            $pesan .= "\n📝 ".(string) str($this->rapat->deskripsi)->limit(300)."\n";
        }

        $pesan .= "\nMohon hadir tepat waktu. Terima kasih.";

        return $pesan;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'tipe' => 'reminder_rapat',
            'rapat_id' => $this->rapat->id,
            'project_id' => $this->rapat->project_id,
            'nama_project' => $this->rapat->project->nama_project ?? 'Proyek Aplikasi',
            'nama_rapat' => $this->rapat->nama_rapat,
            'waktu_rapat' => $this->rapat->waktu_rapat->format('d M Y, H:i'),
            'tempat_rapat' => $this->rapat->tempat_rapat,
            'pesan' => "Reminder: rapat '{$this->rapat->nama_rapat}' akan dimulai pada {$this->rapat->waktu_rapat->format('d M Y, H:i')} WIB.",
        ];
    }
}
