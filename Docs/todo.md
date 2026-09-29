# RFID Card Management — Executable Checklist

## Revisi 2026-09-29

- [x] Partial procurement receipt dan qty aktual inventory diimplementasikan; RFID awal/akhir serta Stock In manual UI dihapus.
- [x] Quantity request diwajibkan untuk semua tipe; bukti bayar opsional; quota 30 dikeluarkan dari approval aktif.
- [x] Filter periode inventory dan akses report/export Viewer ditambahkan; menu Laporan sidebar dihapus.
- [x] Popup notifikasi berbasis data unread, preferensi di Pengaturan, dan terjemahan ID/EN kritis sudah diverifikasi pada browser QA.

## Final demo/deploy gate — 2026-09-29

- [x] Public root siap dipakai guest: submission dua card, tracking tab terpisah, download template, upload application wajib, dan payment proof opsional.
- [x] Internal `/admin` dan `/viewer` tetap membutuhkan login serta authorization role; guest tidak dapat masuk langsung ke dashboard.
- [x] Sidebar expanded/collapsed, tooltip, active route, mobile drawer, header actions, logout state, dan responsive layout diverifikasi.
- [x] Quality gate lulus: Composer audit, Pint, PHPStan, Laravel 68 tests/349 assertions, backend frontend checks, customer-portal 9 tests, production builds, dan immutable Data hash.
- [x] Browser QA lulus pada mobile/tablet/desktop tanpa horizontal overflow; public/login accessibility dan best practices Lighthouse 100.
- [ ] Hosting account-specific: kredensial database, PHP extensions, TLS/domain, cron/queue, mail, backup/restore, dan release secret belum dapat diverifikasi dari repository lokal.

Generated from `Docs/PRD.md` and `Docs/plan.md`.

## Rules

- `[x]` is permitted only after acceptance criteria and test evidence are recorded in `Docs/update.md`.
- `[ ]` means `TODO`, `IN_PROGRESS`, or `BLOCKED`; read each status.
- `Data/` is immutable. Mapping precedes final schema, import/parser, and production template download.
- Customer Portal may only call allowlisted Laravel REST endpoints. UI validation never replaces server validation/authorization.
- NOPOL document work, vehicle/item inline request UI, activation, data changes, and balance mutation are outside active MVP.

## Phase 0 — Re-baseline, decisions, and foundation

- [x] **TODO-001 — P0 — Repository and requirement audit**
  - Dependencies: None
  - Description: Audit project, source references, and requirements; remove legacy engine from active production scope.
  - Acceptance: Scope, architecture, source-data boundary, and unresolved decisions are recorded.
  - Test: Documentation/repository review is recorded in `update.md`.
  - Status: `DONE`

- [x] **TODO-001A — P0 — Record current scope revision**
  - Dependencies: TODO-001
  - Description: Rebaseline the product for four request types, template-download/two-upload portal, Catatan Pengadaan, reports, notifications, and UI direction.
  - Acceptance: PRD, plan, checklist, UI direction, and audit log agree; removed scope is explicit.
  - Test: Cross-document terminology/status/link review and immutable-source hash check are recorded in `update.md`.
  - Status: `DONE`

- [ ] **TODO-002 — P0 — Complete Laravel/MySQL/Biznet architecture and hosting decision record**
  - Dependencies: TODO-001A
  - Description: Verify account-level PHP/database, SSH/Composer/build, domains/TLS, cron/queue, storage, backup/restore, and web-push prerequisites; keep ADRs current.
  - Acceptance: Separate deployments/domains, one backend/database, private storage, fallback MariaDB behavior, and operational rollback are evidenced.
  - Test: Hosting capability checklist and deployment-boundary review pass.
  - Status: `IN_PROGRESS` — account-specific Biznet evidence remains required.

- [ ] **TODO-003 — P0 — Verify independent Laravel/Internal and Customer Portal builds**
  - Dependencies: TODO-002
  - Description: Maintain `backend/` and `customer-portal/` as separate build/release artefacts with no private dashboard dependency in portal bundle.
  - Acceptance: Both boot/build independently and portal has only public API configuration.
  - Test: Clean install, build, bundle/dependency, and boundary checks pass.
  - Status: `TODO`

- [ ] **TODO-004 — P0 — Establish environment, secret, CORS, storage, and push boundaries**
  - Dependencies: TODO-002, TODO-003
  - Description: Configure internal/public domains, session/CSRF, strict CORS, private file storage, public API base URL, VAPID/service-worker configuration when available, and secret handling.
  - Acceptance: Startup/config errors are safe; client bundles expose only allowlisted values; customer cannot use internal session/routes.
  - Test: Configuration, CORS, bundle-leakage, secret-scan, and unauthorized route tests pass.
  - Status: `TODO`

- [ ] **TODO-005 — P0 — Establish quality gates**
  - Dependencies: TODO-003
  - Description: Maintain Laravel tests/Pint/PHPStan and frontend TypeScript/ESLint/Vitest/Playwright/accessibility gates.
  - Acceptance: One documented verification entry point covers changed areas and CI blocks failures.
  - Test: Passing and deliberately failing gate runs are evidenced.
  - Status: `TODO`

- [ ] **TODO-006 — P0 — Configure MySQL/MariaDB Eloquent conventions**
  - Dependencies: TODO-002, TODO-003, TODO-004
  - Description: Configure InnoDB, migrations, collation/timezone, locking, health checks, and isolated test database without relying on phpMyAdmin for schema changes.
  - Acceptance: Empty compatible database migrates reliably and supports required transaction/locking constraints.
  - Test: Clean migration, rollback where supported, collation/timezone, transaction, and lock tests pass.
  - Status: `TODO`

- [ ] **TODO-007 — P0 — Implement authentication and RBAC foundation**
  - Dependencies: TODO-004, TODO-006
  - Description: Enforce Admin, Reviewer/PIC, and Viewer Policies/Gates; keep public submit/track notification endpoints outside internal session boundary.
  - Acceptance: Each role receives only approved capabilities and public clients cannot reach internal data/routes.
  - Test: Route/controller/policy matrix passes for each role and unauthenticated access.
  - Status: `TODO`

- [ ] **TODO-008 — P0 — Define API contracts, enums, translations, and safe projections**
  - Dependencies: TODO-003, TODO-004
  - Description: Define Form Requests, Resources, IDs, four request-type enums, localized validation/error contract, tracking projection, and evidence metadata contract.
  - Acceptance: Laravel is validation authority; internal/public projections differ; no user-facing strings are hardcoded.
  - Test: API/client/translation parity and internal-field exposure tests pass.
  - Status: `IN_PROGRESS` — Form Request/Resource boundary, safe projections, dan internal locale toggle tersedia; migrasi penuh ke translation keys serta coverage seluruh write-path/redaction masih tersisa.

- [ ] **TODO-008A — P0 — Map immutable Data sources and template download contract**
  - Dependencies: TODO-001A
  - Description: Map fields/sheets/visual sections, hash, type, normalization, owner, PII, ambiguity, and reference/import scope for all three `Data/` files; record `Template RFID 2025.xlsx` publication/version/hash contract.
  - Acceptance: No source value/PII is copied; JPEG is limited to approved procurement visual reference; template download is not treated as automatic parser/import.
  - Test: Before/after hashes, workbook/visual inspection, privacy review, and field-coverage review pass.
  - Status: `IN_PROGRESS` — mapping is revised for the active request/procurement model; hash/version publication and source-integrity endpoint tests remain.

- [ ] **TODO-008B — P0 — Resolve final request and receipt policies**
  - Dependencies: TODO-008A
  - Description: Confirm upload signature/malware/cleanup policy, RFID range/list source at procurement receipt, revision policy, and web-push channel policy.
  - Acceptance: Each decision is represented in PRD, API/schema rules, and testable acceptance criteria.
  - Test: Stakeholder decision review recorded in `update.md`.
  - Status: `IN_PROGRESS` — customer fields, manual priority/no-SLA, range-at-receipt, Admin-only reports, no-MVP-resubmit, export limits/retention, and in-app-first notifications are decided; signature/cleanup and optional fail-closed scanner integration are implemented, while actual Biznet scanner capability and VAPID hosting evidence remain.

## Phase 1 — Domain, workflow, and integrity

- [ ] **TODO-009 — P0 — Model customer request and customer identity**
  - Dependencies: TODO-006, TODO-008, TODO-008A, TODO-008B
  - Description: Model Customer and CustomerRequest with approved customer fields, `request_date`, request number, safe tracking credential, final enums (`Kartu baru`, `Kartu rusak`, `Kartu error system`, `Kartu hilang`), and indexes.
  - Acceptance: CRM accepts only 1–13 alphanumeric characters; removed request types cannot persist; request numbers are unique.
  - Test: Migration, relation, constraint, enum, CRM boundary, and indexed-query tests pass.
  - Status: `IN_PROGRESS` — migration, four-type contract, CRM boundary, safe tracking credential, and request date are implemented; MySQL-specific constraint/index verification remains.

- [ ] **TODO-010 — P0 — Model evidence and request history**
  - Dependencies: TODO-009
  - Description: Model separate Pengajuan Kartu and Bukti Bayar evidence plus append-only RequestStatusHistory with visibility classification.
  - Acceptance: Required artefact rules are enforced after policy confirmation; customer timeline cannot expose internal notes.
  - Test: Transaction, privacy projection, successful/failed transition, and concurrent transition tests pass.
  - Status: `IN_PROGRESS` — two typed private evidence artefacts and initial history are implemented; transition/privacy coverage remains.

- [ ] **TODO-011 — P0 — Model RFID inventory, ranges, movements, and assignments**
  - Dependencies: TODO-006, TODO-008A, TODO-009
  - Description: Model RFIDRange/RFIDCard, StockMovement, and RFIDAssignment with unique identity, range constraints, and source-ledger semantics.
  - Acceptance: No duplicate RFID, invalid range, orphan assignment, or negative availability can persist.
  - Test: Migration/database constraint/relation tests pass.
  - Status: `IN_PROGRESS` — model, unique RFID identity, range, movement, and assignment exist; MySQL constraint/concurrency verification remains.

- [ ] **TODO-012 — P0 — Model Catatan Pengadaan, notifications, audit, and export metadata**
  - Dependencies: TODO-006, TODO-007, TODO-008A, TODO-008B
  - Description: Replace StockRequest model/workflow with Catatan Pengadaan and receipt records; add Notification, immutable AuditLog, and ReportExport metadata when needed.
  - Acceptance: Requested/received values and actor/history are distinct; ordinary users cannot edit audit; notification recipient/privacy scope is enforced.
  - Test: Relation, immutability, authorization, migration, and privacy tests pass.
  - Status: `IN_PROGRESS` — note/receipt, audit, in-app notification records, internal presentation, and report-export metadata are implemented; broader audit coverage remains.

- [ ] **TODO-013 — P0 — Implement request and procurement state transitions**
  - Dependencies: TODO-009, TODO-010, TODO-012
  - Description: Enforce request status workflow and authorized procurement draft/receipt transitions without auto-approval or shipping lifecycle.
  - Acceptance: Only declared transitions succeed; approval/rejection and receipt history are atomic and auditable.
  - Test: Exhaustive transition-table, authorization, and rollback tests pass.
  - Status: `IN_PROGRESS` — one-time admin receipt with transactional Stock In is implemented; final request transition table remains.

- [ ] **TODO-014 — P0 — Implement request/evidence validation**
  - Dependencies: TODO-008, TODO-008A, TODO-008B, TODO-009
  - Description: Implement Form Request validation for four request types, CRM length/charset, Tanggal Permintaan, and two evidence artefacts.
  - Acceptance: No vehicle/item inline payload is accepted by the public contract; localization-safe errors identify invalid fields without leakage.
  - Test: Table-driven unit/API validation tests pass.
  - Status: `IN_PROGRESS` — public contract rejects legacy fields/items and validates CRM, four types, date, and two files; signature/malware and full localized-error coverage remain.

- [x] **TODO-015 — P0 — Implement target H+1 dan kontrol Utamakan**
  - Dependencies: TODO-009, TODO-013, TODO-008B
  - Description: Hitung target H+1 dari request\_date; rank Utamakan, terlambat/harus selesai hari ini, request\_date lama, lalu created\_at lama; kontrol Utamakan diaudit.
  - Acceptance: Ranking never changes request status; customer request date is never misused as a deadline.
  - Test: target H+1, ordering, Utamakan, authorization, notification, and audit regression tests pass.
  - Status: `DONE` — target/label, ordering, kontrol terotorisasi, audit, dan notifikasi due/late teruji.

- [ ] **TODO-016 — P0 — Seed and verify domain invariants**
  - Dependencies: TODO-009 through TODO-015
  - Description: Build deterministic non-PII fixtures for four types, evidence, inventory, procurement, notification, and reports.
  - Acceptance: Database recreates and seeds safely without source customer values.
  - Test: Empty database, reseed, invariants, and full domain suite pass.
  - Status: `IN_PROGRESS` — tracking now returns request-scoped notifications; customer status events, read-state endpoint, per-IP rate-limit test, and browser E2E are implemented. Notification privacy abuse and full seed/recreate evidence remain.

## Phase 2 — Customer Portal

- [ ] **TODO-017 — P1 — Build Customer Portal shell and responsive journey**
  - Dependencies: TODO-003, TODO-008
  - Description: Build ID-default/EN portal shell for template download, submit, tracking, and customer notifications using `Docs/uiux-direction.md`.
  - Acceptance: Core routes are clear and usable from phone to desktop; no internal module is bundled.
  - Test: Responsive, keyboard, and bundle-boundary tests pass.
  - Status: `IN_PROGRESS` — responsive ID/EN submit/track shell, browser matrix, and automated axe check pass; real-device/manual accessibility verification remain.

- [ ] **TODO-018 — P0 — Build simplified customer request form**
  - Dependencies: TODO-014, TODO-017
  - Description: Present approved customer fields, four-type selector, CRM 13-character alphanumeric control, and Tanggal Permintaan; remove inline vehicle/item UI.
  - Acceptance: Form mirrors server rules, is localized, and preserves safe entered data when type changes.
  - Test: Component tests cover enum choices, CRM boundaries, required fields, errors, and keyboard flow.
  - Status: `IN_PROGRESS` — approved fields and four types replace vehicle UI; CRM boundary component and keyboard/device coverage remain.

- [x] **TODO-019 — P0 — Publish controlled template download**
  - Dependencies: TODO-004, TODO-008A, TODO-017
  - Description: Provide authorized/publicly intended download for approved version/hash of Template RFID 2025 without modifying the Data source.
  - Acceptance: Download metadata/version is traceable and source asset remains immutable.
  - Test: Version/hash, content-disposition, access, and source-integrity tests pass.
  - Status: `DONE` — public download, content disposition, version header, verified SHA-256 hash, and immutable source boundary are tested.

- [ ] **TODO-020 — P0 — Implement secure two-artefact upload**
  - Dependencies: TODO-004, TODO-010, TODO-014, TODO-018
  - Description: Upload Pengajuan Kartu and Bukti Bayar separately to private storage with allowlisted type/signature/size/count policy.
  - Acceptance: Artefacts are distinct, unauthorized access is denied, invalid/disguised upload is rejected, and failures clean up safely.
  - Test: Upload integration, interruption, type/signature/size, authorization, malware-strategy, and cleanup tests pass.
  - Status: `IN_PROGRESS` — typed private uploads, type/size/signature validation, private authorization, and transaction-failure cleanup are implemented; malware scanner capability remains.

- [ ] **TODO-021 — P0 — Submit request and issue tracking credentials**
  - Dependencies: TODO-009, TODO-010, TODO-014, TODO-020
  - Description: Persist customer/request/evidence/initial history atomically and return only safe confirmation with tracking credentials.
  - Acceptance: Retries are idempotent and partial submissions do not remain.
  - Test: Success, duplicate, storage/database failure, rollback, uniqueness, and idempotency integration tests pass.
  - Status: `IN_PROGRESS` — transactional create/tracking/idempotency path is implemented and tested; storage failure/rollback coverage remains.

- [ ] **TODO-022 — P0 — Implement private customer tracking and notifications**
  - Dependencies: TODO-010, TODO-017, TODO-021, TODO-012
  - Description: Require Request Number + Tracking Code for timeline and customer inbox; deliver submit/status/action events.
  - Acceptance: Mismatched/unknown credentials reveal nothing and notifications cannot be enumerated.
  - Test: Privacy, rate-limit, notification read-state, and E2E tracking tests pass.
  - Status: `IN_PROGRESS` — tracking returns request-scoped notifications, customer status events include PROCESSING, request-level RFID assignment, per-IP abuse limit, and browser E2E are covered; manual device verification remains.

- [ ] **TODO-023 — P1 — Add customer web-push opt-in**
  - Dependencies: TODO-004, TODO-022, TODO-008B
  - Description: Add HTTPS/VAPID permission flow, subscription rotation/removal, and notification delivery fallback.
  - Acceptance: Refusal/unsubscribe is respected; in-app tracking inbox remains usable without push.
  - Test: Permission, subscription, unsubscribe, delivery-failure, and privacy tests pass.
  - Status: `BLOCKED` — delivery/channel and Biznet capability policy are pending.

- [ ] **TODO-024 — P1 — Implement revision/resubmission only after policy approval**
  - Dependencies: TODO-013, TODO-022, TODO-008B
  - Description: Implement approved change/evidence/version/credential rules for rejected or needs-revision requests.
  - Acceptance: Prior history remains immutable and resubmission returns to review safely.
  - Test: State/history/validation/E2E suite passes.
  - Status: `BLOCKED` — revision policy is not yet defined.

## Phase 3 — Internal request operations and notifications

- [x] **TODO-025 — P1 — Refactor internal navigation and remove NOPOL shell**
  - Dependencies: TODO-003, TODO-007
  - Description: Keep Monitoring, Pengajuan, Inventory, Catatan Pengadaan, Reports, Activity, Settings, and Notifications; remove Documents/NOPOL UI/routes from active navigation.
  - Acceptance: Removed feature is inaccessible from navigation and not presented as production functionality.
  - Test: Route/navigation/RBAC regression tests pass.
  - Status: `DONE` — Documents/NOPOL dan legacy Stock Request route, page, navigation, serta action/model aktif telah dihapus; migration compatibility dipertahankan.

- [ ] **TODO-026 — P0 — Build searchable customer request queue**
  - Dependencies: TODO-015, TODO-021, TODO-025
  - Description: List request artefact state with search/filter for approved fields, type, status, Utamakan, target state, and date range using `request_date`.
  - Acceptance: Server ordering/filtering is stable, bounded, shareable, and responsive.
  - Test: Query, pagination, combined-filter, authorization, empty state, and adaptive-table tests pass.
  - Status: `IN_PROGRESS` — queue memakai Utamakan, target state, request\_date, dan created\_at; combined search/type/date filter serta pagination kini punya regression coverage; verifikasi responsive/E2E internal masih tersisa.

- [ ] **TODO-027 — P0 — Build request detail, evidence review, and decision workspace**
  - Dependencies: TODO-010, TODO-020, TODO-025, TODO-026
  - Description: Show customer details, authorized private evidence access, manual priority, status history, and separated internal/customer-safe notes.
  - Acceptance: Reviewer can decide without unrelated records; Viewer cannot mutate; no private note reaches tracking.
  - Test: Permission, evidence access, privacy, responsive, and component-state tests pass.
  - Status: `IN_PROGRESS` — detail workspace, private evidence, status history, Utamakan, dan approve/reject tersedia; catatan internal terpisah dan coverage responsive masih tersisa.

- [ ] **TODO-028 — P0 — Implement manual approval/rejection**
  - Dependencies: TODO-013, TODO-027
  - Description: Add explicit authorized approve/reject action, customer-safe reason, internal note, and idempotent concurrency handling.
  - Acceptance: Every decision is transactional, historized, audited, and notification-aware.
  - Test: Decision, stale-version, duplicate action, authorization, rollback, and notification tests pass.
  - Status: `IN_PROGRESS` — approve/reject terotorisasi, atomik, historized, audited, dan customer-notified; concurrency edge-case coverage masih tersisa.

- [ ] **TODO-029 — P1 — Implement internal inbox and web-push opt-in**
  - Dependencies: TODO-012, TODO-021, TODO-028, TODO-004
  - Description: Deliver authorized in-app notifications for request, inventory, procurement, and status events; add opt-in web push after capability approval.
  - Acceptance: Messages are deduplicated, privacy-safe, localized, deep-link only to authorized records, and track read state.
  - Test: Event, deduplication, permission, read-state, push-subscription, and failure tests pass.
  - Status: `IN_PROGRESS` — in-app inbox, deduplication, read-state, dan due/late notification tersedia; web push deferred dan coverage privacy lebih luas masih tersisa.

## Phase 4 — Inventory and assignment

- [ ] **TODO-030 — P1 — Build RFID inventory and range search**
  - Dependencies: TODO-011, TODO-025
  - Description: Show cards/ranges, availability, source/movement, assignment, request reference, history, and responsive search.
  - Acceptance: Large ranges remain queryable without unsafe payloads and availability reconciles with ledger.
  - Test: Range, pagination, permission, responsive, and reconciliation tests pass.
  - Status: `IN_PROGRESS` — inventory summary, range search, Stock In/Out, assignment, dan low-stock alert tersedia; reconciliation UI dan responsive tests masih tersisa.

- [ ] **TODO-031 — P0 — Implement validated Stock In**
  - Dependencies: TODO-008A, TODO-011, TODO-015
  - Description: Record RFID start/end, derived quantity, source, date, PIC, movement, and audit with transactional range constraints.
  - Acceptance: Invalid/reversed/overlap/duplicate range rolls back completely and valid range updates once.
  - Test: Constraint, transaction, boundary, idempotency, overlap, and audit tests pass.
  - Status: `IN_PROGRESS` — range validation/locking and Stock In are reused by procurement receipt; MySQL concurrency verification remains.

- [ ] **TODO-032 — P0 — Implement transactional assignment and Stock Out**
  - Dependencies: TODO-028, TODO-030, TODO-031
  - Description: Turn approved request into RFID assignment, Stock Out, inventory update, PROCESSING, history, audit, and notification in one locked transaction.
  - Acceptance: Only eligible approved request receives available RFID; no partial/double assignment.
  - Test: MySQL/MariaDB concurrency, deadlock retry, idempotency, authorization, unavailable-card, and rollback tests pass.
  - Status: `IN_PROGRESS` — request-level locked assignment, Stock Out, PROCESSING, completion guard, retry, and unavailable-card handling are implemented; real MySQL concurrency/deadlock evidence remains.

- [ ] **TODO-033 — P1 — Build inventory history and low-stock alerting**
  - Dependencies: TODO-029, TODO-031, TODO-032
  - Description: Show ledger/history/current-stock and threshold-driven notification.
  - Acceptance: Formula reconciles, source/actor is traceable, and threshold configuration is authorized/audited.
  - Test: Reconciliation, threshold, notification deduplication, history, and authorization tests pass.
  - Status: `IN_PROGRESS` — stock summary, ledger writes, low-stock threshold, deduplicated notification, and audit exist; full reconciliation/history UI remains.

## Phase 5 — Catatan Pengadaan

- [ ] **TODO-034 — P1 — Build Admin Catatan Pengadaan**
  - Dependencies: TODO-007, TODO-012, TODO-025
  - Description: Create/edit Admin note with tanggal pengajuan, PIC, QTY diajukan, catatan, tanggal diterima, and QTY diterima.
  - Acceptance: Requested and receipt sections are distinct, history/audit captures edits, and non-Admin mutation is denied.
  - Test: Form, validation, authorization, draft/edit history, and responsive UI tests pass.
  - Status: `IN_PROGRESS` — responsive Admin create/list/one-time receipt workspace is implemented; edit history and responsive tests remain.

- [ ] **TODO-035 — P0 — Receive procurement into atomic Stock In**
  - Dependencies: TODO-008B, TODO-031, TODO-034
  - Description: Validate authorized RFID range/list source, then save receipt, Stock In, inventory update, history, audit, and notification in one idempotent transaction.
  - Acceptance: Receipt retry cannot double inventory; QTY cannot create assignable cards without unique RFID identity; failure rolls back all mutations.
  - Test: Receipt-to-stock-in, lock, idempotency, range/quantity mismatch, failure, and rollback tests pass.
  - Status: `IN_PROGRESS` — inclusive admin-provided range is the receipt source and tested for quantity match/idempotency; MySQL lock/rollback verification remains.

- [ ] **TODO-036 — P0 — Verify procurement-to-inventory journey**
  - Dependencies: TODO-034, TODO-035
  - Description: Reconcile requested/received data, Stock In, inventory total, notification, and audit.
  - Acceptance: Every sample procurement record reconciles exactly once.
  - Test: End-to-end and reconciliation suite pass.
  - Status: `IN_PROGRESS` — receipt-to-Stock In, quantity/range reconciliation, idempotency, notification, and audit are covered; MySQL concurrency verification remains.

## Phase 6 — Monitoring, reports, exports, audit

- [ ] **TODO-037 — P1 — Build consolidated monitoring dashboard**
  - Dependencies: TODO-028, TODO-033, TODO-036
  - Description: Combine related KPI, attention queue, stock health, activity, and notification summary with drill-down.
  - Acceptance: Metrics have definition/period/freshness/RBAC and reconcile with source records.
  - Test: Metric, reconciliation, permission, responsive, empty/loading, and partial-failure tests pass.
  - Status: `IN_PROGRESS` — monitoring memakai metrik database, queue live, stock summary, notification count, dan AuditLog activity; freshness/error-state dan rekonsiliasi penuh masih tersisa.

- [x] **TODO-038 — P1 — Implement Customer Request Detail Report**
  - Dependencies: TODO-028, TODO-033
  - Description: Provide filtered detailed customer-request report containing pending/unapproved and approved records with authorized fields/history.
  - Acceptance: Detail rows reconcile with request records and protect private artefact/credential data.
  - Test: Filter, date/timezone, status, pagination, authorization, and reconciliation tests pass.
  - Status: `DONE` — Admin-only detail report dengan filter request\_date/status/type dan pagination telah diuji.

- [x] **TODO-039 — P1 — Implement Summary Report**
  - Dependencies: TODO-033, TODO-036, TODO-038
  - Description: Derive summary from detail/report source and Stock In/Out ledger: counts by status plus RFID in/out for matching filter.
  - Acceptance: Every displayed total is explainable from underlying authorized detail/ledger data.
  - Test: Cross-report reconciliation, date boundary, filter, authorization, and performance tests pass.
  - Status: `DONE` — summary status request dan Stock In/Out memakai filter aktif telah diuji.

- [x] **TODO-040 — P1 — Export reports to Excel and PDF**
  - Dependencies: TODO-038, TODO-039, TODO-008B
  - Description: Export the active authorized report filter to Excel and PDF with ID/EN labels, private storage/download, formula-injection protection, and size/row policy.
  - Acceptance: Files match displayed authorized data, omit secrets/private links, and safely handle spreadsheet formulas.
  - Test: Format/schema, filter equivalence, localization, authorization, formula injection, row-limit, and large-export tests pass.
  - Status: `DONE` — Admin-only export, batas row, private storage, metadata/audit, download expiry, dan scheduled cleanup telah diimplementasikan; format/filter/authorization dasar teruji.

- [ ] **TODO-041 — P0 — Implement immutable audit pipeline and activity UI**
  - Dependencies: TODO-007, TODO-012
  - Description: Audit all sensitive/business writes, procurement receipt, report export, and notification actions; display authorized/redacted activity.
  - Acceptance: Events include actor/action/entity/time/safe metadata/correlation and cannot be modified by ordinary users.
  - Test: Immutability, transaction, redaction, actor, filtering, pagination, and privileged-access tests pass.
  - Status: `IN_PROGRESS` — AuditLog immutable terhubung ke Activity dan report export; coverage seluruh write-path, redaction, dan immutability test masih tersisa.

## Phase 7 — Responsive, accessibility, security, production

- [ ] **TODO-042 — P0 — Apply responsive and accessibility acceptance matrix**
  - Dependencies: TODO-017 through TODO-041 as applicable
  - Description: Apply `Docs/uiux-direction.md` to customer/admin flows, adaptive data tables, focus/errors/announcements, touch targets, and reduced motion.
  - Acceptance: No critical action requires hover or horizontal page scroll; WCAG 2.2 AA target is met.
  - Test: 320/375/768/1024/1440 viewport, orientation, touch, keyboard, 200% zoom, ID/EN, automated and manual accessibility checks pass.
  - Status: `IN_PROGRESS` — request queue/reports memiliki card view, touch target, focus label, browser viewport matrix, dan portal axe check; device/WCAG manual matrix masih tersisa.

- [ ] **TODO-043 — P0 — Security and privacy hardening**
  - Dependencies: TODO-022, TODO-029, TODO-032, TODO-035, TODO-040, TODO-041
  - Description: Review Policies, API Resources, tracking/notif privacy, CORS/CSRF, rate limits, uploads, headers, dependencies, secrets, and private exports.
  - Acceptance: Every endpoint has validation/authorization decision; no critical/high issue is unresolved without owner/mitigation.
  - Test: Security review, static/dependency/secret scan, authorization, abuse, and upload tests pass.
  - Status: `IN_PROGRESS` — RBAC, private evidence/export, tracking privacy, per-IP rate limit, headers, signature validation, cleanup, optional scanner, and dependency scans are covered; Biznet capability and broader abuse matrix remain.

- [ ] **TODO-044 — P0 — Production deployment and recovery gate**
  - Dependencies: TODO-002 through TODO-043 excluding explicitly deferred/blockers
  - Description: Deploy independently to Biznet/cPanel, verify TLS/domain isolation, migration/release/rollback, backup/restore, and monitoring.
  - Acceptance: Staging and production runbooks are executed with documented recovery evidence.
  - Test: Deployment smoke, restore drill, rollback, health, and release checklist pass.
  - Status: `TODO`

## Current blockers

- **TODO-008B:** upload malware scanner capability dan Biznet production capability verification.
- **TODO-023:** customer web-push waits for channel and hosting decision; customer in-app/tracking notifications do not wait for it.
- **TODO-024:** resubmission policy absent.

## Final Kartu Baru workflow

- [x] **TODO-045 — P0 — Implement CRM quota and per-request quantity/payment workflow**
  - Acceptance: free quota 30 per CRM, per-request quantity, server-side total/used/remaining, mandatory payment upload, approval recheck with transaction/locking, and quota history.
  - Verification: `KartuBaruQuotaTest` 5 tests/38 assertions plus full Laravel suite passed.

- [x] **TODO-046 — P0 — Implement customer detection and internal quota visibility**
  - Acceptance: CRM remains primary identifier, normalized company name raises warning without auto-merge, dashboard exposes quota summary/history, portal exposes no quota numbers.
  - Verification: duplicate-name and dashboard resource/static checks passed.

- [x] **TODO-047 — P0 — Verify shared ngrok user-testing origin**
  - Acceptance: Customer Portal `/`, internal `/login`/`/dashboard`, and Customer API share one ngrok origin through Vite proxy.
  - Verification: portal/login/API/template HTTP checks passed; final sample returned 201 and appeared in the internal dashboard data path.
  - Note: internal dashboard browser smoke now passes through ngrok; external browser multipart upload still has the known PHP artisan serve transport caveat, while the same API path passes with real files through curl/APIRequest.

## Release-ready closure — 2026-09-23

- [x] Single Laravel + Inertia + React runtime with public `/pengajuan`/`/tracking` and internal `/admin`.
- [x] Admin/Viewer RBAC; Viewer writes/evidence download denied, report/export allowed; guest admin denied.
- [x] Partial procurement and actual-quantity inventory; no manual Stock In or RFID start/end receipt input.
- [x] Required quantity for every request type; optional payment proof; quota 30 removed from active workflow.
- [x] Inventory custom filters; Monitoring report periods and PDF/Excel export.
- [x] Header role-aware notifications, Activity category filters, Indonesian default + EN toggle, responsive sidebar/drawer.
- [x] Backend regression evidence: 65 tests passed, 342 assertions. Frontend verification is green.
- [x] Final UI closure 2026-09-24: public submit/track tabs, Admin/Viewer entrypoints, notification popup-only flow, preferences in Pengaturan, responsive/sidebar/accessibility QA.
- [x] Final verification: 66 Laravel tests / 333 assertions, UI test, typecheck, lint, build, Lighthouse accessibility 100 desktop/mobile, and browser console clean.

> Older checklist items describing quota 30, mandatory payment, Customer login, standalone portal, or the notification page are superseded by the authoritative baseline in `Docs/PRD.md`.

- [x] Final UI refinement: two-card public form, download-before-upload flow, accessible custom file controls, compact settings, responsive sidebar, and ID/EN verification.
- [x] Public form alignment recheck: stacked information fields, green download action, clean document upload layout, and optional payment behavior retained.
- [x] Customer root entry: `/` renders RequestPortal directly while `/pengajuan` remains a compatibility redirect.
- [x] Integrate reusable RFID navbar component under the existing shadcn alias structure without adding redundant dependencies or a resizable main sidebar.
- [x] Navbar/sidebar final: 256px expanded, 64px collapsed icon rail, tooltip, active state, mobile drawer, shared Admin/Viewer navigation, header actions, and red logout hover/focus.

---

You are given a task to integrate an existing React component in the codebase

The codebase should support:

- shadcn project structure
- Tailwind CSS
- Typescript

If it doesn't, provide instructions on how to setup project via shadcn CLI, install Tailwind or Typescript.

Determine the default path for components and styles.  
If default path for components is not /components/ui, provide instructions on why it's important to create this folder  
Copy-paste this component to /components/ui folder:

```
navbars.tsx
"use client";
import React, { useState } from "react";
import { Button } from "@/components/ui/button";
import { ScrollArea } from "@/components/ui/scroll-area";
import { Sheet, SheetContent, SheetTrigger } from "@/components/ui/sheet";
import { Home, Menu, MessageSquare, Settings, Users } from "lucide-react";
import {
  ResizableHandle,
  ResizablePanel,
  ResizablePanelGroup,
} from "@/components/ui/resizable";

export default function Navbardemo() {
  const [open, setOpen] = useState(false);

  return (
    <div className="flex ">
      <Sheet open={open} onOpenChange={setOpen}>
        <SheetTrigger asChild>
          <Button
            variant="outline"
            size="icon"
            className="fixed left-4 top-4 lg:hidden"
          >
            <Menu className="h-6 w-6" />
          </Button>
        </SheetTrigger>
        <SheetContent side="left" className="w-[240px] p-0">
          <VerticalNav />
        </SheetContent>
      </Sheet>
      <ResizablePanelGroup
        direction="horizontal"
        className="min-h-[200px] max-w-md rounded-lg border md:min-w-[450px]"
      >
        <ResizablePanel defaultSize={70}>
          <div className="flex h-full items-center justify-center p-6">
            <VerticalNav />
          </div>
        </ResizablePanel>
        <ResizableHandle withHandle />
        <ResizablePanel defaultSize={85}>
          <div className="flex h-full items-center justify-center p-6">
            <span className="font-semibold">Content</span>
          </div>
        </ResizablePanel>
      </ResizablePanelGroup>
    </div>
  );
}

function VerticalNav() {
  return (
    <ScrollArea className="h-full py-6">
      <div className="px-3 py-2">
        <h2 className="mb-2 px-4 text-lg font-semibold">Dashboard</h2>
        <div className="space-y-1">
          <Button variant="ghost" className="w-full justify-start">
            <Home className="mr-2 h-4 w-4" />
            Home
          </Button>
          <Button variant="ghost" className="w-full justify-start">
            <Users className="mr-2 h-4 w-4" />
            Team
          </Button>
          <Button variant="ghost" className="w-full justify-start">
            <MessageSquare className="mr-2 h-4 w-4" />
            Messages
          </Button>
          <Button variant="ghost" className="w-full justify-start">
            <Settings className="mr-2 h-4 w-4" />
            Settings
          </Button>
        </div>
      </div>
    </ScrollArea>
  );
}


demo.tsx
"use client";
import { Button } from "@/components/ui/button";
import {
  Collapsible,
  CollapsibleContent,
  CollapsibleTrigger,
} from "@/components/ui/collapsible";
import { ScrollArea } from "@/components/ui/scroll-area";
import {
  ChevronRight,
  Home,
  Menu,
  Package,
  Settings,
  Users,
} from "lucide-react";
import { useState } from "react";

export default function Sidenavbar() {
  const [isOpen, setIsOpen] = useState(false);

  return (
    //add h-screen to the div class name to make the sidebar full height
    <div className="flex ">
      <aside
        className={`${
          isOpen ? "w-64" : "w-16"
        } flex flex-col border-r transition-all duration-300 ease-in-out`}
      >
        <div className="flex h-16 items-center justify-between border-b px-4">
          <span
            className={`${isOpen ? "block" : "hidden"} text-lg font-semibold`}
          >
            Menu
          </span>
          <Button
            variant="ghost"
            size="icon"
            onClick={() => setIsOpen(!isOpen)}
          >
            <Menu className="h-6 w-6" />
          </Button>
        </div>
        <ScrollArea className="flex-1">
          <nav className="p-2">
            <Button variant="ghost" className="w-full justify-start">
              <Home className="mr-2 h-4 w-4" />
              {isOpen && "Home"}
            </Button>
            {isOpen ? (
              <Collapsible>
                <CollapsibleTrigger asChild>
                  <Button variant="ghost" className="w-full justify-start">
                    <Package className="mr-2 h-4 w-4" />
                    {isOpen && (
                      <>
                        Products
                        <ChevronRight className="ml-auto h-4 w-4" />
                      </>
                    )}
                  </Button>
                </CollapsibleTrigger>
                <CollapsibleContent className="ml-4 space-y-1">
                  <Button
                    variant="ghost"
                    size="sm"
                    className="w-full justify-start"
                  >
                    Category 1
                  </Button>
                  <Button
                    variant="ghost"
                    size="sm"
                    className="w-full justify-start"
                  >
                    Category 2
                  </Button>
                  <Button
                    variant="ghost"
                    size="sm"
                    className="w-full justify-start"
                  >
                    Category 3
                  </Button>
                </CollapsibleContent>
              </Collapsible>
            ) : (
              <Button variant="ghost">
                <Package className="mr-2 h-4 w-4" />
              </Button>
            )}
            <Button variant="ghost" className="w-full justify-start">
              <Users className="mr-2 h-4 w-4" />
              {isOpen && "Users"}
            </Button>
            <Button variant="ghost" className="w-full justify-start">
              <Settings className="mr-2 h-4 w-4" />
              {isOpen && "Settings"}
            </Button>
          </nav>
        </ScrollArea>
      </aside>
      <main className="flex-1 p-6">
        <h1 className="text-2xl font-bold">Main Content Area</h1>
      </main>
    </div>
  );
}

```

Copy-paste these files for dependencies:

```
shadcn/button
import * as React from "react"
import { Slot } from "@radix-ui/react-slot"
import { cva, type VariantProps } from "class-variance-authority"

import { cn } from "@/lib/utils"

const buttonVariants = cva(
  "inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50",
  {
    variants: {
      variant: {
        default: "bg-primary text-primary-foreground hover:bg-primary/90",
        destructive:
          "bg-destructive text-destructive-foreground hover:bg-destructive/90",
        outline:
          "border border-input bg-background hover:bg-accent hover:text-accent-foreground",
        secondary:
          "bg-secondary text-secondary-foreground hover:bg-secondary/80",
        ghost: "hover:bg-accent hover:text-accent-foreground",
        link: "text-primary underline-offset-4 hover:underline",
      },
      size: {
        default: "h-10 px-4 py-2",
        sm: "h-9 rounded-md px-3",
        lg: "h-11 rounded-md px-8",
        icon: "h-10 w-10",
      },
    },
    defaultVariants: {
      variant: "default",
      size: "default",
    },
  },
)

export interface ButtonProps
  extends React.ButtonHTMLAttributes<HTMLButtonElement>,
    VariantProps<typeof buttonVariants> {
  asChild?: boolean
}

const Button = React.forwardRef<HTMLButtonElement, ButtonProps>(
  ({ className, variant, size, asChild = false, ...props }, ref) => {
    const Comp = asChild ? Slot : "button"
    return (
      <Comp
        className={cn(buttonVariants({ variant, size, className }))}
        ref={ref}
        {...props}
      />
    )
  },
)
Button.displayName = "Button"

export { Button, buttonVariants }

```

```
shadcn/scroll-area
"use client"

import * as React from "react"
import * as ScrollAreaPrimitive from "@radix-ui/react-scroll-area"

import { cn } from "@/lib/utils"

const ScrollArea = React.forwardRef<
  React.ElementRef<typeof ScrollAreaPrimitive.Root>,
  React.ComponentPropsWithoutRef<typeof ScrollAreaPrimitive.Root>
>(({ className, children, ...props }, ref) => (
  <ScrollAreaPrimitive.Root
    ref={ref}
    className={cn("relative overflow-hidden", className)}
    {...props}
  >
    <ScrollAreaPrimitive.Viewport className="h-full w-full rounded-[inherit]">
      {children}
    </ScrollAreaPrimitive.Viewport>
    <ScrollBar />
    <ScrollAreaPrimitive.Corner />
  </ScrollAreaPrimitive.Root>
))
ScrollArea.displayName = ScrollAreaPrimitive.Root.displayName

const ScrollBar = React.forwardRef<
  React.ElementRef<typeof ScrollAreaPrimitive.ScrollAreaScrollbar>,
  React.ComponentPropsWithoutRef<typeof ScrollAreaPrimitive.ScrollAreaScrollbar>
>(({ className, orientation = "vertical", ...props }, ref) => (
  <ScrollAreaPrimitive.ScrollAreaScrollbar
    ref={ref}
    orientation={orientation}
    className={cn(
      "flex touch-none select-none transition-colors",
      orientation === "vertical" &&
        "h-full w-2.5 border-l border-l-transparent p-[1px]",
      orientation === "horizontal" &&
        "h-2.5 flex-col border-t border-t-transparent p-[1px]",
      className,
    )}
    {...props}
  >
    <ScrollAreaPrimitive.ScrollAreaThumb className="relative flex-1 rounded-full bg-border" />
  </ScrollAreaPrimitive.ScrollAreaScrollbar>
))
ScrollBar.displayName = ScrollAreaPrimitive.ScrollAreaScrollbar.displayName

export { ScrollArea, ScrollBar }

```

```
shadcn/separator
"use client"

import * as React from "react"
import * as SeparatorPrimitive from "@radix-ui/react-separator"

import { cn } from "@/lib/utils"

const Separator = React.forwardRef<
  React.ElementRef<typeof SeparatorPrimitive.Root>,
  React.ComponentPropsWithoutRef<typeof SeparatorPrimitive.Root>
>(
  (
    { className, orientation = "horizontal", decorative = true, ...props },
    ref
  ) => (
    <SeparatorPrimitive.Root
      ref={ref}
      decorative={decorative}
      orientation={orientation}
      className={cn(
        "shrink-0 bg-border",
        orientation === "horizontal" ? "h-[1px] w-full" : "h-full w-[1px]",
        className
      )}
      {...props}
    />
  )
)
Separator.displayName = SeparatorPrimitive.Root.displayName

export { Separator }

```

```
shadcn/sheet
"use client"

import * as React from "react"
import * as SheetPrimitive from "@radix-ui/react-dialog"
import { cva, type VariantProps } from "class-variance-authority"
import { X } from "lucide-react"

import { cn } from "@/lib/utils"

const Sheet = SheetPrimitive.Root

const SheetTrigger = SheetPrimitive.Trigger

const SheetClose = SheetPrimitive.Close

const SheetPortal = SheetPrimitive.Portal

const SheetOverlay = React.forwardRef<
  React.ElementRef<typeof SheetPrimitive.Overlay>,
  React.ComponentPropsWithoutRef<typeof SheetPrimitive.Overlay>
>(({ className, ...props }, ref) => (
  <SheetPrimitive.Overlay
    className={cn(
      "fixed inset-0 z-50 bg-black/80  data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0",
      className,
    )}
    {...props}
    ref={ref}
  />
))
SheetOverlay.displayName = SheetPrimitive.Overlay.displayName

const sheetVariants = cva(
  "fixed z-50 gap-4 bg-background p-6 shadow-lg transition ease-in-out data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:duration-300 data-[state=open]:duration-500",
  {
    variants: {
      side: {
        top: "inset-x-0 top-0 border-b data-[state=closed]:slide-out-to-top data-[state=open]:slide-in-from-top",
        bottom:
          "inset-x-0 bottom-0 border-t data-[state=closed]:slide-out-to-bottom data-[state=open]:slide-in-from-bottom",
        left: "inset-y-0 left-0 h-full w-3/4 border-r data-[state=closed]:slide-out-to-left data-[state=open]:slide-in-from-left sm:max-w-sm",
        right:
          "inset-y-0 right-0 h-full w-3/4  border-l data-[state=closed]:slide-out-to-right data-[state=open]:slide-in-from-right sm:max-w-sm",
      },
    },
    defaultVariants: {
      side: "right",
    },
  },
)

interface SheetContentProps
  extends React.ComponentPropsWithoutRef<typeof SheetPrimitive.Content>,
    VariantProps<typeof sheetVariants> {}

const SheetContent = React.forwardRef<
  React.ElementRef<typeof SheetPrimitive.Content>,
  SheetContentProps
>(({ side = "right", className, children, ...props }, ref) => (
  <SheetPortal>
    <SheetOverlay />
    <SheetPrimitive.Content
      ref={ref}
      className={cn(sheetVariants({ side }), className)}
      {...props}
    >
      {children}
      <SheetPrimitive.Close className="absolute right-4 top-4 rounded-sm opacity-70 ring-offset-background transition-opacity hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:pointer-events-none data-[state=open]:bg-secondary">
        <X className="h-4 w-4" />
        <span className="sr-only">Close</span>
      </SheetPrimitive.Close>
    </SheetPrimitive.Content>
  </SheetPortal>
))
SheetContent.displayName = SheetPrimitive.Content.displayName

const SheetHeader = ({
  className,
  ...props
}: React.HTMLAttributes<HTMLDivElement>) => (
  <div
    className={cn(
      "flex flex-col space-y-2 text-center sm:text-left",
      className,
    )}
    {...props}
  />
)
SheetHeader.displayName = "SheetHeader"

const SheetFooter = ({
  className,
  ...props
}: React.HTMLAttributes<HTMLDivElement>) => (
  <div
    className={cn(
      "flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2",
      className,
    )}
    {...props}
  />
)
SheetFooter.displayName = "SheetFooter"

const SheetTitle = React.forwardRef<
  React.ElementRef<typeof SheetPrimitive.Title>,
  React.ComponentPropsWithoutRef<typeof SheetPrimitive.Title>
>(({ className, ...props }, ref) => (
  <SheetPrimitive.Title
    ref={ref}
    className={cn("text-lg font-semibold text-foreground", className)}
    {...props}
  />
))
SheetTitle.displayName = SheetPrimitive.Title.displayName

const SheetDescription = React.forwardRef<
  React.ElementRef<typeof SheetPrimitive.Description>,
  React.ComponentPropsWithoutRef<typeof SheetPrimitive.Description>
>(({ className, ...props }, ref) => (
  <SheetPrimitive.Description
    ref={ref}
    className={cn("text-sm text-muted-foreground", className)}
    {...props}
  />
))
SheetDescription.displayName = SheetPrimitive.Description.displayName

export {
  Sheet,
  SheetPortal,
  SheetOverlay,
  SheetTrigger,
  SheetClose,
  SheetContent,
  SheetHeader,
  SheetFooter,
  SheetTitle,
  SheetDescription,
}

```

```
shadcn/input
import * as React from "react"

import { cn } from "@/lib/utils"

export interface InputProps
  extends React.InputHTMLAttributes<HTMLInputElement> {}

const Input = React.forwardRef<HTMLInputElement, InputProps>(
  ({ className, type, ...props }, ref) => {
    return (
      <input
        type={type}
        className={cn(
          "flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50",
          className
        )}
        ref={ref}
        {...props}
      />
    )
  }
)
Input.displayName = "Input"

export { Input }

```

```
shadcn/label
"use client"

import * as React from "react"
import * as LabelPrimitive from "@radix-ui/react-label"
import { cva, type VariantProps } from "class-variance-authority"

import { cn } from "@/lib/utils"

const labelVariants = cva(
  "text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70",
)

const Label = React.forwardRef<
  React.ElementRef<typeof LabelPrimitive.Root>,
  React.ComponentPropsWithoutRef<typeof LabelPrimitive.Root> &
    VariantProps<typeof labelVariants>
>(({ className, ...props }, ref) => (
  <LabelPrimitive.Root
    ref={ref}
    className={cn(labelVariants(), className)}
    {...props}
  />
))
Label.displayName = LabelPrimitive.Root.displayName

export { Label }

```

```
shadcn/avatar
"use client";

import * as React from "react";
import * as AvatarPrimitive from "@radix-ui/react-avatar";

import { cn } from "@/lib/utils";

const Avatar = React.forwardRef<
  React.ElementRef<typeof AvatarPrimitive.Root>,
  React.ComponentPropsWithoutRef<typeof AvatarPrimitive.Root>
>(({ className, ...props }, ref) => (
  <AvatarPrimitive.Root
    ref={ref}
    className={cn(
      "relative flex h-10 w-10 shrink-0 overflow-hidden rounded-full",
      className,
    )}
    {...props}
  />
));
Avatar.displayName = AvatarPrimitive.Root.displayName;

const AvatarImage = React.forwardRef<
  React.ElementRef<typeof AvatarPrimitive.Image>,
  React.ComponentPropsWithoutRef<typeof AvatarPrimitive.Image>
>(({ className, ...props }, ref) => (
  <AvatarPrimitive.Image
    ref={ref}
    className={cn("aspect-square h-full w-full", className)}
    {...props}
  />
));
AvatarImage.displayName = AvatarPrimitive.Image.displayName;

const AvatarFallback = React.forwardRef<
  React.ElementRef<typeof AvatarPrimitive.Fallback>,
  React.ComponentPropsWithoutRef<typeof AvatarPrimitive.Fallback>
>(({ className, ...props }, ref) => (
  <AvatarPrimitive.Fallback
    ref={ref}
    className={cn(
      "flex h-full w-full items-center justify-center rounded-full bg-muted",
      className,
    )}
    {...props}
  />
));
AvatarFallback.displayName = AvatarPrimitive.Fallback.displayName;

export { Avatar, AvatarImage, AvatarFallback };

export default Avatar;

```

```
shadcn/navigation-menu
import * as React from "react"
import { ChevronDownIcon } from "@radix-ui/react-icons"
import * as NavigationMenuPrimitive from "@radix-ui/react-navigation-menu"
import { cva } from "class-variance-authority"

import { cn } from "@/lib/utils"

const NavigationMenu = React.forwardRef<
  React.ElementRef<typeof NavigationMenuPrimitive.Root>,
  React.ComponentPropsWithoutRef<typeof NavigationMenuPrimitive.Root>
>(({ className, children, ...props }, ref) => (
  <NavigationMenuPrimitive.Root
    ref={ref}
    className={cn(
      "relative z-10 flex max-w-max flex-1 items-center justify-center",
      className
    )}
    {...props}
  >
    {children}
    <NavigationMenuViewport />
  </NavigationMenuPrimitive.Root>
))
NavigationMenu.displayName = NavigationMenuPrimitive.Root.displayName

const NavigationMenuList = React.forwardRef<
  React.ElementRef<typeof NavigationMenuPrimitive.List>,
  React.ComponentPropsWithoutRef<typeof NavigationMenuPrimitive.List>
>(({ className, ...props }, ref) => (
  <NavigationMenuPrimitive.List
    ref={ref}
    className={cn(
      "group flex flex-1 list-none items-center justify-center space-x-1",
      className
    )}
    {...props}
  />
))
NavigationMenuList.displayName = NavigationMenuPrimitive.List.displayName

const NavigationMenuItem = NavigationMenuPrimitive.Item

const navigationMenuTriggerStyle = cva(
  "group inline-flex h-9 w-max items-center justify-center rounded-md bg-background px-4 py-2 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground focus:outline-none disabled:pointer-events-none disabled:opacity-50 data-[active]:bg-accent/50 data-[state=open]:bg-accent/50"
)

const NavigationMenuTrigger = React.forwardRef<
  React.ElementRef<typeof NavigationMenuPrimitive.Trigger>,
  React.ComponentPropsWithoutRef<typeof NavigationMenuPrimitive.Trigger>
>(({ className, children, ...props }, ref) => (
  <NavigationMenuPrimitive.Trigger
    ref={ref}
    className={cn(navigationMenuTriggerStyle(), "group", className)}
    {...props}
  >
    {children}{" "}
    <ChevronDownIcon
      className="relative top-[1px] ml-1 h-3 w-3 transition duration-300 group-data-[state=open]:rotate-180"
      aria-hidden="true"
    />
  </NavigationMenuPrimitive.Trigger>
))
NavigationMenuTrigger.displayName = NavigationMenuPrimitive.Trigger.displayName

const NavigationMenuContent = React.forwardRef<
  React.ElementRef<typeof NavigationMenuPrimitive.Content>,
  React.ComponentPropsWithoutRef<typeof NavigationMenuPrimitive.Content>
>(({ className, ...props }, ref) => (
  <NavigationMenuPrimitive.Content
    ref={ref}
    className={cn(
      "left-0 top-0 w-full data-[motion^=from-]:animate-in data-[motion^=to-]:animate-out data-[motion^=from-]:fade-in data-[motion^=to-]:fade-out data-[motion=from-end]:slide-in-from-right-52 data-[motion=from-start]:slide-in-from-left-52 data-[motion=to-end]:slide-out-to-right-52 data-[motion=to-start]:slide-out-to-left-52 md:absolute md:w-auto ",
      className
    )}
    {...props}
  />
))
NavigationMenuContent.displayName = NavigationMenuPrimitive.Content.displayName

const NavigationMenuLink = NavigationMenuPrimitive.Link

const NavigationMenuViewport = React.forwardRef<
  React.ElementRef<typeof NavigationMenuPrimitive.Viewport>,
  React.ComponentPropsWithoutRef<typeof NavigationMenuPrimitive.Viewport>
>(({ className, ...props }, ref) => (
  <div className={cn("absolute left-0 top-full flex justify-center")}>
    <NavigationMenuPrimitive.Viewport
      className={cn(
        "origin-top-center relative mt-1.5 h-[var(--radix-navigation-menu-viewport-height)] w-full overflow-hidden rounded-md border bg-popover text-popover-foreground shadow data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-90 md:w-[var(--radix-navigation-menu-viewport-width)]",
        className
      )}
      ref={ref}
      {...props}
    />
  </div>
))
NavigationMenuViewport.displayName =
  NavigationMenuPrimitive.Viewport.displayName

const NavigationMenuIndicator = React.forwardRef<
  React.ElementRef<typeof NavigationMenuPrimitive.Indicator>,
  React.ComponentPropsWithoutRef<typeof NavigationMenuPrimitive.Indicator>
>(({ className, ...props }, ref) => (
  <NavigationMenuPrimitive.Indicator
    ref={ref}
    className={cn(
      "top-full z-[1] flex h-1.5 items-end justify-center overflow-hidden data-[state=visible]:animate-in data-[state=hidden]:animate-out data-[state=hidden]:fade-out data-[state=visible]:fade-in",
      className
    )}
    {...props}
  >
    <div className="relative top-[60%] h-2 w-2 rotate-45 rounded-tl-sm bg-border shadow-md" />
  </NavigationMenuPrimitive.Indicator>
))
NavigationMenuIndicator.displayName =
  NavigationMenuPrimitive.Indicator.displayName

export {
  navigationMenuTriggerStyle,
  NavigationMenu,
  NavigationMenuList,
  NavigationMenuItem,
  NavigationMenuContent,
  NavigationMenuTrigger,
  NavigationMenuLink,
  NavigationMenuIndicator,
  NavigationMenuViewport,
}

```

```
shadcn/dropdown-menu
"use client"

import * as React from "react"
import * as DropdownMenuPrimitive from "@radix-ui/react-dropdown-menu"
import { Check, ChevronRight, Circle } from "lucide-react"

import { cn } from "@/lib/utils"

const DropdownMenu = DropdownMenuPrimitive.Root

const DropdownMenuTrigger = DropdownMenuPrimitive.Trigger

const DropdownMenuGroup = DropdownMenuPrimitive.Group

const DropdownMenuPortal = DropdownMenuPrimitive.Portal

const DropdownMenuSub = DropdownMenuPrimitive.Sub

const DropdownMenuRadioGroup = DropdownMenuPrimitive.RadioGroup

const DropdownMenuSubTrigger = React.forwardRef<
  React.ElementRef<typeof DropdownMenuPrimitive.SubTrigger>,
  React.ComponentPropsWithoutRef<typeof DropdownMenuPrimitive.SubTrigger> & {
    inset?: boolean
  }
>(({ className, inset, children, ...props }, ref) => (
  <DropdownMenuPrimitive.SubTrigger
    ref={ref}
    className={cn(
      "flex cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none focus:bg-accent data-[state=open]:bg-accent",
      inset && "pl-8",
      className,
    )}
    {...props}
  >
    {children}
    <ChevronRight className="ml-auto h-4 w-4" />
  </DropdownMenuPrimitive.SubTrigger>
))
DropdownMenuSubTrigger.displayName =
  DropdownMenuPrimitive.SubTrigger.displayName

const DropdownMenuSubContent = React.forwardRef<
  React.ElementRef<typeof DropdownMenuPrimitive.SubContent>,
  React.ComponentPropsWithoutRef<typeof DropdownMenuPrimitive.SubContent>
>(({ className, ...props }, ref) => (
  <DropdownMenuPrimitive.SubContent
    ref={ref}
    className={cn(
      "z-50 min-w-[8rem] overflow-hidden rounded-md border bg-popover p-1 text-popover-foreground shadow-lg data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2",
      className,
    )}
    {...props}
  />
))
DropdownMenuSubContent.displayName =
  DropdownMenuPrimitive.SubContent.displayName

const DropdownMenuContent = React.forwardRef<
  React.ElementRef<typeof DropdownMenuPrimitive.Content>,
  React.ComponentPropsWithoutRef<typeof DropdownMenuPrimitive.Content>
>(({ className, sideOffset = 4, ...props }, ref) => (
  <DropdownMenuPrimitive.Portal>
    <DropdownMenuPrimitive.Content
      ref={ref}
      sideOffset={sideOffset}
      className={cn(
        "z-50 min-w-[8rem] overflow-hidden rounded-md border bg-popover p-1 text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2",
        className,
      )}
      {...props}
    />
  </DropdownMenuPrimitive.Portal>
))
DropdownMenuContent.displayName = DropdownMenuPrimitive.Content.displayName

const DropdownMenuItem = React.forwardRef<
  React.ElementRef<typeof DropdownMenuPrimitive.Item>,
  React.ComponentPropsWithoutRef<typeof DropdownMenuPrimitive.Item> & {
    inset?: boolean
  }
>(({ className, inset, ...props }, ref) => (
  <DropdownMenuPrimitive.Item
    ref={ref}
    className={cn(
      "relative flex cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none transition-colors focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50",
      inset && "pl-8",
      className,
    )}
    {...props}
  />
))
DropdownMenuItem.displayName = DropdownMenuPrimitive.Item.displayName

const DropdownMenuCheckboxItem = React.forwardRef<
  React.ElementRef<typeof DropdownMenuPrimitive.CheckboxItem>,
  React.ComponentPropsWithoutRef<typeof DropdownMenuPrimitive.CheckboxItem>
>(({ className, children, checked, ...props }, ref) => (
  <DropdownMenuPrimitive.CheckboxItem
    ref={ref}
    className={cn(
      "relative flex cursor-default select-none items-center rounded-sm py-1.5 pl-8 pr-2 text-sm outline-none transition-colors focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50",
      className,
    )}
    checked={checked}
    {...props}
  >
    <span className="absolute left-2 flex h-3.5 w-3.5 items-center justify-center">
      <DropdownMenuPrimitive.ItemIndicator>
        <Check className="h-4 w-4" />
      </DropdownMenuPrimitive.ItemIndicator>
    </span>
    {children}
  </DropdownMenuPrimitive.CheckboxItem>
))
DropdownMenuCheckboxItem.displayName =
  DropdownMenuPrimitive.CheckboxItem.displayName

const DropdownMenuRadioItem = React.forwardRef<
  React.ElementRef<typeof DropdownMenuPrimitive.RadioItem>,
  React.ComponentPropsWithoutRef<typeof DropdownMenuPrimitive.RadioItem>
>(({ className, children, ...props }, ref) => (
  <DropdownMenuPrimitive.RadioItem
    ref={ref}
    className={cn(
      "relative flex cursor-default select-none items-center rounded-sm py-1.5 pl-8 pr-2 text-sm outline-none transition-colors focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50",
      className,
    )}
    {...props}
  >
    <span className="absolute left-2 flex h-3.5 w-3.5 items-center justify-center">
      <DropdownMenuPrimitive.ItemIndicator>
        <Circle className="h-2 w-2 fill-current" />
      </DropdownMenuPrimitive.ItemIndicator>
    </span>
    {children}
  </DropdownMenuPrimitive.RadioItem>
))
DropdownMenuRadioItem.displayName = DropdownMenuPrimitive.RadioItem.displayName

const DropdownMenuLabel = React.forwardRef<
  React.ElementRef<typeof DropdownMenuPrimitive.Label>,
  React.ComponentPropsWithoutRef<typeof DropdownMenuPrimitive.Label> & {
    inset?: boolean
  }
>(({ className, inset, ...props }, ref) => (
  <DropdownMenuPrimitive.Label
    ref={ref}
    className={cn(
      "px-2 py-1.5 text-sm font-semibold",
      inset && "pl-8",
      className,
    )}
    {...props}
  />
))
DropdownMenuLabel.displayName = DropdownMenuPrimitive.Label.displayName

const DropdownMenuSeparator = React.forwardRef<
  React.ElementRef<typeof DropdownMenuPrimitive.Separator>,
  React.ComponentPropsWithoutRef<typeof DropdownMenuPrimitive.Separator>
>(({ className, ...props }, ref) => (
  <DropdownMenuPrimitive.Separator
    ref={ref}
    className={cn("-mx-1 my-1 h-px bg-muted", className)}
    {...props}
  />
))
DropdownMenuSeparator.displayName = DropdownMenuPrimitive.Separator.displayName

const DropdownMenuShortcut = ({
  className,
  ...props
}: React.HTMLAttributes<HTMLSpanElement>) => {
  return (
    <span
      className={cn("ml-auto text-xs tracking-widest opacity-60", className)}
      {...props}
    />
  )
}
DropdownMenuShortcut.displayName = "DropdownMenuShortcut"

export {
  DropdownMenu,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuCheckboxItem,
  DropdownMenuRadioItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuShortcut,
  DropdownMenuGroup,
  DropdownMenuPortal,
  DropdownMenuSub,
  DropdownMenuSubContent,
  DropdownMenuSubTrigger,
  DropdownMenuRadioGroup,
}

```

```
shadcn/collapsible
"use client"

import * as CollapsiblePrimitive from "@radix-ui/react-collapsible"

const Collapsible = CollapsiblePrimitive.Root

const CollapsibleTrigger = CollapsiblePrimitive.CollapsibleTrigger

const CollapsibleContent = CollapsiblePrimitive.CollapsibleContent

export { Collapsible, CollapsibleTrigger, CollapsibleContent }

```

Install NPM dependencies:

```
lucide-react, @radix-ui/react-slot, class-variance-authority, @radix-ui/react-scroll-area, @radix-ui/react-separator, @radix-ui/react-dialog, @radix-ui/react-label, @radix-ui/react-avatar, @radix-ui/react-icons, @radix-ui/react-navigation-menu, @radix-ui/react-dropdown-menu, @radix-ui/react-collapsible
```
- [x] Replace internal demo login with username-only Admin and Viewer accounts and verify role authorization.
