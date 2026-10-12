<?php

namespace App\Notifications;

use App\Models\HostingAplikasi;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AplikasiLiveNotification extends Notification
{
    use Queueable;

    public function __construct(public HostingAplikasi $hosting) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $project = $this->hosting->project;
        $namaAplikasi = $project?->aplikasi?->nama_aplikasi ?? $project?->nama_project ?? 'Aplikasi';
        $url = $this->hosting->url_aplikasi ?? ('https://'.($this->hosting->nama_domain ?? ''));

        return [
            'hosting_id' => $this->hosting->id,
            'project_id' => $this->hosting->project_id,
            'nama_aplikasi' => $namaAplikasi,
            'url_aplikasi' => $url,
            'ip_server' => $this->hosting->ip_server,
            'tanggal_live' => $this->hosting->tanggal_live?->format('d M Y') ?? now()->format('d M Y'),
            'pesan' => "Selamat! Aplikasi '{$namaAplikasi}' telah selesai dideploy dan berstatus LIVE di {$url}.",
        ];
    }
}
