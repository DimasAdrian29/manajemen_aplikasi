<?php

namespace App\Http\Requests\Superadmin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AplikasiRequest extends FormRequest
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
        return [
            'instansi_id' => ['required', 'exists:instansi,id'],
            'nama_aplikasi' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'url_aplikasi' => ['nullable', 'url', 'max:255'],
            'status' => ['required', 'in:pengembangan,aktif,nonaktif'],
        ];
    }

    /**
     * Custom attribute names.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'instansi_id' => 'Instansi OPD Pemilik',
            'nama_aplikasi' => 'Nama Aplikasi',
            'deskripsi' => 'Deskripsi Aplikasi',
            'url_aplikasi' => 'URL Aplikasi',
            'status' => 'Status Operasional',
        ];
    }
}
