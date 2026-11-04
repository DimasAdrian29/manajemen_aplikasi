<?php

namespace App\Services;

use App\Models\RapatProject;
use App\Notifications\ReminderRapatNotification;
use Carbon\CarbonImmutable;

class RapatReminderService
{
    public function __construct(private readonly TelegramService $telegramService) {}

    /**
     * Kirim reminder Telegram ke seluruh peserta rapat yang memiliki ID Telegram.
     *
     * @return array{rapat: int, terkirim: int, dilewati: int}
     */
    public function kirimUntukRapat(RapatProject $rapat, bool $paksa = false): array
    {
        $rapat->loadMissing(['project', 'peserta.user']);

        $jumlahPeserta = $rapat->peserta->count();

        if (! $paksa && $rapat->status !== 'dijadwalkan') {
            return ['rapat' => 1, 'terkirim' => 0, 'dilewati' => $jumlahPeserta];
        }

        if (! $this->telegramService->isConfigured()) {
            return ['rapat' => 1, 'terkirim' => 0, 'dilewati' => $jumlahPeserta];
        }

        $terkirim = 0;
        $dilewati = 0;

        foreach ($rapat->peserta as $peserta) {
            if (! $paksa && $peserta->reminder_dikirim_at !== null) {
                $dilewati++;

                continue;
            }

            if (blank($peserta->user?->telegram_id)) {
                $dilewati++;

                continue;
            }

            $peserta->user->notify(new ReminderRapatNotification($rapat));

            $peserta->forceFill(['reminder_dikirim_at' => now()])->save();

            $terkirim++;
        }

        return ['rapat' => 1, 'terkirim' => $terkirim, 'dilewati' => $dilewati];
    }

    /**
     * Kirim reminder untuk rapat yang akan dimulai dalam jendela waktu yang dikonfigurasi.
     *
     * @return array{rapat: int, terkirim: int, dilewati: int}
     */
    public function kirimReminderTerdekat(): array
    {
        $timezone = (string) config('rapat.reminder.timezone', 'Asia/Jakarta');
        $menitSebelum = max(1, (int) config('rapat.reminder.menit_sebelum', 60));

        $sekarang = CarbonImmutable::now($timezone);
        $batas = $sekarang->addMinutes($menitSebelum);

        $rapats = RapatProject::query()
            ->where('status', 'dijadwalkan')
            ->where('waktu_rapat', '>=', $sekarang->toDateTimeString())
            ->where('waktu_rapat', '<=', $batas->toDateTimeString())
            ->with(['project', 'peserta.user'])
            ->get();

        $hasil = ['rapat' => 0, 'terkirim' => 0, 'dilewati' => 0];

        foreach ($rapats as $rapat) {
            $hasilRapat = $this->kirimUntukRapat($rapat);

            $hasil['rapat'] += $hasilRapat['rapat'];
            $hasil['terkirim'] += $hasilRapat['terkirim'];
            $hasil['dilewati'] += $hasilRapat['dilewati'];
        }

        return $hasil;
    }
}
