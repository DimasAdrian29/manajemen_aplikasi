<?php

namespace App\Http\Requests\Opd;

use App\Models\Layanan;
use App\Models\MasterDokumenPengajuan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PengajuanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare inputs for validation to ensure backward and forward compatibility.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('layanan_id') && ! $this->filled('jenis_pengajuan')) {
            $layanan = Layanan::find($this->input('layanan_id'));
            if ($layanan) {
                $jenis = match (true) {
                    str_contains($layanan->slug, 'baru') => 'new_app',
                    str_contains($layanan->slug, 'nonaktif') || str_contains($layanan->slug, 'deactivation') => 'app_deactivation',
                    default => 'app_development',
                };
                $this->merge(['jenis_pengajuan' => $jenis]);
            }
        } elseif ($this->filled('jenis_pengajuan') && ! $this->filled('layanan_id')) {
            $slug = match ($this->input('jenis_pengajuan')) {
                'new_app' => 'pembangunan-aplikasi-baru',
                'app_development' => 'pengembangan-aplikasi-eksisting',
                'app_deactivation' => 'penonaktifan-aplikasi',
                default => null,
            };
            if ($slug) {
                $layanan = Layanan::where('slug', $slug)->first();
                if ($layanan) {
                    $this->merge(['layanan_id' => $layanan->id]);
                }
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        $instansiId = auth()->user()->instansi_id;
        $jenis = $this->input('jenis_pengajuan');

        $rules = [
            'layanan_id' => ['nullable', 'exists:layanan,id'],
            'jenis_pengajuan' => ['required', 'string'],
            'nama_aplikasi' => ['required_if:jenis_pengajuan,new_app', 'nullable', 'string', 'max:255'],
            'aplikasi_id' => [
                'required_if:jenis_pengajuan,app_development,app_deactivation',
                'nullable',
                Rule::exists('aplikasi', 'id')->where(function ($query) use ($instansiId) {
                    $query->where('instansi_id', $instansiId);
                }),
            ],
            'deskripsi_kebutuhan' => ['required', 'string', 'min:10'],
            'nama_pemohon' => ['required', 'string', 'max:255'],
            'no_hp_pemohon' => ['required', 'string', 'max:20'],
        ];

        // Validasi Dokumen (baik melalui master dokumen maupun workflow fields)
        if ($jenis) {
            $masterDocs = MasterDokumenPengajuan::active()->berlakuUntuk($jenis)->get();
            $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
            $pengajuan = $this->route('pengajuan');

            foreach ($masterDocs as $doc) {
                $docRules = [];

                if ($doc->wajib) {
                    if ($isUpdate && $pengajuan) {
                        $alreadyUploaded = $pengajuan->dokumen()->where('master_dokumen_id', $doc->id)->exists();
                        $docRules[] = $alreadyUploaded ? 'nullable' : 'required';
                    } else {
                        $docRules[] = 'required';
                    }
                } else {
                    $docRules[] = 'nullable';
                }

                $docRules[] = 'file';

                if ($doc->format_diizinkan) {
                    $cleanFormats = str_replace([' ', '.'], '', $doc->format_diizinkan);
                    $docRules[] = 'mimes:'.$cleanFormats;
                }

                if ($doc->max_size_kb) {
                    $docRules[] = 'max:'.$doc->max_size_kb;
                }

                $rules["dokumen.{$doc->id}"] = $docRules;
            }
        }

        // Validasi Field Workflow Tambahan (Checklist & Teks Wajib) jika disertakan dari formulir alur
        if ($this->has('fields') && $this->filled('layanan_id')) {
            $layanan = Layanan::with('workflows.workflow.fields')->find($this->input('layanan_id'));
            $firstStep = $layanan?->workflows->first()?->workflow;
            if ($firstStep) {
                foreach ($firstStep->fields as $wfField) {
                    if ($wfField->tipe === 'checklist' && $wfField->wajib) {
                        $rules["fields.{$wfField->id}"] = ['accepted'];
                    } elseif ($wfField->tipe === 'text' && $wfField->wajib) {
                        $rules["fields.{$wfField->id}"] = ['required', 'string'];
                    }
                }
            }
        }

        return $rules;
    }

    /**
     * Custom validation attribute names.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $attributes = [
            'layanan_id' => 'Jenis Layanan',
            'jenis_pengajuan' => 'Jenis Permohonan',
            'nama_aplikasi' => 'Nama Usulan Aplikasi',
            'aplikasi_id' => 'Pilihan Aplikasi',
            'deskripsi_kebutuhan' => 'Deskripsi Kebutuhan & Ruang Lingkup',
            'nama_pemohon' => 'Nama Pejabat / Narahubung Pemohon',
            'no_hp_pemohon' => 'Nomor WhatsApp / HP Pemohon',
        ];

        $masterDocs = MasterDokumenPengajuan::all();
        foreach ($masterDocs as $doc) {
            $attributes["dokumen.{$doc->id}"] = "Berkas {$doc->nama_dokumen}";
        }

        return $attributes;
    }
}
