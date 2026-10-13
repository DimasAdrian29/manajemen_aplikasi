@props([
    'workflow' => null,
    'workflowData' => null,
    'readonly' => false,
])

@php
    $targetWorkflow = $workflow instanceof \App\Models\LayananWorkflow ? $workflow->workflow : $workflow;
    $fields = $targetWorkflow ? $targetWorkflow->fields()->orderBy('urutan')->get() : collect();
    $existingData = $workflowData ? $workflowData->fieldData->keyBy('workflow_field_id') : collect();
@endphp

@if($fields->isNotEmpty())
    <div class="bg-slate-50/70 dark:bg-slate-800/40 p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-700/80 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200/80 dark:border-slate-700 pb-3">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[#0D47A1] dark:text-blue-400 text-lg">fact_check</span>
                <div>
                    <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">
                        Formulir & Checklist Dinamis: {{ $targetWorkflow->nama }}
                    </h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        Parameter evaluasi dan verifikasi yang dikonfigurasikan pada master workflow.
                    </p>
                </div>
            </div>
            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-blue-50 text-[#0D47A1] border border-blue-200/50">
                {{ $fields->count() }} Parameter
            </span>
        </div>

        <div class="space-y-4 pt-1">
            @foreach($fields as $field)
                @php
                    $savedRecord = $existingData->get($field->id);
                    $val = old("workflow_fields.{$field->id}", $savedRecord?->value);
                    $filePath = $savedRecord?->file_path;
                    $fileName = $savedRecord?->nama_file_asli;
                @endphp

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-200">
                        {{ $field->label }}
                        @if($field->wajib)
                            <span class="text-rose-500">*</span>
                        @endif
                    </label>

                    @if($field->tipe === 'checklist')
                        <label class="inline-flex items-center gap-2.5 cursor-pointer mt-0.5">
                            <input 
                                type="checkbox" 
                                name="workflow_fields[{{ $field->id }}]" 
                                value="1" 
                                {{ $val === '1' || $val === 'true' ? 'checked' : '' }}
                                {{ $readonly ? 'disabled' : '' }}
                                class="rounded border-slate-300 text-[#0D47A1] focus:ring-[#0D47A1] h-4 w-4 transition"
                            >
                            <span class="text-xs text-slate-600 dark:text-slate-400">
                                {{ $field->keterangan ?: 'Telah diverifikasi dan memenuhi standar ketentuan.' }}
                            </span>
                        </label>

                    @elseif($field->tipe === 'text')
                        <textarea 
                            name="workflow_fields[{{ $field->id }}]" 
                            rows="2" 
                            {{ $readonly ? 'readonly' : '' }}
                            placeholder="{{ $field->keterangan ?: 'Tuliskan catatan atau rincian hasil evaluasi...' }}"
                            class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900/60 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] p-3 transition"
                        >{{ $val }}</textarea>

                    @elseif($field->tipe === 'file_upload')
                        @if(! $readonly)
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                <input 
                                    type="file" 
                                    name="workflow_files[{{ $field->id }}]" 
                                    class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#0D47A1]/10 file:text-[#0D47A1] hover:file:bg-[#0D47A1]/20 transition"
                                >
                            </div>
                        @endif

                        @if($filePath)
                            <div class="flex items-center gap-2 mt-1 p-2 rounded-lg bg-emerald-50/80 border border-emerald-200/70 text-xs text-emerald-800">
                                <span class="material-symbols-outlined text-sm text-emerald-600">check_circle</span>
                                <span class="font-medium truncate max-w-xs">{{ $fileName ?: basename($filePath) }}</span>
                                <a 
                                    href="{{ \Illuminate\Support\Facades\Storage::url($filePath) }}" 
                                    target="_blank" 
                                    class="ml-auto inline-flex items-center gap-1 text-[11px] font-bold text-[#0D47A1] hover:underline"
                                >
                                    <span class="material-symbols-outlined text-xs">download</span> Unduh
                                </a>
                            </div>
                        @elseif($readonly)
                            <span class="text-xs text-slate-400 italic">Tidak ada berkas yang diunggah.</span>
                        @endif

                        @if($field->keterangan)
                            <p class="text-[11px] text-slate-400 mt-0.5">{{ $field->keterangan }}</p>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif
