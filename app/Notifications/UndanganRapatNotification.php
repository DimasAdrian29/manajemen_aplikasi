<?php

namespace App\Notifications;

use App\Models\RapatProject;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UndanganRapatNotification extends Notification
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
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'rapat_id' => $this->rapat->id,
            'project_id' => $this->rapat->project_id,
            'nama_project' => $this->rapat->project->nama_project ?? 'Proyek Aplikasi',
            'nama_rapat' => $this->rapat->nama_rapat,
            'waktu_rapat' => $this->rapat->waktu_rapat->format('d M Y, H:i'),
            'tempat_rapat' => $this->rapat->tempat_rapat,
            'pesan' => "Undangan Rapat Proyek '{$this->rapat->nama_rapat}' pada {$this->rapat->waktu_rapat->format('d M Y, H:i')} WIB.",
        ];
    }
}
