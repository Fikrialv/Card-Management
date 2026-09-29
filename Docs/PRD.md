# PRD — RFID Card Management & Customer Request System

## Revisi UI dan workflow terbaru — 2026-09-23

- Pengadaan menerima kartu secara parsial; penerimaan hanya mencatat tanggal dan qty aktual. RFID awal/akhir bukan input pengadaan.
- Inventory dihitung dari ledger qty aktual Stock In/Stock Out. Stock In manual di UI dihapus; penerimaan berasal dari Catatan Pengadaan.
- Semua tipe kartu wajib memiliki `requested_quantity`. Bukti bayar opsional dan quota 30 dihapus dari alur bisnis aktif.
- Filter Inventory mendukung semua, hari ini, kemarin, minggu, bulan, dan custom. Upload memakai validasi signature, storage privat, cleanup terjadwal, dan histori bisnis/audit tidak dihapus.
- Notifikasi tersedia dari header dan tetap disaring sesuai role. Aktivitas audit memiliki pencarian/kategori fitur.
- Menu Laporan dihapus dari sidebar; Report tetap berada di Monitoring dengan periode harian, mingguan, bulanan, tahunan, custom, serta PDF/Excel.
- Role aktif: Admin, Viewer read-only dengan report/export, dan Customer melalui portal publik. Bahasa utama Indonesia; toggle EN dipertahankan.
- Sidebar internal collapsible/icon-only dengan tooltip dan drawer mobile.

## 1. Ringkasan Produk

Sistem memiliki dua antarmuka yang dideploy pada domain terpisah dan memakai satu backend Laravel serta satu database bersama:

1. **Internal Dashboard** — Laravel + Inertia.js + React + TypeScript untuk operasi RFID, pengajuan customer, approval manual, Catatan Pengadaan, inventory, laporan, notifikasi, dan audit.
2. **Customer Request Portal** — React + TypeScript terpisah untuk mengunduh template, mengirim pengajuan, mengunggah bukti, menerima notifikasi, dan tracking melalui Laravel REST API.

Customer Portal tidak boleh mengakses route, session, komponen privat, atau data Internal Dashboard.

## 2. Tujuan

- Mengurangi proses manual Excel/email sambil menjadikan template pengajuan sebagai kontrak input yang terkendali.
- Menjaga stok, nomor, dan histori kartu RFID akurat.
- Memprioritaskan review secara server-side tanpa auto-approval.
- Menghubungkan pengajuan, Catatan Pengadaan, Stock In/Out, laporan, notifikasi, dan audit tanpa input ulang.
- Menyediakan laporan detail pengajuan dan ringkasan arus stok yang dapat diekspor ke Excel dan PDF.

## 3. User & Role

### Internal

- **Admin**: akses penuh; laporan view/export; evidence view/download.
- **Reviewer/PIC**: review, approve/reject, proses pengajuan; evidence view/download.
- **Viewer**: read-only dan hanya metadata evidence; tidak dapat download atau export laporan.

### External

- **Customer**: mengirim pengajuan, mengakses tracking, dan menerima notifikasi tanpa akses dashboard internal.

## 4. Produk & Modul

### A. Internal Dashboard

- Monitoring operasional
- Pengajuan customer dan Priority-Based Approval Queue
- RFID Inventory dan Stock Movement
- Catatan Pengadaan RFID
- Laporan detail pengajuan dan summary stok
- Notifikasi
- Activity / Audit Log
- Settings

Monitoring yang saling terkait digabung pada satu halaman. Pengajuan, Inventory, Catatan Pengadaan, Laporan, dan Audit tetap halaman fitur sendiri.

### B. Customer Request Portal

- Download template pengajuan kartu
- Buat Pengajuan
- Upload Pengajuan Kartu dan Bukti Bayar
- Submit langsung ke Laravel API
- Tracking dan notifikasi customer

## 5. Customer Request

### Data customer dan request

- Nama Institusi
- Nomor CRM: maksimum 13 karakter alfanumerik (`A–Z`, `a–z`, `0–9`), tanpa spasi atau simbol
- Jenis Pengajuan
- **Tanggal Permintaan** — menggantikan label dan input “Tanggal dibutuhkan”

`Tanggal Permintaan` adalah tanggal domain resmi. Tidak ada SLA otomatis atau deadline berdasarkan request type pada MVP. `created_at` hanya timestamp teknis/audit.

### Jenis Pengajuan (enum final)

- `new_card` — Kartu baru
- `damaged_card` — Kartu rusak
- `system_error_card` — Kartu error system
- `lost_card` — Kartu hilang

Aktivasi kartu, perubahan data, dan mutasi saldo dikeluarkan dari scope pengajuan saat ini.

### Template dan bukti

- Customer dapat mengunduh template pengajuan kartu yang bersumber dari `Data/Template RFID 2025.xlsx`; source file tetap read-only.
- Customer mengunggah dua artefak terpisah: **Pengajuan Kartu** dan **Bukti Bayar**.
- Form portal tidak lagi membuat atau mengedit kendaraan/item RFID inline. NOPOL tetap berada di artefak `.xlsx` dan tidak diparse pada MVP.
- Upload, private storage, akses download, validasi tipe/signature/ukuran, retensi, dan malware handling wajib dikendalikan server-side.

### Aturan yang harus dikonfirmasi sebelum kontrak API/schema final

- Keempat field customer yang ditandai di atas: dipertahankan, dijadikan optional, atau dihapus.
- Format/ukuran maksimum dan mandatory rule untuk masing-masing artefak upload; Bukti Bayar wajib pada setiap pengajuan.
- Kebijakan ekstraksi/validasi isi template yang diunggah; MVP tidak mengasumsikan parsing otomatis.

## 6. Status Workflow

`NEW → UNDER_REVIEW → APPROVED → PROCESSING → COMPLETED`

Cabang: `UNDER_REVIEW → REJECTED`.

`REJECTED` final pada MVP. Customer membuat request baru; revision/resubmit ditunda.

## 7. Work Queue and Target

Ranking dihitung server-side dan hanya menentukan urutan review; **tidak pernah meng-approve otomatis**.

Urutan deterministik:

Target pengerjaan otomatis adalah H+1 dari `request_date`. Request kemarin yang belum selesai diberi label **Harus selesai hari ini**. Request yang melewati target diberi label/peringatan **Terlambat**.

Kontrol operasional hanya satu: **Utamakan**. Jika dicentang, request selalu berada paling atas queue.

Urutan deterministik:

1. `Utamakan` dicentang
2. Terlambat / harus selesai hari ini
3. `request_date` paling lama
4. `created_at` paling lama

Priority hanya mengurutkan pekerjaan dan tidak pernah melakukan auto-approval.

Admin/Reviewer berwenang dapat mengubah **Utamakan** dengan alasan dan audit. Kontrol ini hanya mengatur urutan pengerjaan.

## 8. RFID Inventory

Inventory melacak nomor/range RFID dan ledger Stock In/Stock Out, bukan hanya total stok.

### Stock In

- RFID Start dan RFID End
- Total otomatis = End - Start + 1
- Sumber, tanggal penerimaan, PIC, dan referensi Catatan Pengadaan bila berlaku

### Stock Out

Stock Out dibuat dari request approved dan assignment RFID. Simpan nomor/range RFID, customer, CRM, request number, tanggal, dan assigned by.

### Formula dan integritas

`Stock Akhir = Stock Sebelumnya + Stock In - Stock Out`

Flow: `Approved Request → Assign RFID → Stock Out → Update Inventory → Processing → Completed`.

Assignment, Stock Out, update inventory, dan status terkait harus berjalan dalam transaksi InnoDB dengan row locking. Sistem menolak range overlap, RFID ganda, stok negatif, dan assignment kartu yang sudah terpakai.

## 9. Catatan Pengadaan RFID

Catatan Pengadaan menggantikan Stock Request. Ini adalah fitur internal yang dibuat dan diedit Admin, bukan workflow customer atau pengiriman.

Data minimum:

- Tanggal pengajuan
- PIC / nama PIC
- QTY RFID yang diajukan
- Catatan admin
- Tanggal diterima (diisi/diubah saat penerimaan)
- QTY RFID yang diterima (diisi/diubah saat penerimaan)

Saat penerimaan disimpan, sistem wajib membuat Stock In dan memperbarui inventory dalam satu transaksi atomik, idempotent, terotorisasi, serta beraudit. Receipt retry tidak boleh menambah stok dua kali.

**Integrity gate:** QTY penerimaan saja tidak cukup untuk membuat kartu RFID unik. Sebelum implementasi penerimaan, pemilik produk harus menentukan apakah admin memasukkan range/daftar RFID saat receipt atau apakah tersedia sumber nomor RFID terotorisasi lain. Sistem tidak boleh mengubah QTY menjadi kartu assignable tanpa identitas RFID unik.

`Data/template pengajuan.jpeg` hanya referensi visual untuk pengajuan/pengadaan; tidak boleh disalin sebagai data produksi tanpa mapping dan persetujuan field.

## 10. Laporan

### Laporan Detail Pengajuan

Menampilkan pengajuan customer secara detail, termasuk minimal request number, tanggal permintaan, institusi/CRM sesuai hak akses, jenis, status (termasuk belum disetujui dan disetujui), approval history aman, dan tautan artefak yang diizinkan.

### Laporan Summary

Ditentukan dari data detail dan stock ledger dengan minimal jumlah pengajuan menurut status serta total RFID **masuk (Stock In)** dan **keluar (Stock Out)** pada periode/filter yang sama.

Kedua laporan mendukung filter periode dan export **Excel** serta **PDF**. Reports hanya dapat dilihat/export Admin. Excel maksimal 5.000 row, PDF maksimal 1.000 row, retention export 7 hari, private, dan mengikuti filter aktif.

## 11. Tracking & Customer Notifications

Customer memakai **Request Number + Tracking Code** untuk melihat status, timeline aman, alasan rejection yang customer-safe, dan notifikasi miliknya. REJECTED bersifat final pada MVP; customer membuat request baru. Respons tidak boleh mengungkap request lain, catatan privat, atau credential sensitif.

Customer menerima in-app/tracking notifications untuk submit dan setiap perubahan status, termasuk `PROCESSING` dan `REJECTED`. Aksesnya selalu memerlukan pasangan tracking credential yang valid. Internal notification untuk request yang harus selesai hari ini atau terlambat dikirim ke Admin/Reviewer sesuai authorization; actor approve/reject tidak diberi notifikasi atas aksinya sendiri.

## 12. Dashboard KPI

Satu halaman monitoring operasional menggabungkan Current RFID Stock, Stock In/Out, Low Stock, Pending Approval, request yang perlu ditindaklanjuti, Overdue, Due Today, Approved Today, Completed, antrian pengerjaan, alert, recent activity, dan ringkasan notifikasi.

Setiap metrik memiliki definisi periode, freshness/error state, RBAC, serta drill-down ke halaman fitur utama.

## 13. Search & Filter

- Search: request number, customer, CRM
- Status, Utamakan, target state, request type, dan date range berdasarkan `request_date`
- Default Approval Queue: Utamakan, lalu target, request date, dan created_at

## 14. Notifications

Minimal tersedia in-app inbox/get notification yang terotorisasi untuk Admin/Reviewer/Viewer sesuai Policy dan customer melalui tracking credential. Web Push deferred dan bukan blocker MVP.

Web Push adalah channel tambahan yang permission-based; perlu HTTPS, VAPID/subscription, unsubscribe, dan capability hosting yang diverifikasi. In-app notification tetap wajib sebagai fallback.

## 15. Audit Log

Catat login penting, request created/updated/status, perubahan Utamakan, approve/reject, RFID assignment, Stock In/Out, penerimaan Catatan Pengadaan, export laporan, perubahan settings, dan notifikasi penting. Audit log tidak dapat diedit/dihapus user biasa; tidak menyimpan secret atau tracking code mentah.

## 16. Bahasa dan Lokalisasi

- Bahasa Indonesia default; toggle ID/EN di kedua antarmuka.
- Cakupan: UI, form, validation, error, status, notification, tracking, empty state, export/PDF bila diperlukan.
- User-facing strings memakai translation keys; tidak di-hardcode.

## 17. Responsive dan Accessibility

- Mobile-first untuk HP, tablet, laptop, dan desktop pada Customer Portal serta Internal Dashboard.
- Table/data-dense view menggunakan card/stacked rows, kolom prioritas, summary-detail, atau progressive disclosure; bukan horizontal overflow saja.
- Aksi kritis tidak bergantung hover; touch target, keyboard, focus, zoom, portrait/landscape, reduced motion, dan WCAG 2.2 AA diuji.

## 18. Arah UI/UX

- Internal: dashboard operasional yang tenang dan padat informasi, sidebar yang jelas, kartu metrik, queue/table adaptif, dan warna status semantik. Referensi percakapan Image #7–#12 dipakai sebagai arah, bukan untuk disalin.
- Customer: alur form, detail, tracking, dan upload yang bersih, fokus, dan bertahap. Referensi Image #13–#15 dipakai sebagai arah, bukan untuk disalin.
- Detail komponen, breakpoints, treatment table, empty/loading/error state, dan kriteria visual tersimpan di `Docs/uiux-direction.md`.

## 19. Arsitektur dan Deployment Target

### Core stack

- Backend/shared domain: Laravel, PHP, Eloquent, Form Requests, API Resources, Policies/Gates, Jobs/Queues bila hosting mendukung.
- Internal Dashboard: Laravel + Inertia.js + React + TypeScript.
- Customer Portal: React + TypeScript terpisah via Laravel REST API.
- UI: Tailwind CSS + shadcn/ui.
- Database: MySQL 8 via Biznet/cPanel + phpMyAdmin; MariaDB-compatible fallback tanpa perubahan domain architecture.
- Storage: private cPanel-compatible storage untuk upload dan export.

PostgreSQL, Prisma, Next.js, dan NOPOL document generator dikeluarkan dari core MVP.

### Boundary dan deployment

- Hosting utama Biznet/cPanel.
- Internal Dashboard dan Customer Portal memiliki domain/subdomain, build, environment, serta deployment berbeda.
- Keduanya memakai satu Laravel backend dan satu database MySQL/MariaDB-compatible.
- Route internal memakai session/RBAC; Customer Portal hanya allowlisted REST endpoints dengan CORS, rate limit, API Resource projection, dan authorization yang sesuai.

## 20. Sumber Data dan Mapping

File berikut read-only dan tidak boleh diubah:

- `Data/Template RFID 2025.xlsx`
- `Data/UPDATE KARTU RFID.xlsx`
- `Data/template pengajuan.jpeg`

Sebelum schema/import final, mapping wajib mencatat source, canonical field, tipe, requiredness, normalisasi, domain owner, PII/sensitivitas, ambiguity, dan import/reference-only scope. Nilai contoh customer/CRM tidak boleh disalin ke docs, fixture, atau log.

`Template RFID 2025.xlsx` menjadi referensi kandidat template download; publikasi/download production memerlukan version/hash, kebijakan akses, dan validasi artefak tanpa mengubah source.

## 21. Glossary & Entity Minimum

- **Customer Request**: satu pengajuan eksternal beserta artefak Pengajuan Kartu dan Bukti Bayar; tidak membuat RequestItem kendaraan inline pada scope ini.
- **RequestItem**: entitas historis/future parser; bukan input Customer Portal MVP.
- **Catatan Pengadaan**: catatan internal Admin untuk jumlah RFID diajukan/diterima; penerimaan yang sah menjadi sumber Stock In.
- **RFID Range/Card** dan **Stock Movement**: identitas/range RFID serta ledger masuk/keluar.
- **Notification**: pesan terotorisasi dengan read/delivery state, channel, dan referensi entitas.
- **AuditLog**: catatan immutable atas mutasi bisnis dan aksi sensitif.

Entity minimum: User/Role, Customer, CustomerRequest, RequestEvidence, RequestStatusHistory, RFIDCard/RFIDRange, StockMovement, CatatanPengadaan, RFIDAssignment, Notification, AuditLog, dan ReportExport bila export disimpan/asinkron. RequestItem hanya historical/future parser.

## 22. Non-Functional Requirements

- Validation dan authorization server-side untuk setiap write path.
- Secure upload/private access, rate limit Customer Portal, serta no public file URL permanen.
- Backup/restore database dan file, log tanpa PII/secret berlebih, TLS, dan compatibility Biznet/cPanel.
- Sekitar 20 user simultan tidak menyebabkan data race pada approval, receipt, atau assignment.

## 23. Success Criteria

- Pengajuan customer beserta dua artefaknya masuk Internal Dashboard tanpa input ulang.
- Customer Portal tidak dapat mengakses data/route admin.
- Approval queue deterministic dan tetap manual.
- Receipt Catatan Pengadaan yang valid memperbarui Stock In/inventory tepat sekali tanpa RFID duplikat atau stok negatif.
- Laporan detail dan summary direkonsiliasi dengan source records, lalu dapat diekspor ke Excel/PDF secara aman.
- Notifikasi internal/customer aman dan terotorisasi.
- UI kritis usable dalam ID/EN di HP, tablet, laptop, desktop, serta memenuhi gate WCAG 2.2 AA.

## 24. Final workflow Kartu Baru

- Semua user testing staging memakai satu origin aplikasi Laravel yang merutekan flow publik di `/pengajuan` dan `/tracking`, serta internal di `/login`, `/admin`, dan `/dashboard`.
- Setiap Customer Request memiliki `requested_quantity` wajib untuk semua tipe kartu; tidak ada quota 30, total/used/remaining, atau alokasi free/paid.
- Template RFID wajib diunggah pada setiap pengajuan; Bukti Bayar opsional.
- Approval tidak mengunci atau mengonsumsi quota.
- CRM adalah identifier utama. Nama PT disimpan dalam bentuk normalisasi untuk duplicate warning lintas CRM; sistem tidak melakukan auto-merge dan keputusan tetap pada Admin.
- Internal Dashboard menampilkan status dan quantity request; tidak ada tampilan quota.

## Authoritative implementation baseline — supersedes legacy requirements

- Runtime final adalah satu aplikasi Laravel + Inertia + React; flow publik berada di `/pengajuan` dan `/tracking`, internal di `/admin`. Standalone Customer Portal/CORS terpisah bukan runtime aktif.
- Role internal hanya Admin dan Viewer. Admin full operation; Viewer read-only + report/export. Reviewer/PIC tidak aktif.
- Semua request wajib memiliki `requested_quantity`; payment proof opsional; quota 30 dan enforcement/alokasi quota tidak aktif.
- Procurement mendukung receipt parsial berbasis qty aktual tanpa input RFID awal/akhir. `300 → 250 → 50` menghasilkan stock tepat 300; zero/negative/over-receipt ditolak. Stock In manual di UI dihapus.
- Inventory mendukung all/today/yesterday/week/month/custom. Report berada dari Monitoring dengan daily/weekly/monthly/yearly/custom + PDF/Excel.
- Notifikasi adalah popup header role-aware; Activity memiliki filter kategori; bahasa Indonesia default dengan toggle EN; sidebar collapse/icon-only, tooltip, dan mobile drawer.
> **Public form recheck â€” 2026-09-24:** Informasi Pengajuan uses stacked labels and inputs; Dokumen Pendukung presents a full-width green download action before uploads. The visible payment label is `Upload Bukti Bayar`; backend validation remains optional. `/admin` remains auth-protected: guests go to `/login`, while an already authenticated Admin may enter the dashboard directly.
> **Authoritative final baseline — 2026-09-24:** Runtime is one Laravel + Inertia + React app. Public `/` redirects to `/pengajuan` with **Kirim Pengajuan** / **Lacak Pengajuan** tabs; internal `/admin` is Admin-only and `/viewer` is Viewer-only. Customer does not authenticate. Notification history is a header popup with preferences under Pengaturan; there is no notification sidebar/page. Procurement receipt is partial actual quantity without RFID start/end; Inventory has no manual Stock In; quantity is mandatory and payment proof optional; Reports are under Monitoring with daily/weekly/monthly/yearly/custom filters and PDF/Excel export.
> **UI closure — 2026-09-24:** Public submit flow uses two visual cards inside one logical form: Informasi Pengajuan and Dokumen Pendukung. Download is next to document uploads. Tracking remains a separate tab. Settings uses Indonesian labels by default; EN toggle remains available.
> **Navbar closure — 2026-09-24:** Internal navigation uses a non-resizable collapsible sidebar: 256px expanded and 64px icon rail collapsed, with tooltip/focus labels, active route state, mobile drawer, shared Admin/Viewer menu, header bell/language/security controls, and compact user/logout footer.
> **Demo/deploy baseline — 2026-09-29:** Guest `/` is the ready public website entry point with separate Kirim Pengajuan and Lacak Pengajuan tabs, responsive two-card form, immutable template download, required application upload, optional payment proof, and no horizontal overflow across mobile/tablet/desktop QA. `/admin` and `/viewer` remain protected by login and role authorization; no remember-me session is offered.
