# plan.md — RFID Card Management System

## Revisi implementasi 2026-09-23

1. Catatan Pengadaan menjadi sumber tunggal penerimaan inventory dan mendukung partial receipt berbasis qty aktual.
2. Input RFID awal/akhir dan Stock In manual dihapus dari UI; inventory tetap menyimpan ledger/audit dan assignment RFID historis.
3. `requested_quantity` berlaku untuk semua request type; payment proof optional; quota 30 tidak lagi menjadi aturan approval.
4. Monitoring menjadi entry point Report; sidebar tidak menampilkan menu Laporan. Inventory memakai filter periode operasional.
5. Header memuat akses notifikasi, layout mendukung collapse/icon-only serta mobile drawer.
6. Public root `/` menjadi website customer siap demo: dua-card submission form, tracking tab terpisah, download template, upload application wajib, dan payment proof opsional.
7. Internal entrypoints tetap auth-protected; Admin/Viewer role gate dipertahankan dan session tidak memakai remember-me.

## 0. Skill & execution baseline

- Gunakan skill yang sesuai sebelum tindakan: `brainstorming` untuk perubahan desain/fitur, `domain-modeling` untuk schema/workflow, `tdd` untuk perilaku baru, `diagnosing-bugs` untuk error, dan skill UI/accessibility untuk antarmuka.
- Semua command shell diprefix `rtk`. Gunakan implementasi minimal dan konvensional; jangan membuat abstraksi yang belum diminta.
- ECC/Ponytail hanya boleh disebut aktif jika plugin aktif terverifikasi pada `CODEX_HOME` yang dipakai.
- Sumber kebenaran: `PRD.md → plan.md → todo.md → implementation → test → update.md`.

## 1. Architecture & boundaries

- `backend/`: Laravel, Internal Dashboard Inertia/React/TypeScript, REST API, shared domain, database owner.
- `customer-portal/`: React/TypeScript standalone, deployed terpisah, hanya memakai allowlisted REST API.
- MySQL 8/InnoDB pada Biznet/cPanel; gunakan subset MariaDB-compatible bila perlu. Laravel migration tetap authoritative; phpMyAdmin hanya operasi/inspeksi.
- Internal dan public domain/deployment/environment terpisah tetapi berbagi backend dan database yang sama.
- Storage upload/export privat. Customer tidak menerima route internal, session internal, maupun permanent private-file URL.

## 2. Phase 0 — Re-baseline requirements and contracts

1. Rekonsiliasi perubahan scope terbaru dengan repository dan audit trail.
2. Lengkapi ADR/hosting audit untuk Biznet: PHP/database, subdomain/TLS, Composer/build upload, cron/queue, storage, backup/restore, dan web-push prerequisites.
3. Inspeksi/mapping read-only tiga file `Data/` sebelum schema final, parser/import, atau template download production.
4. Tetapkan public API contract, identifier, translation keys, policy boundaries, upload policy, and public tracking projection.
5. Terapkan keputusan final: field customer MVP, target H+1, kontrol Utamakan, upload policy, identity RFID saat receipt, report RBAC/limits, dan push channel policy.

**Exit:** requirements versioned; mapping, capability matrix, contracts, and genuine blockers explicit. Tidak ada source `Data/` berubah.

## 3. Phase 1 — Foundation, domain, and integrity

1. Verifikasi scaffold Laravel/Internal dan standalone Customer Portal, quality gate, environment, auth/RBAC, MySQL/MariaDB convention.
2. Model Customer Request dengan request date, target H+1, kontrol Utamakan, enum empat request type, request evidence, status history, and safe tracking credentials.
3. Pertahankan `RequestItem` sebagai historical/future parser entity; jangan gunakan sebagai input portal pada scope ini.
4. Model RFID range/card/movement/assignment dengan constraints/locks.
5. Model Catatan Pengadaan, receipt, notification, audit, and export job metadata.
6. Implement state machine, target H+1, kontrol Utamakan, dan approval manual. Target hanya mengatur urutan/peringatan; queue memakai Utamakan, status target, `request_date`, lalu `created_at`.

**Exit:** migration, constraints, Form Requests, Policies, domain tests, and privacy projections prove invariants on MySQL/MariaDB.

## 4. Phase 2 — Customer Portal

1. Bangun shell ID-first/EN toggle dan responsive mobile-first.
2. Implement form Customer Request tanpa section kendaraan/item inline: field customer yang sudah disetujui, CRM alfanumerik maksimal 13, request type final, dan Tanggal Permintaan.
3. Sediakan download artefak template RFID versioned dari source reference yang disetujui.
4. Implement upload terpisah Pengajuan Kartu dan Bukti Bayar dengan private storage/validation.
5. Submit atomik, response berisi tracking credentials aman, tracking, in-app notification customer, dan optional web push opt-in setelah capability ready.

**Exit:** customer dapat download/submit/track secara aman pada phone–desktop; bundle/API tidak memuat internal field/routes.

## 5. Phase 3 — Internal request operations

1. Internal shell dengan Monitoring, Pengajuan, Inventory, Catatan Pengadaan, Reports, Activity, Settings, and Notifications; hapus navigasi/UI Dokumen NOPOL dari scope aktif.
2. Queue/detail/review untuk artefak customer; preview/download private melalui authorization.
3. Server-side ranking, manual approve/reject, kontrol Utamakan dengan audit, dan status notification.
4. In-app inbox untuk internal; web push opt-in jika HTTPS/VAPID/subscription support verified.

**Exit:** internal review remains manual, auditable, localized, responsive, and isolated from portal boundary.

## 6. Phase 4 — RFID Inventory and assignment

1. Build inventory/range search, Stock In, Stock Out, history, low stock, and secure assignment.
2. Use `DB::transaction`, deterministic locking, database unique constraints, guarded transitions, idempotency, and bounded retry.
3. Reconcile approved request to assignment/Stock Out/Processing/Completed.

**Exit:** concurrency tests show no double assignment, overlap, lost update, negative stock, or partial movement.

## 7. Phase 5 — Catatan Pengadaan to Stock In

1. Replace legacy Stock Request workflow with Admin-only Catatan Pengadaan: tanggal pengajuan, PIC, QTY diajukan, catatan, tanggal diterima, QTY diterima.
2. Allow authorized Admin edit with history/audit; distinguish requested vs received values.
3. At receipt, validate RFID identity source/range and atomically create one Stock In plus inventory update.
4. Prevent duplicate receipt with version/idempotency guard; retry/rollback must retain consistent state.

**Exit:** Catatan Pengadaan → receipt → Stock In is reconciled, audited, notification-aware, and race-safe.

## 8. Phase 6 — Reports, monitoring, notifications, audit

1. Monitoring combines related operational KPI with drill-down; it is not a substitute for feature pages.
2. Implement Customer Request Detail Report (pending and approved included) and Summary Report derived from detail + Stock In/Out ledger.
3. Implement filtered Excel/PDF export with authorization, private data projection, formula-injection protection, localization, and limits.
4. Cover audit for request, priority, inventory, procurement receipt, exports, settings, and notification actions.

**Exit:** metric/report totals reconcile; export output matches authorized filter result; audit chain is inspectable.

## 9. Phase 7 — Responsive, accessibility, and production readiness

- Apply `Docs/uiux-direction.md`: customer flow is focused/stepwise; internal operations are calm/high-density with adaptive data views.
- Test phone, tablet, laptop, desktop; touch, keyboard, zoom, orientation, and WCAG 2.2 AA. Tables need intentional small-screen treatments beyond horizontal scroll.
- Complete security review: Policies, API projection, CORS, CSRF/session, rate limit, secure uploads, notification privacy, CORS/push service worker, headers, secrets, backup/restore, migration/rollback.
- Verify Biznet/cPanel production capability and separate release artifacts.

## 10. Verification journeys

1. Customer download template → submit Pengajuan Kartu + Bukti Bayar → tracking/in-app notification → Internal review → approval/rejection → safe customer notification.
2. Approved request → RFID assignment → Stock Out → processing/completed, with concurrency/race cases.
3. Admin Catatan Pengadaan → receipt containing approved RFID identity source → atomic Stock In → updated inventory and notification.
4. Detail Report + Summary Report → identical filter reconciliation → authorized Excel/PDF export.
5. Customer Portal cannot access internal page/data; mismatched tracking credentials disclose nothing.
6. Every critical journey runs ID/EN across representative device/accessibility matrix.

## 11. Explicitly deferred scope

- NOPOL document/template/PDF history is removed from the active MVP. Existing shell/data must not be represented as production capability and should be removed when the corresponding implementation task executes.
- Vehicle/item inline form, activation card, data change, and balance mutation are removed from current customer-request scope.
- No automatic extraction of uploaded template data until an approved mapping/parser policy exists.

## 12. Definition of Done

A task is DONE only after PRD acceptance, mapped source usage, server authorization/validation, test evidence, audit events, ID/EN, responsive/accessibility checks, no regression, Biznet/MySQL compatibility, and synchronized `todo.md`/`update.md` evidence.

## 13. Final Kartu Baru workflow

1. Tambahkan `requested_quantity`, customer identity normalization, `customer_quotas`, dan `customer_quota_usages` melalui migration/factory/model.
2. Validasi submit server-side: setiap request memiliki `requested_quantity`; Template RFID dan Bukti Bayar selalu wajib.
3. Recheck saat approval menggunakan row lock dan transaction; catat free/paid allocation sebagai history.
4. Expose hanya `payment_required` dan duplicate warning ke portal; expose total/used/remaining/history di Internal Dashboard.
5. Uji satu origin ngrok: customer submit melalui public API, lalu request tampil di dashboard Laravel melalui proxy path.

**Exit:** quota invariants, duplicate warning tanpa auto-merge, approval race guard, dashboard history, portal contract, and ngrok smoke are verified.

## Final execution status — 2026-09-23

- Runtime: satu aplikasi Laravel + Inertia + React; publik `/pengajuan`/`/tracking`, internal `/admin` ber-auth.
- RBAC: Admin write/full operation; Viewer read-only + report/export; guest admin ditolak.
- Procurement: receipt qty aktual parsial, tanpa RFID awal/akhir; Stock In hanya dibuat dari receipt; zero/negative/over-receipt ditolak.
- Inventory/report/UI: custom filters, report period lengkap + PDF/Excel, notification header popup, Activity kategori, ID default + EN toggle, responsive sidebar/drawer.
- Upload: application wajib, payment proof opsional, private/signature/type/size validation, scheduled cleanup tanpa menghapus histori bisnis/audit.
- Evidence backend terakhir: `php artisan test` 65 passed, 342 assertions. Final frontend rerun dicatat di `Docs/update.md`.
- Final verification — 2026-09-24: `rtk php artisan test` **66 passed / 333 assertions**; UI test 1/1, typecheck, lint, and build passed. Browser QA through ngrok passed public tabs, internal role entry, popup, mobile drawer, and Lighthouse accessibility 100 desktop/mobile with no console errors.
- Final route/UI closure: `/admin` Admin-only, `/viewer` Viewer-only, `/notifications` page removed, header bell popup retained, and Pengaturan contains notification preferences.
- Public form recheck: Card 01 alignment, Card 02 download/upload order, payment label, and isolated guest `/admin` redirect verified.
- Public entry correction: root renders the customer page directly; legacy `/pengajuan` redirects to root.
- UI recheck 2026-09-24: two-card public form, clean accessible upload controls, separate tracking tab, compact settings layout, and reference-aligned sidebar verified.
- Navbar verification 2026-09-24: expanded/collapsed rail, active route, tooltip/focus, mobile drawer, shared role menu, header actions, and logout focus styling passed browser QA.
- Final readiness recheck 2026-09-29: Pint, PHPStan, Composer validation/audit, Laravel tests (68/349), backend typecheck/lint/UI test/build, customer-portal lint/typecheck/tests (9)/build, immutable source hashes, public mobile/tablet/desktop overflow checks, login accessibility, and ngrok smoke all passed. Public root is the active guest website; `/admin` and `/viewer` remain protected.
- Login internal menggunakan username-only: `adminpertamina` untuk Admin dan `viewerpertamina` untuk Viewer. Email tidak diminta pada layar login.
