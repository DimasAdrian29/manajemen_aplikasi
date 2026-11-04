<?php

namespace Tests\Feature;

use App\Models\Aplikasi;
use App\Models\Instansi;
use App\Models\Pengajuan;
use App\Models\PesertaRapat;
use App\Models\Project;
use App\Models\RapatProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReminderRapatTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminAptika;

    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.telegram.bot_token' => 'token-uji',
            'rapat.reminder.menit_sebelum' => 60,
            'rapat.reminder.timezone' => 'Asia/Jakarta',
        ]);

        Http::preventStrayRequests();

        Role::firstOrCreate(['name' => 'admin_aptika']);

        $this->adminAptika = User::factory()->create([
            'name' => 'Admin Aptika',
            'username' => 'admin_aptika_reminder',
            'email' => 'admin_reminder@test.com',
        ]);
        $this->adminAptika->assignRole('admin_aptika');

        $instansi = Instansi::create([
            'nama_instansi' => 'Dinas Komunikasi dan Informatika Provinsi Riau',
        ]);

        $aplikasi = Aplikasi::create([
            'instansi_id' => $instansi->id,
            'nama_aplikasi' => 'Aplikasi Reminder Rapat',
            'status' => 'aktif',
        ]);

        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'REG-REMINDER-01',
            'instansi_id' => $instansi->id,
            'aplikasi_id' => $aplikasi->id,
            'jenis_pengajuan' => 'new_app',
            'nama_aplikasi' => 'Aplikasi Reminder Rapat',
            'deskripsi_kebutuhan' => 'Pengujian reminder rapat via Telegram',
            'nama_pemohon' => 'Pemohon Uji',
            'no_hp_pemohon' => '08123456789',
            'status' => 'approved',
        ]);

        $this->project = Project::create([
            'pengajuan_id' => $pengajuan->id,
            'aplikasi_id' => $aplikasi->id,
            'nama_project' => 'Proyek Reminder Rapat',
            'status' => 'in_development',
        ]);
    }

    /**
     * @param  array<int, User>  $peserta
     */
    private function buatRapat(array $peserta, ?string $waktuRapat = null): RapatProject
    {
        $rapat = RapatProject::create([
            'project_id' => $this->project->id,
            'dibuat_oleh' => $this->adminAptika->id,
            'nama_rapat' => 'Rapat Koordinasi Mingguan',
            'waktu_rapat' => $waktuRapat ?? now('Asia/Jakarta')->addMinutes(30)->format('Y-m-d H:i:s'),
            'tempat_rapat' => 'Ruang Rapat Aptika',
            'deskripsi' => 'Pembahasan progres pengembangan aplikasi.',
            'status' => 'dijadwalkan',
        ]);

        foreach ($peserta as $user) {
            PesertaRapat::create([
                'rapat_project_id' => $rapat->id,
                'user_id' => $user->id,
                'status_kehadiran' => 'menunggu',
            ]);
        }

        return $rapat->load('peserta.user');
    }

    public function test_command_mengirim_reminder_telegram_dan_menandai_peserta(): void
    {
        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        $pesertaTelegram = User::factory()->create(['telegram_id' => '123456789']);
        $pesertaTanpaTelegram = User::factory()->create(['telegram_id' => null]);

        $rapat = $this->buatRapat([$pesertaTelegram, $pesertaTanpaTelegram]);

        $this->artisan('rapat:kirim-reminder')
            ->expectsOutputToContain('1 rapat, 1 peserta terkirim, 1 peserta dilewati')
            ->assertSuccessful();

        Http::assertSent(function (Request $request) {
            return str_contains($request->url(), 'sendMessage')
                && $request['chat_id'] === '123456789'
                && str_contains($request['text'], 'Rapat Koordinasi Mingguan')
                && str_contains($request['text'], 'Proyek Reminder Rapat');
        });

        $this->assertNotNull(
            PesertaRapat::where('rapat_project_id', $rapat->id)
                ->where('user_id', $pesertaTelegram->id)
                ->value('reminder_dikirim_at')
        );

        $this->assertNull(
            PesertaRapat::where('rapat_project_id', $rapat->id)
                ->where('user_id', $pesertaTanpaTelegram->id)
                ->value('reminder_dikirim_at')
        );

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $pesertaTelegram->id,
            'notifiable_type' => User::class,
        ]);

        $this->assertDatabaseMissing('notifications', [
            'notifiable_id' => $pesertaTanpaTelegram->id,
            'notifiable_type' => User::class,
        ]);
    }

    public function test_command_tidak_mengirim_ulang_reminder_yang_sudah_terkirim(): void
    {
        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        $peserta = User::factory()->create(['telegram_id' => '987654321']);

        $this->buatRapat([$peserta]);

        $this->artisan('rapat:kirim-reminder')->assertSuccessful();
        $this->artisan('rapat:kirim-reminder')
            ->expectsOutputToContain('1 peserta dilewati')
            ->assertSuccessful();

        Http::assertSentCount(1);
    }

    public function test_command_tidak_mengirim_reminder_untuk_rapat_di_luar_jendela_waktu(): void
    {
        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        $peserta = User::factory()->create(['telegram_id' => '1122334455']);

        $this->buatRapat([$peserta], now('Asia/Jakarta')->addHours(3)->format('Y-m-d H:i:s'));

        $this->artisan('rapat:kirim-reminder')
            ->expectsOutputToContain('0 rapat, 0 peserta terkirim, 0 peserta dilewati')
            ->assertSuccessful();

        Http::assertNothingSent();
    }

    public function test_command_gagal_jika_token_bot_belum_dikonfigurasi(): void
    {
        config(['services.telegram.bot_token' => null]);

        $this->artisan('rapat:kirim-reminder')
            ->expectsOutputToContain('TELEGRAM_BOT_TOKEN belum dikonfigurasi')
            ->assertFailed();

        Http::assertNothingSent();
    }

    public function test_admin_aptika_dapat_mengirim_reminder_dari_halaman_detail_rapat(): void
    {
        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        $peserta = User::factory()->create(['telegram_id' => '5566778899']);

        $rapat = $this->buatRapat([$peserta]);

        $response = $this->actingAs($this->adminAptika)
            ->patch(route('aptika.rapat.reminder', $rapat));

        $response->assertRedirect()
            ->assertSessionHas('success', 'Reminder Telegram berhasil dikirim ke 1 peserta rapat.');

        Http::assertSentCount(1);

        $this->assertNotNull(
            PesertaRapat::where('rapat_project_id', $rapat->id)->value('reminder_dikirim_at')
        );
    }
}
