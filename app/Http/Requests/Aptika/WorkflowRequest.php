<?php

namespace App\Http\Requests\Aptika;

use Illuminate\Foundation\Http\FormRequest;

class WorkflowRequest extends FormRequest
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
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'ikon' => ['nullable', 'string', 'max:50'],
            'role_id' => ['nullable', 'exists:roles,id'],
        ];
    }
}
