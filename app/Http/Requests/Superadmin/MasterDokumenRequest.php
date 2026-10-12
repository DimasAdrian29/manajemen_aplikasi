<?php

namespace App\Http\Requests\Superadmin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MasterDokumenRequest extends FormRequest
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
        $masterDokumenId = $this->route('master_dokumen')?->id ?? $this->route('master_dokuman');

        return [
            'nama_dokumen' => ['required', 'string', 'max:255'],
            'kode_dokumen' => [
                'required',
                'string',
                'max:100',
                'alpha_dash',
                Rule::unique('master_dokumen_pengajuan', 'kode_dokumen')->ignore($masterDokumenId),
            ],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'wajib' => ['nullable', 'boolean'],
            'berlaku_untuk' => ['required', Rule::in(['semua', 'new_app', 'app_development', 'app_deactivation'])],
            'format_diizinkan' => ['required', 'string', 'max:50'],
            'max_size_kb' => ['required', 'integer', 'min:100', 'max:51200'],
            'file_template' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx', 'max:10240'],
            'is_active' => ['nullable', 'boolean'],
            'urutan' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Prepare data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'wajib' => $this->boolean('wajib'),
            'is_active' => $this->has('is_active') ? $this->boolean('is_active') : true,
        ]);
    }

    /**
     * Custom attribute names.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nama_dokumen' => 'Nama Dokumen',
            'kode_dokumen' => 'Kode Identifier Dokumen',
            'deskripsi' => 'Petunjuk / Deskripsi Dokumen',
            'berlaku_untuk' => 'Jenis Permohonan',
            'format_diizinkan' => 'Format Berkas',
            'max_size_kb' => 'Batas Ukuran Maksimal (KB)',
            'file_template' => 'Berkas Format Template',
            'urutan' => 'Urutan Tampil',
        ];
    }
}
