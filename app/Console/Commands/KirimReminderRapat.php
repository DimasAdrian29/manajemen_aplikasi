<?php

namespace App\Console\Commands;

use App\Models\RapatProject;
use App\Services\RapatReminderService;
use App\Services\TelegramService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('rapat:kirim-reminder {--rapat= : ID rapat tertentu yang ingin dikirimi reminder} {--paksa : Kirim ulang meski reminder sudah pernah dikirim}')]
#[Description('Kirim reminder rapat melalui Telegram ke seluruh peserta yang memiliki ID Telegram.')]
class KirimReminderRapat extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(RapatReminderService $reminderService, TelegramService $telegramService): int
    {
        if (! $telegramService->isConfigured()) {
            $this->error('TELEGRAM_BOT_TOKEN belum dikonfigurasi. Reminder Telegram tidak dapat dikirim.');

            return self::FAILURE;
        }

        $rapatId = $this->option('rapat');

        if (filled($rapatId)) {
            $rapat = RapatProject::find($rapatId);

            if (! $rapat) {
                $this->error("Rapat dengan ID {$rapatId} tidak ditemukan.");

                return self::FAILURE;
            }

            $hasil = $reminderService->kirimUntukRapat($rapat, (bool) $this->option('paksa'));
        } else {
            $hasil = $reminderService->kirimReminderTerdekat();
        }

        $this->info("Reminder rapat diproses: {$hasil['rapat']} rapat, {$hasil['terkirim']} peserta terkirim, {$hasil['dilewati']} peserta dilewati.");

        return self::SUCCESS;
    }
}
