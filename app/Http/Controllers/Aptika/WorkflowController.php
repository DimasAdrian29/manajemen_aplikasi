<?php

namespace App\Http\Controllers\Aptika;

use App\Http\Controllers\Controller;
use App\Http\Requests\Aptika\WorkflowRequest;
use App\Models\MasterDokumenPengajuan;
use App\Models\Workflow;
use App\Models\WorkflowField;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WorkflowController extends Controller
{
    /**
     * Tampilkan daftar master alur kerja (workflow steps).
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $workflows = Workflow::withCount(['fields', 'layananWorkflows'])
            ->with(['layananWorkflows.layanan'])
            ->when($search, function ($query, $search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('aptika.workflow.index', compact('workflows', 'search'));
    }

    /**
     * Form tambah workflow baru.
     */
    public function create(): View
    {
        return view('aptika.workflow.create');
    }

    /**
     * Simpan master workflow baru.
     */
    public function store(WorkflowRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $baseSlug = Str::slug($data['nama']);
        $slug = $baseSlug;
        $counter = 1;
        while (Workflow::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }
        $data['slug'] = $slug;

        $workflow = Workflow::create($data);

        return redirect()->route('aptika.workflow.show', $workflow)
            ->with('success', "Workflow '{$workflow->nama}' berhasil dibuat. Silakan tambahkan field/kelengkapan input.");
    }

    /**
     * Tampilkan detail workflow dan konfigurasi isian input / checklist / dokumen.
     */
    public function show(Workflow $workflow): View
    {
        $workflow->load([
            'fields' => function ($q) {
                $q->orderBy('urutan')->with('masterDokumen');
            },
            'layananWorkflows.layanan',
        ]);

        $masterDokumens = MasterDokumenPengajuan::active()->orderBy('urutan')->get();

        return view('aptika.workflow.show', compact('workflow', 'masterDokumens'));
    }

    /**
     * Form edit master workflow.
     */
    public function edit(Workflow $workflow): View
    {
        return view('aptika.workflow.edit', compact('workflow'));
    }

    /**
     * Perbarui data workflow.
     */
    public function update(WorkflowRequest $request, Workflow $workflow): RedirectResponse
    {
        $data = $request->validated();
        $workflow->update($data);

        return redirect()->route('aptika.workflow.show', $workflow)
            ->with('success', "Workflow '{$workflow->nama}' berhasil diperbarui.");
    }

    /**
     * Hapus workflow (dengan proteksi jika digunakan oleh alur layanan).
     */
    public function destroy(Workflow $workflow): RedirectResponse
    {
        if ($workflow->layananWorkflows()->exists()) {
            return back()->with('error', 'Workflow ini tidak dapat dihapus karena sedang digunakan dalam satu atau lebih alur layanan.');
        }

        $nama = $workflow->nama;
        $workflow->delete();

        return redirect()->route('aptika.workflow.index')
            ->with('success', "Workflow '{$nama}' berhasil dihapus.");
    }

    /**
     * Tambah field input/dokumen/checklist baru pada workflow.
     */
    public function storeField(Request $request, Workflow $workflow): RedirectResponse
    {
        $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'tipe' => ['required', 'in:checklist,text,file_upload'],
            'master_dokumen_id' => ['nullable', 'exists:master_dokumen_pengajuan,id'],
            'wajib' => ['nullable', 'boolean'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ]);

        $nextUrutan = ($workflow->fields()->max('urutan') ?? 0) + 1;

        WorkflowField::create([
            'workflow_id' => $workflow->id,
            'label' => $request->input('label'),
            'tipe' => $request->input('tipe'),
            'master_dokumen_id' => $request->input('tipe') === 'file_upload' ? $request->input('master_dokumen_id') : null,
            'wajib' => $request->boolean('wajib', false),
            'keterangan' => $request->input('keterangan'),
            'urutan' => $nextUrutan,
        ]);

        return back()->with('success', 'Field input berhasil ditambahkan ke dalam workflow.');
    }

    /**
     * Perbarui konfigurasi field.
     */
    public function updateField(Request $request, Workflow $workflow, WorkflowField $field): RedirectResponse
    {
        if ($field->workflow_id !== $workflow->id) {
            abort(404);
        }

        $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'tipe' => ['required', 'in:checklist,text,file_upload'],
            'master_dokumen_id' => ['nullable', 'exists:master_dokumen_pengajuan,id'],
            'wajib' => ['nullable', 'boolean'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ]);

        $field->update([
            'label' => $request->input('label'),
            'tipe' => $request->input('tipe'),
            'master_dokumen_id' => $request->input('tipe') === 'file_upload' ? $request->input('master_dokumen_id') : null,
            'wajib' => $request->boolean('wajib', false),
            'keterangan' => $request->input('keterangan'),
        ]);

        return back()->with('success', 'Field input berhasil diperbarui.');
    }

    /**
     * Hapus field dari workflow.
     */
    public function destroyField(Workflow $workflow, WorkflowField $field): RedirectResponse
    {
        if ($field->workflow_id !== $workflow->id) {
            abort(404);
        }

        $field->delete();
        $this->normalizeUrutanFields($workflow);

        return back()->with('success', 'Field input berhasil dihapus dari workflow.');
    }

    /**
     * Atur urutan field (Naikkan / Turunkan).
     */
    public function reorderField(Request $request, Workflow $workflow): RedirectResponse
    {
        $request->validate([
            'field_id' => ['required', 'exists:workflow_field,id'],
            'direction' => ['required', 'in:up,down'],
        ]);

        $current = WorkflowField::where('workflow_id', $workflow->id)
            ->where('id', $request->input('field_id'))
            ->firstOrFail();

        $direction = $request->input('direction');

        DB::transaction(function () use ($workflow, $current, $direction) {
            if ($direction === 'up') {
                $swapWith = WorkflowField::where('workflow_id', $workflow->id)
                    ->where('urutan', '<', $current->urutan)
                    ->orderBy('urutan', 'desc')
                    ->first();
            } else {
                $swapWith = WorkflowField::where('workflow_id', $workflow->id)
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

        return back()->with('success', 'Urutan field input berhasil diperbarui.');
    }

    /**
     * Normalisasi nomor urutan field agar berurutan.
     */
    private function normalizeUrutanFields(Workflow $workflow): void
    {
        $fields = $workflow->fields()->orderBy('urutan')->get();
        $urutan = 1;
        foreach ($fields as $f) {
            $f->update(['urutan' => $urutan++]);
        }
    }
}
