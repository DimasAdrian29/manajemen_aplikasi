<?php

namespace App\Http\Requests\Infrastruktur;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class HostingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->hasRole('admin_infrastruktur');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'nama_domain' => ['required', 'string', 'max:255'],
            'url_aplikasi' => ['required', 'url', 'max:255'],
            'ip_server' => ['nullable', 'ip'],
            'nama_server' => ['nullable', 'string', 'max:255'],
            'jenis_hosting' => ['required', 'in:vps,shared,container,cloud,on_premise'],
            'sistem_operasi' => ['nullable', 'string', 'max:255'],
            'cpu_core' => ['nullable', 'integer', 'min:1', 'max:256'],
            'ram_gb' => ['nullable', 'integer', 'min:1', 'max:1024'],
            'storage_gb' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'web_server' => ['nullable', 'string', 'max:100'],
            'database_server' => ['nullable', 'string', 'max:100'],
            'ssl_aktif' => ['boolean'],
            'ssl_kadaluarsa' => ['nullable', 'date'],
            'tanggal_live' => ['nullable', 'date'],
            'status_hosting' => ['required', 'in:persiapan,aktif,maintenance,nonaktif'],
            'spesifikasi_hosting' => ['nullable', 'string'],
            'keterangan' => ['nullable', 'string'],
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['alasan_perubahan'] = ['required', 'string', 'min:5', 'max:1000'];
        }

        return $rules;
    }

    /**
     * Prepare inputs for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'ssl_aktif' => filter_var($this->ssl_aktif, FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
