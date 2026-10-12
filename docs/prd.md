# PRD — Sistem Manajemen Pengembangan Aplikasi
**Dinas Komunikasi, Informatika & Statistik Provinsi Riau**

> **Status:** Draft v0.1 · 1 Oktober 2026
> **Catatan:** Dokumen ini masih bersifat living document. Bagian yang belum ditentukan ditandai 🔲 TBD.

---

## 1. Latar Belakang

Dinas Kominfo Provinsi Riau membutuhkan sistem terpusat untuk mengelola siklus hidup pengembangan aplikasi yang diajukan oleh berbagai Organisasi Perangkat Daerah (OPD). Proses yang saat ini berjalan belum terdigitalisasi secara penuh, sehingga menyulitkan koordinasi antar bidang (Aptika, Infrastruktur, Persandian) dan pemantauan progres oleh pimpinan.

---

## 2. Tujuan Sistem

- Digitalisasi alur permohonan pembangunan / pengembangan / penonaktifan aplikasi oleh OPD.
- Transparansi proses tinjauan, inisiasi project, pengujian, dan hosting.
- Pemantauan progres pengembangan secara real-time oleh Ketua Tim dan manajemen.
- Dokumentasi terpusat (proposal, dokumen teknis, hasil uji keamanan, URL hosting).

---

## 3. Aktor & Role

| Role | Deskripsi | Status |
|---|---|---|
| `superadmin` | Akses penuh ke seluruh sistem, manajemen user & konfigurasi | ✅ Defined |
| `opd` | Instansi pengaju permohonan aplikasi | ✅ Defined |
| `admin_aptika` | Bidang Aptika — reviewer, inisiator project, penguji fungsional | ✅ Defined |
| `admin_infrastruktur` | Bidang Infrastruktur — pengelola hosting | ✅ Defined |
| `admin_persandian` | Bidang Persandian — penguji keamanan | 🔲 Prosedur TBD |
| `ketua_tim` | Ketua tim project — pencatat progres | ✅ Defined |
| `kepala_bidang` | Kepala Bidang | 🔲 TBD |
| `kepala_dinas` | Kepala Dinas | 🔲 TBD |

> **Catatan:** Role `kepala_dinas` ditambahkan ke sistem (belum ada sebelumnya).

---

## 4. Jenis Permohonan

OPD dapat mengajukan 3 jenis permohonan:

| Kode | Jenis | Keterangan |
|---|---|---|
| `new_app` | Pembangunan Aplikasi Baru | Aplikasi belum pernah ada sebelumnya |
| `app_development` | Pengembangan Aplikasi | Penambahan fitur / perbaikan aplikasi yang sudah ada |
| `app_deactivation` | Penonaktifan Aplikasi | Permintaan menonaktifkan aplikasi yang sudah tidak digunakan |

---

## 5. Alur Kerja Utama

### 5.1 Alur Permohonan OPD

```
OPD mengajukan permohonan
    ↓
Upload dokumen (Proposal + Dokumen Teknis)
    ↓
Status: MENUNGGU TINJAUAN
    ↓
admin_aptika meninjau permohonan
    ↓
    ├── Ditolak → OPD diberi notifikasi + alasan
    └── Disetujui → Inisiasi Project
```

### 5.2 Alur Inisiasi Project (admin_aptika)

```
admin_aptika membuat project baru
    ↓
Tentukan anggota tim (assign ketua_tim + anggota internal)
  → Anggota tim adalah pengguna internal yang datanya dikelola oleh superadmin
    ↓
Status: PROJECT AKTIF
    ↓
ketua_tim mencatat progres pengembangan
```

### 5.3 Alur Pengujian & Deployment

```
Pengembangan selesai (dicatat ketua_tim)
    ↓
admin_aptika → Uji Fungsional
    ├── Tidak lulus → dikembalikan ke tim
    └── Lulus ↓
admin_persandian → Uji Keamanan     🔲 Prosedur TBD
    ├── Tidak lulus → dikembalikan ke tim
    └── Lulus ↓
admin_infrastruktur → Input URL Hosting + Deskripsi
    ↓
Status: APLIKASI AKTIF / LIVE
```

### 5.4 Fitur Kolaborasi & Rapat Proyek (Tim & OPD)

```
Project Aktif
    ↓
Penyelenggara (Aptika / Ketua Tim) menjadwalkan Rapat
    ├── Mengisi: Agenda, Waktu, Lokasi/Link, Surat Undangan, Deskripsi
    └── Memilih peserta: Anggota Tim internal & Perwakilan Instansi OPD
    ↓
Sistem mengirimkan Notifikasi ke peserta (In-App / Email / WhatsApp)
    ↓
Rapat Berlangsung & Selesai
    ↓
Upload Notulensi Hasil Rapat & Berkas Dokumentasi/Foto Kegiatan
```

---

## 6. Fitur per Role

### 6.1 OPD
- [ ] Daftar & login akun OPD
- [ ] Lihat daftar permohonan milik OPD sendiri
- [ ] Ajukan permohonan baru (jenis: new_app / app_development / app_deactivation)
- [ ] Upload dokumen proposal (PDF)
- [ ] Upload dokumen teknis (PDF)
- [ ] Lihat status permohonan secara real-time
- [ ] Lihat alasan penolakan (jika ditolak)

### 6.2 admin_aptika
- [ ] Lihat semua permohonan masuk dari seluruh OPD
- [ ] Tinjau detail permohonan + download dokumen
- [ ] Approve / Tolak permohonan (dengan catatan)
- [ ] Buat project baru dari permohonan yang disetujui
- [ ] Assign ketua_tim dan anggota tim ke project (dari daftar user internal)
- [ ] Lakukan dan catat hasil uji fungsional
- [ ] Lihat progres project (dari catatan ketua_tim)

### 6.3 admin_infrastruktur
- [ ] Lihat project yang sudah lulus uji fungsional & keamanan
- [ ] Input URL aplikasi yang sudah di-hosting
- [ ] Input deskripsi environment hosting (server, spesifikasi, dll)
- [ ] Update informasi hosting jika ada perubahan

### 6.4 admin_persandian
- [ ] Lihat project yang siap diuji keamanan
- [ ] 🔲 Input hasil uji keamanan (prosedur TBD)
- [ ] 🔲 Upload laporan uji keamanan (TBD)

### 6.5 ketua_tim
- [ ] Lihat project yang di-assign kepadanya
- [ ] Catat progres pengembangan (log aktivitas / milestone)
- [ ] 🔲 Fitur tambahan lainnya (TBD)

### 6.6 kepala_bidang
- [ ] 🔲 Seluruh fitur TBD
- Kemungkinan: dashboard monitoring semua project, approval tertentu

### 6.7 kepala_dinas
- [ ] 🔲 Seluruh fitur TBD
- Kemungkinan: dashboard eksekutif, laporan rekapitulasi

### 6.8 superadmin
- [ ] Manajemen user internal (CRUD: superadmin, admin_aptika, admin_infrastruktur, admin_persandian, ketua_tim, kepala_bidang, kepala_dinas)
- [ ] Manajemen akun OPD (CRUD instansi OPD)
- [ ] Assign role ke user
- [ ] Konfigurasi sistem
- [ ] Akses ke semua fitur

---

## 7. Manajemen User & Tim

- **User internal** (superadmin, admin_aptika, admin_infrastruktur, admin_persandian, ketua_tim, kepala_bidang, kepala_dinas) dikelola sepenuhnya oleh `superadmin` melalui panel manajemen user.
- **Akun OPD** juga dikelola oleh `superadmin` (tidak self-register).
- Saat admin_aptika menyusun tim project, ia memilih dari daftar user internal yang sudah ada di sistem.

---

## 8. Status Permohonan & Project

### Status Permohonan
| Status | Keterangan |
|---|---|
| `draft` | OPD belum submit |
| `submitted` | Sudah diajukan, menunggu tinjauan |
| `under_review` | Sedang ditinjau admin_aptika |
| `approved` | Disetujui, menunggu inisiasi project |
| `rejected` | Ditolak |

### Status Project
| Status | Keterangan |
|---|---|
| `initiated` | Project baru dibuat, tim ditentukan |
| `in_development` | Sedang dikembangkan |
| `functional_testing` | Sedang uji fungsional (admin_aptika) |
| `security_testing` | Sedang uji keamanan (admin_persandian) |
| `deployment` | Sedang proses hosting (admin_infrastruktur) |
| `live` | Aplikasi sudah aktif |
| `inactive` | Aplikasi dinonaktifkan |

---

## 9. Entitas Data Utama (Gambaran Awal)

> Database detail akan dibahas terpisah. Ini hanya gambaran umum entitas.

| Entitas | Keterangan |
|---|---|
| `users` | Semua pengguna sistem (internal + OPD) |
| `opd_instances` | Data instansi OPD |
| `applications` | Data aplikasi yang ada / pernah ada |
| `requests` | Permohonan dari OPD |
| `request_documents` | Dokumen terlampir (proposal, teknis) |
| `projects` | Project yang diinisiasi dari permohonan |
| `project_members` | Anggota tim per project (user internal) |
| `project_progress_logs` | Catatan progres oleh ketua_tim |
| `dokumen_perancangan` | Berkas teknis rekayasa software |
| `tahapan_project` | Milestone tahapan proyek & persentase |
| `progres_project` | Log aktivitas berkala & notulensi progres |
| `testing_aplikasi` | Hasil uji fungsional |
| `pengujian_keamanan` | Hasil uji keamanan (VAPT) |
| `hosting_aplikasi` | Info domain & spesifikasi hosting |
| `penundaan` | Riwayat hold/penundaan proyek |
| `rapat_project` | Agenda rapat proyek, jadwal, surat, dokumentasi, notulensi |
| `peserta_rapat` | Daftar personil internal & OPD yang diundang rapat |
| `notifications` | Sistem notifikasi in-app / log pengiriman notifikasi |

---

## 10. Hal-hal yang Masih TBD

| # | Item | Keterangan |
|---|---|---|
| 1 | Prosedur uji keamanan (admin_persandian) | Akan dikonfirmasi kemudian |
| 2 | Fitur kepala_bidang | Akan dikonfirmasi kemudian |
| 3 | Fitur kepala_dinas | Akan dikonfirmasi kemudian |
| 4 | Fitur tambahan ketua_tim | Kemungkinan bertambah |
| 5 | Sistem notifikasi | Email / in-app? |
| 6 | Approval kepala_bidang / kepala_dinas | Apakah ada dalam alur? |
| 7 | Format dokumen | Hanya PDF? Ada batasan ukuran? |
