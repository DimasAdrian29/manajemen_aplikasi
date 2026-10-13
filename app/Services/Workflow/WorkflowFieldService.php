<?php

namespace App\Services\Workflow;

use App\Models\PengajuanFieldData;
use App\Models\PengajuanWorkflowData;
use App\Models\WorkflowField;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class WorkflowFieldService
{
    /**
     * Simpan nilai formulir dinamis (teks, checklist, dan file) untuk tahapan workflow tertentu.
     *
     * @param  array{fields?: array<int|string, mixed>, files?: array<int|string, UploadedFile>}  $inputData
     */
    public function saveFieldValues(PengajuanWorkflowData $workflowData, array $inputData): void
    {
        $fields = $inputData['fields'] ?? [];
        $files = $inputData['files'] ?? [];

        // Simpan field teks / checklist
        foreach ($fields as $fieldId => $value) {
            $workflowField = WorkflowField::find($fieldId);
            if (! $workflowField) {
                continue;
            }

            $valString = is_bool($value) ? ($value ? '1' : '0') : (string) $value;

            PengajuanFieldData::updateOrCreate(
                [
                    'pengajuan_workflow_data_id' => $workflowData->id,
                    'workflow_field_id' => $workflowField->id,
                ],
                [
                    'value' => $valString,
                ]
            );
        }

        // Simpan field file upload
        foreach ($files as $fieldId => $file) {
            if (! ($file instanceof UploadedFile) || ! $file->isValid()) {
                continue;
            }

            $workflowField = WorkflowField::find($fieldId);
            if (! $workflowField) {
                continue;
            }

            // Hapus file lama jika ada
            $existing = PengajuanFieldData::where('pengajuan_workflow_data_id', $workflowData->id)
                ->where('workflow_field_id', $workflowField->id)
                ->first();

            if ($existing?->file_path && Storage::disk('public')->exists($existing->file_path)) {
                Storage::disk('public')->delete($existing->file_path);
            }

            $path = $file->store("workflow_files/{$workflowData->pengajuan_id}", 'public');

            PengajuanFieldData::updateOrCreate(
                [
                    'pengajuan_workflow_data_id' => $workflowData->id,
                    'workflow_field_id' => $workflowField->id,
                ],
                [
                    'file_path' => $path,
                    'nama_file_asli' => $file->getClientOriginalName(),
                    'ukuran_file' => $file->getSize(),
                ]
            );
        }
    }
}
