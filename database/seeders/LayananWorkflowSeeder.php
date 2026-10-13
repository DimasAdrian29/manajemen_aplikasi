<?php

namespace Database\Seeders;

use App\Models\Layanan;
use App\Models\LayananWorkflow;
use App\Models\MasterDokumenPengajuan;
use App\Models\Pengajuan;
use App\Models\PengajuanFieldData;
use App\Models\PengajuanWorkflowData;
use App\Models\Workflow;
use App\Models\WorkflowField;
use Illuminate\Database\Seeder;

class LayananWorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Master Workflows (Tahapan Siklus Hidup)
        $workflows = [
            [
                'nama' => 'Pengajuan Berkas OPD',
                'slug' => 'pengajuan-berkas',
                'deskripsi' => 'Pengisian formulir usulan, kebutuhan sistem, dan kelengkapan berkas persyaratan SPBE oleh OPD pemohon.',
                'ikon' => 'upload_file',
            ],
            [
                'nama' => 'Telaah Kelayakan APTIKA',
                'slug' => 'telaah-kelayakan',
                'deskripsi' => 'Pemeriksaan kelayakan teknis, arsitektur data SPBE, dan persetujuan usulan oleh Tim Telaah Bidang APTIKA.',
                'ikon' => 'fact_check',
            ],
            [
                'nama' => 'Inisiasi & Pengerjaan Proyek',
                'slug' => 'in_development',
                'deskripsi' => 'Pembentukan tim pengembang internal, perancangan SRS/ERD, dan pelaksanaan sprint coding aplikasi.',
                'ikon' => 'engineering',
            ],
            [
                'nama' => 'Uji Fungsional Aplikasi',
                'slug' => 'functional_testing',
                'deskripsi' => 'Pengujian fungsionalitas fitur, pengujian skenario pengguna (Blackbox Testing), dan Berita Acara Uji Fungsional.',
                'ikon' => 'checklist',
            ],
            [
                'nama' => 'Uji Keamanan Siber (CSIRT)',
                'slug' => 'security_testing',
                'deskripsi' => 'Audit kerentanan keamanan informasi, Penetration Testing (VAPT), dan penerbitan rekomendasi keamanan oleh Persandian.',
                'ikon' => 'security',
            ],
            [
                'nama' => 'Deployment & Go-Live',
                'slug' => 'deployment',
                'deskripsi' => 'Alokasi infrastruktur server/VM hosting, konfigurasi domain resmi riau.go.id, dan publikasi sistem ke katalog daerah.',
                'ikon' => 'rocket_launch',
            ],
            [
                'nama' => 'Decommissioning Server',
                'slug' => 'decommissioning',
                'deskripsi' => 'Pencadangan arsip data final, penutupan akses server hosting, dan pelepasan domain riau.go.id.',
                'ikon' => 'power_settings_new',
            ],
        ];

        $createdWorkflows = [];
        foreach ($workflows as $wf) {
            $createdWorkflows[$wf['slug']] = Workflow::firstOrCreate(['slug' => $wf['slug']], $wf);
        }

        // 2. Master Layanan
        $layanans = [
            [
                'nama' => 'Pembangunan Aplikasi Baru',
                'slug' => 'pembangunan-aplikasi-baru',
                'deskripsi' => 'Ajukan usulan pengembangan sistem informasi atau aplikasi baru untuk menunjang tugas pokok fungsi instansi Anda.',
                'ikon' => 'add_to_photos',
                'warna' => '#10B981',
                'memerlukan_aplikasi_eksisting' => false,
                'is_active' => true,
                'urutan' => 1,
                'workflows' => [
                    'pengajuan-berkas',
                    'telaah-kelayakan',
                    'in_development',
                    'functional_testing',
                    'security_testing',
                    'deployment',
                ],
            ],
            [
                'nama' => 'Pengembangan Aplikasi Eksisting',
                'slug' => 'pengembangan-aplikasi-eksisting',
                'deskripsi' => 'Permintaan pembaruan modul, integrasi interoperabilitas antar-layanan, atau perbaikan alur pada aplikasi yang sudah ada.',
                'ikon' => 'published_with_changes',
                'warna' => '#0D47A1',
                'memerlukan_aplikasi_eksisting' => true,
                'is_active' => true,
                'urutan' => 2,
                'workflows' => [
                    'pengajuan-berkas',
                    'telaah-kelayakan',
                    'in_development',
                    'functional_testing',
                    'security_testing',
                    'deployment',
                ],
            ],
            [
                'nama' => 'Penonaktifan Aplikasi',
                'slug' => 'penonaktifan-aplikasi',
                'deskripsi' => 'Pengajuan penghentian operasional, pencabutan domain sub-pemprov, atau migrasi data ke aplikasi terpadu nasional/provinsi.',
                'ikon' => 'power_settings_new',
                'warna' => '#F59E0B',
                'memerlukan_aplikasi_eksisting' => true,
                'is_active' => true,
                'urutan' => 3,
                'workflows' => [
                    'pengajuan-berkas',
                    'telaah-kelayakan',
                    'decommissioning',
                ],
            ],
        ];

        $createdLayanans = [];
        foreach ($layanans as $layananData) {
            $wfSlugs = $layananData['workflows'];
            unset($layananData['workflows']);

            $layanan = Layanan::firstOrCreate(['slug' => $layananData['slug']], $layananData);
            $layanan->update($layananData);
            $createdLayanans[$layanan->slug] = $layanan;

            // Hubungkan Layanan dengan Workflow sesuai urutan prioritas
            $urutan = 1;
            foreach ($wfSlugs as $slug) {
                if (isset($createdWorkflows[$slug])) {
                    LayananWorkflow::firstOrCreate(
                        [
                            'layanan_id' => $layanan->id,
                            'workflow_id' => $createdWorkflows[$slug]->id,
                        ],
                        [
                            'urutan' => $urutan++,
                            'is_active' => true,
                        ]
                    );
                }
            }
        }

        // 3. Konfigurasi Field Input pada Workflow 'Pengajuan Berkas OPD'
        $wfPengajuan = $createdWorkflows['pengajuan-berkas'];
        if ($wfPengajuan && $wfPengajuan->fields()->count() === 0) {
            $docProposal = MasterDokumenPengajuan::where('kode_dokumen', 'proposal_teknis')->first();
            $docSptjm = MasterDokumenPengajuan::where('kode_dokumen', 'sptjm')->first();
            $docSurat = MasterDokumenPengajuan::where('kode_dokumen', 'surat_pengantar')->first();

            $fields = [
                [
                    'label' => 'Proposal Teknis / KAK (Kerangka Acuan Kerja)',
                    'tipe' => 'file_upload',
                    'master_dokumen_id' => $docProposal?->id,
                    'wajib' => true,
                    'keterangan' => 'Memuat latar belakang, ruang lingkup proses bisnis, dan kebutuhan modul aplikasi.',
                    'urutan' => 1,
                ],
                [
                    'label' => 'Surat Pernyataan Tanggung Jawab Mutlak (SPTJM)',
                    'tipe' => 'file_upload',
                    'master_dokumen_id' => $docSptjm?->id,
                    'wajib' => true,
                    'keterangan' => 'Komitmen ketersediaan sumber daya manusia dan pemeliharaan aplikasi.',
                    'urutan' => 2,
                ],
                [
                    'label' => 'Surat Pengantar Kepala Dinas',
                    'tipe' => 'file_upload',
                    'master_dokumen_id' => $docSurat?->id,
                    'wajib' => false,
                    'keterangan' => 'Surat permohonan resmi bertandatangan Kepala Dinas / Kepala Badan.',
                    'urutan' => 3,
                ],
                [
                    'label' => 'Pakta Kepatuhan Arsitektur SPBE & Integrasi Data',
                    'tipe' => 'checklist',
                    'master_dokumen_id' => null,
                    'wajib' => true,
                    'keterangan' => 'Instansi pemohon menjamin kesiapan integrasi data sesuai standar Satu Data Indonesia & SPBE Provinsi Riau.',
                    'urutan' => 4,
                ],
            ];

            foreach ($fields as $fieldData) {
                WorkflowField::create(array_merge($fieldData, ['workflow_id' => $wfPengajuan->id]));
            }
        }

        // 4. Sinkronisasi Seluruh Pengajuan, Workflow Pipeline Data & Field Data
        self::syncAllPengajuanWorkflows();
    }

    /**
     * Sinkronisasi data pengajuan lama/baru ke alur pipeline workflow dinamis.
     */
    public static function syncAllPengajuanWorkflows(): void
    {
        $layananBaru = Layanan::where('slug', 'pembangunan-aplikasi-baru')->value('id');
        $layananPengembangan = Layanan::where('slug', 'pengembangan-aplikasi-eksisting')->value('id');
        $layananPenonaktifan = Layanan::where('slug', 'penonaktifan-aplikasi')->value('id');

        if ($layananBaru) {
            Pengajuan::where('jenis_pengajuan', 'new_app')->whereNull('layanan_id')->update(['layanan_id' => $layananBaru]);
        }
        if ($layananPengembangan) {
            Pengajuan::where('jenis_pengajuan', 'app_development')->whereNull('layanan_id')->update(['layanan_id' => $layananPengembangan]);
        }
        if ($layananPenonaktifan) {
            Pengajuan::where('jenis_pengajuan', 'app_deactivation')->whereNull('layanan_id')->update(['layanan_id' => $layananPenonaktifan]);
        }

        $pengajuans = Pengajuan::with(['layanan.workflows.workflow.fields', 'project', 'dokumen', 'instansi.users'])->get();

        foreach ($pengajuans as $pengajuan) {
            if (! $pengajuan->layanan) {
                continue;
            }

            $workflows = $pengajuan->layanan->workflows()->with('workflow.fields')->orderBy('urutan')->get();

            // Tentukan tahapan aktif berdasarkan status pengajuan dan progres proyek
            $activeSlug = match ($pengajuan->status) {
                'draft' => 'pengajuan-berkas',
                'submitted' => 'telaah-kelayakan',
                'under_review' => 'telaah-kelayakan',
                'rejected' => 'telaah-kelayakan',
                'approved' => match (true) {
                    $pengajuan->layanan->slug === 'penonaktifan-aplikasi' => 'decommissioning',
                    ! $pengajuan->project => 'in_development',
                    $pengajuan->project->status === 'live' => 'live',
                    $pengajuan->project->status === 'deployment' => 'deployment',
                    $pengajuan->project->status === 'security_testing' => 'security_testing',
                    $pengajuan->project->status === 'functional_testing' => 'functional_testing',
                    default => 'in_development',
                },
                default => 'pengajuan-berkas',
            };

            $targetIndex = null;
            foreach ($workflows as $idx => $lw) {
                if ($lw->workflow->slug === $activeSlug) {
                    $targetIndex = $idx;
                    break;
                }
            }

            foreach ($workflows as $idx => $lw) {
                if ($pengajuan->status === 'draft') {
                    $stepStatus = ($idx === 0) ? 'in_progress' : 'pending';
                } elseif ($pengajuan->status === 'rejected') {
                    if ($idx === 0) {
                        $stepStatus = 'completed';
                    } elseif ($idx === 1) {
                        $stepStatus = 'rejected';
                    } else {
                        $stepStatus = 'pending';
                    }
                } elseif ($activeSlug === 'live') {
                    $stepStatus = 'completed';
                } elseif ($targetIndex !== null) {
                    if ($idx < $targetIndex) {
                        $stepStatus = 'completed';
                    } elseif ($idx === $targetIndex) {
                        $stepStatus = ($pengajuan->status === 'submitted') ? 'pending' : 'in_progress';
                    } else {
                        $stepStatus = 'pending';
                    }
                } else {
                    $stepStatus = ($idx === 0) ? 'completed' : 'pending';
                }

                $completedBy = null;
                $completedAt = null;
                $catatan = null;

                if ($stepStatus === 'completed') {
                    $completedAt = $pengajuan->created_at?->copy()->addDays($idx * 2) ?? now();
                    if ($idx === 0) {
                        $completedBy = $pengajuan->instansi?->users()?->first()?->id;
                    } elseif ($idx === 1) {
                        $completedBy = $pengajuan->dinilai_oleh;
                        $catatan = $pengajuan->catatan_penilaian;
                    }
                } elseif ($stepStatus === 'rejected') {
                    $completedBy = $pengajuan->dinilai_oleh;
                    $completedAt = $pengajuan->tanggal_penilaian ?? now();
                    $catatan = $pengajuan->catatan_penilaian;
                }

                $pwData = PengajuanWorkflowData::updateOrCreate(
                    [
                        'pengajuan_id' => $pengajuan->id,
                        'layanan_workflow_id' => $lw->id,
                    ],
                    [
                        'status' => $stepStatus,
                        'completed_by' => $completedBy,
                        'completed_at' => $completedAt,
                        'catatan' => $catatan,
                    ]
                );

                // Sinkronisasi data field checklist / berkas
                foreach ($lw->workflow->fields as $field) {
                    $val = null;
                    $filePath = null;
                    $namaAsli = null;
                    $ukuranFile = null;

                    if ($field->tipe === 'checklist') {
                        $val = ($pengajuan->status !== 'draft') ? '1' : null;
                    } elseif ($field->tipe === 'file_upload' && $field->master_dokumen_id) {
                        $dok = $pengajuan->dokumen->firstWhere('master_dokumen_id', $field->master_dokumen_id);
                        if ($dok) {
                            $filePath = $dok->file_path;
                            $namaAsli = $dok->nama_file_asli;
                            $ukuranFile = $dok->ukuran_file;
                            $val = $dok->file_path;
                        }
                    }

                    if ($val || $filePath) {
                        PengajuanFieldData::updateOrCreate(
                            [
                                'pengajuan_workflow_data_id' => $pwData->id,
                                'workflow_field_id' => $field->id,
                            ],
                            [
                                'value' => $val,
                                'file_path' => $filePath,
                                'nama_file_asli' => $namaAsli,
                                'ukuran_file' => $ukuranFile,
                            ]
                        );
                    }
                }
            }

            // Hubungkan current_workflow_id pada project
            if ($pengajuan->project && ! $pengajuan->project->current_workflow_id) {
                $activeLw = $workflows->firstWhere('workflow.slug', $activeSlug);
                if (! $activeLw && $activeSlug === 'live') {
                    $activeLw = $workflows->last();
                }
                if ($activeLw) {
                    $pengajuan->project->update(['current_workflow_id' => $activeLw->id]);
                }
            }
        }
    }
}
