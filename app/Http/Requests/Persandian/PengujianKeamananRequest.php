<?php

namespace App\Http\Requests\Persandian;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PengujianKeamananRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->hasRole('admin_persandian');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kategori_pengujian' => ['required', 'in:vulnerability_assessment,penetration_testing,source_code_review,audit_konfigurasi'],
            'hasil' => ['required', 'in:lulus,tidak_lulus,perbaikan'],
            'tingkat_risiko' => ['required', 'in:aman,rendah,sedang,tinggi,kritis'],
            'tanggal_uji' => ['required', 'date'],
            'nomor_surat' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:2000'],
            'catatan_kerentanan' => ['nullable', 'string', 'max:5000'],
            'rekomendasi' => ['nullable', 'string', 'max:5000'],
            'file_hasil' => ['nullable', 'file', 'mimes:pdf,doc,docx,zip,rar', 'max:20480'],
        ];
    }
}
