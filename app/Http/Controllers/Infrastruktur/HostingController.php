<?php

namespace App\Http\Controllers\Infrastruktur;

use App\Http\Controllers\Controller;
use App\Http\Requests\Infrastruktur\HostingRequest;
use App\Models\HostingAplikasi;
use App\Models\RiwayatHosting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HostingController extends Controller
{
    /**
     * Display a listing of servers and applications hosting.
     */
    public function index(Request $request): View
    {
        $query = HostingAplikasi::with([
            'project.aplikasi.instansi',
            'adminInfrastruktur',
        ]);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_domain', 'like', "%{$search}%")
                    ->orWhere('url_aplikasi', 'like', "%{$search}%")
                    ->orWhere('ip_server', 'like', "%{$search}%")
                    ->orWhere('nama_server', 'like', "%{$search}%")
                    ->orWhereHas('project', function ($pQ) use ($search) {
                        $pQ->where('nama_project', 'like', "%{$search}%");
                    })
                    ->orWhereHas('project.aplikasi', function ($appQ) use ($search) {
                        $appQ->where('nama_aplikasi', 'like', "%{$search}%");
                    })
                    ->orWhereHas('project.aplikasi.instansi', function ($instQ) use ($search) {
                        $instQ->where('nama_instansi', 'like', "%{$search}%");
                    });
            });
        }

        if ($jenis = $request->input('jenis_hosting')) {
            $query->where('jenis_hosting', $jenis);
        }

        if ($status = $request->input('status_hosting')) {
            $query->where('status_hosting', $status);
        }

        if ($ssl = $request->input('ssl')) {
            if ($ssl === 'aktif') {
                $query->where('ssl_aktif', true);
            } elseif ($ssl === 'nonaktif') {
                $query->where('ssl_aktif', false);
            } elseif ($ssl === 'expiring') {
                $query->where('ssl_aktif', true)
                    ->whereNotNull('ssl_kadaluarsa')
                    ->whereBetween('ssl_kadaluarsa', [now()->startOfDay(), now()->addDays(30)->endOfDay()]);
            }
        }

        $hostings = $query->latest()->paginate(10)->withQueryString();

        return view('infrastruktur.hosting.index', compact('hostings'));
    }

    /**
     * Display detail of a hosting record with change history.
     */
    public function show(HostingAplikasi $hosting): View
    {
        $hosting->load([
            'project.aplikasi.instansi',
            'project.tim.user',
            'adminInfrastruktur',
            'riwayat.user',
        ]);

        return view('infrastruktur.hosting.show', compact('hosting'));
    }

    /**
     * Show form for editing hosting data.
     */
    public function edit(HostingAplikasi $hosting): View
    {
        $hosting->load(['project.aplikasi.instansi']);

        return view('infrastruktur.hosting.edit', compact('hosting'));
    }

    /**
     * Update the hosting resource and record audit trail history.
     */
    public function update(HostingRequest $request, HostingAplikasi $hosting): RedirectResponse
    {
        $validated = $request->validated();
        $alasanPerubahan = $validated['alasan_perubahan'];
        unset($validated['alasan_perubahan']);

        DB::transaction(function () use ($hosting, $validated, $alasanPerubahan) {
            // Snapshot previous data
            RiwayatHosting::create([
                'hosting_aplikasi_id' => $hosting->id,
                'user_id' => auth()->id(),
                'data_sebelum' => [
                    'nama_domain' => $hosting->nama_domain,
                    'url_aplikasi' => $hosting->url_aplikasi,
                    'ip_server' => $hosting->ip_server,
                    'nama_server' => $hosting->nama_server,
                    'jenis_hosting' => $hosting->jenis_hosting,
                    'sistem_operasi' => $hosting->sistem_operasi,
                    'cpu_core' => $hosting->cpu_core,
                    'ram_gb' => $hosting->ram_gb,
                    'storage_gb' => $hosting->storage_gb,
                    'web_server' => $hosting->web_server,
                    'database_server' => $hosting->database_server,
                    'ssl_aktif' => (bool) $hosting->ssl_aktif,
                    'ssl_kadaluarsa' => $hosting->ssl_kadaluarsa?->toDateString(),
                    'tanggal_live' => $hosting->tanggal_live?->toDateString(),
                    'status_hosting' => $hosting->status_hosting,
                    'spesifikasi_hosting' => $hosting->spesifikasi_hosting,
                    'keterangan' => $hosting->keterangan,
                ],
                'alasan_perubahan' => $alasanPerubahan,
            ]);

            // Update hosting
            $hosting->update($validated);

            // Sync with related Aplikasi
            if ($hosting->project && $hosting->project->aplikasi) {
                $aplikasi = $hosting->project->aplikasi;
                $updateData = ['url_aplikasi' => $hosting->url_aplikasi];

                if ($hosting->status_hosting === 'maintenance') {
                    $updateData['status'] = 'maintenance';
                } elseif ($hosting->status_hosting === 'nonaktif') {
                    $updateData['status'] = 'nonaktif';
                } elseif ($hosting->status_hosting === 'aktif' && $aplikasi->status !== 'aktif') {
                    $updateData['status'] = 'aktif';
                }

                $aplikasi->update($updateData);
            }
        });

        return redirect()
            ->route('infrastruktur.hosting.show', $hosting)
            ->with('success', 'Data hosting dan riwayat perubahan berhasil diperbarui.');
    }
}
