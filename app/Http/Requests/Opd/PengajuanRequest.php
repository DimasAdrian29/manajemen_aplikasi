<?php

namespace App\Http\Requests\Opd;

use App\Models\MasterDokumenPengajuan;
use Illuminate\Contracts\Validation\ValidationRule;
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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $instansiId = auth()->user()->instansi_id;
        $jenis = $this->input('jenis_pengajuan');

        $rules = [
            'jenis_pengajuan' => ['required', Rule::in(['new_app', 'app_development', 'app_deactivation'])],
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

        // Dynamic Document Rules
        if ($jenis) {
            $masterDocs = MasterDokumenPengajuan::active()->berlakuUntuk($jenis)->get();
            $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
            $pengajuan = $this->route('pengajuan');

            foreach ($masterDocs as $doc) {
                $docRules = [];

                // Jika wajib
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
