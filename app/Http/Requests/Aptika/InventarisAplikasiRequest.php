<?php

namespace App\Http\Requests\Aptika;

use App\Models\InventarisAplikasi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InventarisAplikasiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalisasi checkbox yang tidak dicentang menjadi false.
     */
    protected function prepareForValidation(): void
    {
        $booleans = ['is_aplikasi_pemprov', 'is_website_resmi', ...array_keys(InventarisAplikasi::DOKUMEN)];

        $this->merge(collect($booleans)->mapWithKeys(fn ($kolom) => [$kolom => $this->boolean($kolom)])->all());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        $inventaris = $this->route('inventaris_aplikasi');

        $rules = [
            'nomor_registrasi' => ['nullable', 'integer', 'min:1', Rule::unique('inventaris_aplikasi', 'nomor_registrasi')->ignore($inventaris)],
            'nama_aplikasi' => ['required', 'string', 'max:255'],
            'instansi_id' => ['required', 'exists:instansi,id'],
            'status' => ['nullable', Rule::in(array_keys(InventarisAplikasi::STATUS))],
            'aplikasi_pengganti_id' => [
                'nullable',
                'required_if:status,diganti',
                'exists:inventaris_aplikasi,id',
                Rule::notIn(array_filter([$inventaris?->id])),
            ],
            'is_aplikasi_pemprov' => ['boolean'],
            'is_website_resmi' => ['boolean'],
            'pic_aplikasi' => ['nullable', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
            'fungsi_menu' => ['nullable', 'string', 'max:5000'],
            'platform' => ['nullable', Rule::in(array_keys(InventarisAplikasi::PLATFORM))],
            'jenis_layanan' => ['nullable', Rule::in(array_keys(InventarisAplikasi::JENIS_LAYANAN))],
            'konektivitas' => ['nullable', Rule::in(array_keys(InventarisAplikasi::KONEKTIVITAS))],
            'url' => ['nullable', 'string', 'max:255'],
            'tanggal_pemberlakuan' => ['nullable', 'date'],
            'penempatan_server' => ['nullable', 'string', 'max:100'],
            'status_pengembangan' => ['nullable', Rule::in(array_keys(InventarisAplikasi::STATUS_PENGEMBANGAN))],
            'catatan' => ['nullable', 'string', 'max:5000'],
        ];

        foreach (array_keys(InventarisAplikasi::TIM_TEKNIS) as $kolom) {
            $rules[$kolom] = ['nullable', 'string', 'max:2000'];
        }

        foreach (array_keys(InventarisAplikasi::DOKUMEN) as $kolom) {
            $rules[$kolom] = ['boolean'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nomor_registrasi' => 'nomor registrasi',
            'nama_aplikasi' => 'nama aplikasi',
            'instansi_id' => 'pengelola (OPD)',
            'aplikasi_pengganti_id' => 'aplikasi pengganti',
            'tanggal_pemberlakuan' => 'tanggal pemberlakuan',
            'status_pengembangan' => 'status pengembangan',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'aplikasi_pengganti_id.required_if' => 'Aplikasi pengganti wajib dipilih jika status aplikasi Diganti.',
            'aplikasi_pengganti_id.not_in' => 'Aplikasi tidak dapat menggantikan dirinya sendiri.',
        ];
    }
}
