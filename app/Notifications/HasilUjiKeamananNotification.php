<?php

namespace App\Notifications;

use App\Models\PengujianKeamanan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class HasilUjiKeamananNotification extends Notification
{
    use Queueable;

    public function __construct(public PengujianKeamanan $pengujian) {}

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
        $project = $this->pengujian->project;
        $namaProject = $project?->nama_project ?? 'Proyek Aplikasi';
        $hasil = ucfirst($this->pengujian->hasil);
        $tingkatRisiko = ucfirst($this->pengujian->tingkat_risiko);

        return [
            'pengujian_id' => $this->pengujian->id,
            'project_id' => $this->pengujian->project_id,
            'nama_project' => $namaProject,
            'hasil' => $this->pengujian->hasil,
            'tingkat_risiko' => $this->pengujian->tingkat_risiko,
            'tanggal_uji' => $this->pengujian->tanggal_uji?->format('d M Y') ?? now()->format('d M Y'),
            'nomor_surat' => $this->pengujian->nomor_surat,
            'pesan' => "Hasil Uji Keamanan Siber (CSIRT) untuk '{$namaProject}': {$hasil} (Tingkat Risiko: {$tingkatRisiko}).",
        ];
    }
}
