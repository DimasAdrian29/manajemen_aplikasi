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
                    $layanan->isAplikasiBaru() => 'new_app',
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
        $layananId = $this->input('layanan_id');

        $layanan = $layananId ? Layanan::with('workflows.workflow.fields.masterDokumen')->find($layananId) : null;
        $firstStep = $layanan?->workflows->first()?->workflow;
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $pengajuan = $this->route('pengajuan');

        $rules = [
            'layanan_id' => ['nullable', 'exists:layanan,id'],
            'jenis_pengajuan' => ['nullable', 'string'],
            'deskripsi_kebutuhan' => ['required', 'string', 'min:10'],
            'nama_pemohon' => ['required', 'string', 'max:255'],
            'no_hp_pemohon' => ['required', 'string', 'max:20'],
        ];

        // Validasi Nama Usulan Aplikasi vs Pilihan Aplikasi Terdaftar
        $isNewApp = $layanan ? $layanan->isAplikasiBaru() : ($jenis === 'new_app');
        if ($isNewApp) {
            $rules['nama_aplikasi'] = ['required', 'string', 'max:255'];
            $rules['aplikasi_id'] = ['nullable'];
        } else {
            if ($this->filled('nama_aplikasi') && ! $this->filled('aplikasi_id')) {
                $rules['nama_aplikasi'] = ['required', 'string', 'max:255'];
                $rules['aplikasi_id'] = ['nullable'];
            } else {
                $rules['aplikasi_id'] = [
                    'required',
                    Rule::exists('aplikasi', 'id')->where(function ($query) use ($instansiId) {
                        $query->where('instansi_id', $instansiId);
                    }),
                ];
                $rules['nama_aplikasi'] = ['nullable', 'string', 'max:255'];
            }
        }

        // Validasi Dokumen & Field Dinamis berdasarkan Workflow Layanan Terpilih
        if ($firstStep && $firstStep->fields->isNotEmpty()) {
            foreach ($firstStep->fields as $field) {
                if ($field->tipe === 'file_upload') {
                    $key = $field->master_dokumen_id ? "dokumen.{$field->master_dokumen_id}" : "workflow_files.{$field->id}";
                    $fieldRules = [];

                    if ($field->wajib) {
                        if ($isUpdate && $pengajuan) {
                            $alreadyUploaded = $field->master_dokumen_id
                                ? $pengajuan->dokumen()->where('master_dokumen_id', $field->master_dokumen_id)->exists()
                                : false;
                            $fieldRules[] = $alreadyUploaded ? 'nullable' : 'required';
                        } else {
                            $fieldRules[] = 'required';
                        }
                    } else {
                        $fieldRules[] = 'nullable';
                    }

                    $fieldRules[] = 'file';

                    if ($field->masterDokumen) {
                        if ($field->masterDokumen->format_diizinkan) {
                            $cleanFormats = str_replace([' ', '.'], '', $field->masterDokumen->format_diizinkan);
                            $fieldRules[] = 'mimes:'.$cleanFormats;
                        }
                        if ($field->masterDokumen->max_size_kb) {
                            $fieldRules[] = 'max:'.$field->masterDokumen->max_size_kb;
                        }
                    } else {
                        $fieldRules[] = 'mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg';
                        $fieldRules[] = 'max:10240';
                    }

                    $rules[$key] = $fieldRules;
                } elseif ($field->tipe === 'checklist' && $field->wajib) {
                    if ($this->has('fields') || $this->has("fields.{$field->id}")) {
                        $rules["fields.{$field->id}"] = ['accepted'];
                    }
                } elseif ($field->tipe === 'text') {
                    if ($this->has('fields') || $this->has("fields.{$field->id}")) {
                        $rules["fields.{$field->id}"] = $field->wajib ? ['required', 'string'] : ['nullable', 'string'];
                    }
                }
            }
        } elseif ($jenis) {
            // Fallback: jika belum ada field workflow tersendiri, gunakan master dokumen
            $masterDocs = MasterDokumenPengajuan::active()->berlakuUntuk($jenis)->get();
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

        if ($this->filled('layanan_id')) {
            $layanan = Layanan::with('workflows.workflow.fields')->find($this->input('layanan_id'));
            $firstStep = $layanan?->workflows->first()?->workflow;
            if ($firstStep) {
                foreach ($firstStep->fields as $field) {
                    if ($field->tipe === 'file_upload') {
                        $key = $field->master_dokumen_id ? "dokumen.{$field->master_dokumen_id}" : "workflow_files.{$field->id}";
                        $attributes[$key] = "Berkas {$field->label}";
                    } else {
                        $attributes["fields.{$field->id}"] = $field->label;
                    }
                }
            }
        }

        return $attributes;
    }
}
