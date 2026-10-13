<?php

namespace App\Http\Controllers\Aptika;

use App\Http\Controllers\Controller;
use App\Http\Requests\Aptika\LayananRequest;
use App\Models\Layanan;
use App\Models\LayananWorkflow;
use App\Models\Workflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LayananController extends Controller
{
    /**
     * Tampilkan daftar master layanan fasilitasi aplikasi daerah.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $layanans = Layanan::withCount(['workflows', 'pengajuans'])
            ->with(['workflows.workflow'])
            ->when($search, function ($query, $search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            })
            ->orderBy('urutan')
            ->paginate(10)
            ->withQueryString();

        return view('aptika.layanan.index', compact('layanans', 'search'));
    }

    /**
     * Tampilkan form pembuatan layanan baru.
     */
    public function create(): View
    {
        $nextUrutan = (Layanan::max('urutan') ?? 0) + 1;

        return view('aptika.layanan.create', compact('nextUrutan'));
    }

    /**
     * Simpan layanan baru.
     */
    public function store(LayananRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $baseSlug = Str::slug($data['nama']);
        $slug = $baseSlug;
        $counter = 1;
        while (Layanan::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }
        $data['slug'] = $slug;

        if (empty($data['urutan'])) {
            $data['urutan'] = (Layanan::max('urutan') ?? 0) + 1;
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['memerlukan_aplikasi_eksisting'] = $request->boolean('memerlukan_aplikasi_eksisting');

        $layanan = Layanan::create($data);

        return redirect()->route('aptika.layanan.show', $layanan)
            ->with('success', "Layanan '{$layanan->nama}' berhasil dibuat. Silakan susun workflow untuk layanan ini.");
    }

    /**
     * Tampilkan detail layanan beserta susunan alur workflow-nya.
     */
    public function show(Layanan $layanan): View
    {
        $layanan->load([
            'workflows' => function ($q) {
                $q->orderBy('urutan')->with('workflow.fields');
            },
        ]);

        $assignedWorkflowIds = $layanan->workflows->pluck('workflow_id')->toArray();
        $availableWorkflows = Workflow::whereNotIn('id', $assignedWorkflowIds)->orderBy('nama')->get();

        return view('aptika.layanan.show', compact('layanan', 'availableWorkflows'));
    }

    /**
     * Tampilkan form edit layanan.
     */
    public function edit(Layanan $layanan): View
    {
        return view('aptika.layanan.edit', compact('layanan'));
    }

    /**
     * Perbarui data layanan.
     */
    public function update(LayananRequest $request, Layanan $layanan): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['memerlukan_aplikasi_eksisting'] = $request->boolean('memerlukan_aplikasi_eksisting');

        $layanan->update($data);

        return redirect()->route('aptika.layanan.show', $layanan)
            ->with('success', "Informasi layanan '{$layanan->nama}' berhasil diperbarui.");
    }

    /**
     * Hapus layanan (dengan safeguard jika sudah ada pengajuan).
     */
    public function destroy(Layanan $layanan): RedirectResponse
    {
        if ($layanan->pengajuans()->exists()) {
            return back()->with('error', 'Layanan ini tidak dapat dihapus karena sudah memiliki data usulan pengajuan. Anda dapat menonaktifkannya agar tidak muncul di portal OPD.');
        }

        $nama = $layanan->nama;
        $layanan->delete();

        return redirect()->route('aptika.layanan.index')
            ->with('success', "Layanan '{$nama}' berhasil dihapus dari sistem.");
    }

    /**
     * Toggle status aktif / nonaktif layanan.
     */
    public function toggle(Layanan $layanan): RedirectResponse
    {
        $layanan->update([
            'is_active' => ! $layanan->is_active,
        ]);

        $statusText = $layanan->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Layanan '{$layanan->nama}' berhasil {$statusText}.");
    }

    /**
     * Tambahkan sebuah master workflow ke susunan layanan ini.
     */
    public function attachWorkflow(Request $request, Layanan $layanan): RedirectResponse
    {
        $request->validate([
            'workflow_id' => ['required', 'exists:workflow,id'],
        ]);

        $workflowId = $request->input('workflow_id');

        if ($layanan->workflows()->where('workflow_id', $workflowId)->exists()) {
            return back()->with('error', 'Workflow tersebut sudah ada di dalam susunan layanan ini.');
        }

        $nextUrutan = ($layanan->workflows()->max('urutan') ?? 0) + 1;

        LayananWorkflow::create([
            'layanan_id' => $layanan->id,
            'workflow_id' => $workflowId,
            'urutan' => $nextUrutan,
            'is_active' => true,
        ]);

        return back()->with('success', 'Tahapan workflow berhasil ditambahkan ke dalam alur layanan.');
    }

    /**
     * Lepaskan sebuah workflow dari susunan layanan.
     */
    public function detachWorkflow(Layanan $layanan, LayananWorkflow $layananWorkflow): RedirectResponse
    {
        if ($layananWorkflow->layanan_id !== $layanan->id) {
            abort(404);
        }

        $layananWorkflow->delete();

        // Susun ulang nomor urutan agar tidak berlubang
        $this->normalizeUrutan($layanan);

        return back()->with('success', 'Tahapan workflow berhasil dihapus dari alur layanan ini.');
    }

    /**
     * Atur ulang urutan workflow (Naikkan / Turunkan).
     */
    public function reorderWorkflow(Request $request, Layanan $layanan): RedirectResponse
    {
        $request->validate([
            'layanan_workflow_id' => ['required', 'exists:layanan_workflow,id'],
            'direction' => ['required', 'in:up,down'],
        ]);

        $current = LayananWorkflow::where('layanan_id', $layanan->id)
            ->where('id', $request->input('layanan_workflow_id'))
            ->firstOrFail();

        $direction = $request->input('direction');

        DB::transaction(function () use ($layanan, $current, $direction) {
            if ($direction === 'up') {
                $swapWith = LayananWorkflow::where('layanan_id', $layanan->id)
                    ->where('urutan', '<', $current->urutan)
                    ->orderBy('urutan', 'desc')
                    ->first();
            } else {
                $swapWith = LayananWorkflow::where('layanan_id', $layanan->id)
                    ->where('urutan', '>', $current->urutan)
                    ->orderBy('urutan', 'asc')
                    ->first();
            }

            if ($swapWith) {
                $tempUrutan = $current->urutan;
                $current->update(['urutan' => $swapWith->urutan]);
                $swapWith->update(['urutan' => $tempUrutan]);
            }
        });

        return back()->with('success', 'Urutan prioritas workflow berhasil diperbarui.');
    }

    /**
     * Normalisasi nomor urutan agar berurutan 1, 2, 3...
     */
    private function normalizeUrutan(Layanan $layanan): void
    {
        $workflows = $layanan->workflows()->orderBy('urutan')->get();
        $urutan = 1;
        foreach ($workflows as $wf) {
            $wf->update(['urutan' => $urutan++]);
        }
    }
}
