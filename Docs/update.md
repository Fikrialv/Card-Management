# Card Management & Customer Request System — Implementation Update

This log records actual work only. It is not a duplicate of `todo.md`.

## 2026-09-17 — Queue filter coverage and verification audit

Status: IN PROGRESS — queue behavior strengthened; internal browser/device verification and production environment checks remain open.

Implemented:

- Added feature coverage for the public `/requests` seam: combined CRM search, request type, request-date range, result filtering, and pagination metadata.
- Kept queue scope minimal: existing server-side filters, stable ordering, bounded pagination, and responsive mobile cards; no new abstraction added.
- Recorded the Customer Portal local verification blocker: Vitest cannot load the installed Tailwind Oxide Windows native binding (`stream did not contain valid UTF-8`) and Vite reports `spawn EPERM`; no application assertion failure was observed.

Verification:

- Focused queue tests: **6 passed / 56 assertions**.
- Full Laravel suite: **58 passed / 317 assertions**.
- Pint: passed.
- PHPStan: passed with 0 errors.
- Backend TypeScript check: passed.
- Customer Portal tests: blocked by local native dependency/runtime load error; prior browser evidence remains valid, but this run is not claimed as passing.
- RTK: `0.48.0`, global savings reported at 94%.
- `codex plugin list --json`: `installed: []`; ECC is unavailable and not claimed active.

Remaining:

- Complete internal queue/detail browser review, full translation parity, manual device/orientation/200% zoom/WCAG review.
- Verify MySQL/MariaDB locking, Biznet PHP/database/TLS/cron/backup/restore, and production release boundaries.

Next:

- Resolve Customer Portal dependency/runtime installation in a permitted environment, then run its test/build suite; continue internal request decision E2E review.

## 2026-09-17 — Customer and internal notification inbox

Status: IN PROGRESS — in-app notification slice implemented; Web Push and broader production hardening remain explicitly deferred or blocked.

Implemented:

- Added request-scoped customer notifications to tracking responses. Customer read-state requires the same Request Number + Tracking Code pair and notification ownership is enforced.
- Added customer status-change notifications for review, approval, rejection, and completion transitions.
- Added authenticated internal notification inbox with pagination, unread count, ownership-safe read action, and navigation entry.
- Added responsive notification UI using the existing operational dashboard direction: navy/cobalt shell, calm high-density cards, adaptive mobile layout, visible 44px actions, keyboard focus rings, and no generic marketing pattern.
- Replaced Customer Portal tracking JSON output with a focused status summary, request timeline, notification cards, unread state, and customer read action. Copy remains ID-first with EN parity and follows the existing focused template-led portal layout.
- Removed obsolete `city` and `pic_name` fields from the internal request projection after the active domain removed those customer fields.
- Verified ECC status: no ECC plugin appears as installed and enabled in active `codex plugin list --json`; ECC was not claimed active.

Tests and verification:

- New `NotificationInboxTest`: 4 tests / 30 assertions.
- Full Laravel suite: 51 tests / 286 assertions.
- `vendor/bin/pint --test`: passed.
- PHPStan: passed with 0 errors.
- Internal frontend: `npm run typecheck`, `npm run lint`, and `npm run build` passed.
- Customer Portal: `npm run build` passed.
- Customer Portal tests: 9 tests passed after tracking-notification UI update.
- RTK: version `0.48.0`; all shell commands in this increment used the RTK prefix.

Checklist:

- TODO-022 and TODO-029 moved from `TODO` to `IN_PROGRESS`; not marked `[x]` because rate-limit, browser E2E, full localization, Web Push, and production capability evidence remain.
- TODO-012 and TODO-025 wording updated to reflect implemented notification presentation.

Next:

- Continue with request queue/detail and manual decision workspace, then reports/exports after policy blockers are resolved.

## 2026-09-07

### TODO-001 — Repository discovery

Status: DONE

Implemented:

- Inspected the repository and found only `Docs/plan.md`.
- Confirmed there is no existing application source, package manifest, database, authentication layer, test suite, or deployment configuration to preserve.
- Identified `plan.md` as a master requirements prompt containing the product scope and documentation rules.

Tests:

- Repository file listing and runtime availability checked.
- Node.js `v24.18.0`, npm `12.0.1`, and Git `2.55.0.windows.3` are available.

Decisions:

- Treat the project as greenfield.
- Keep `Docs/plan.md` unchanged as the supplied requirements source.
- Derive actionable work in `Docs/todo.md` and record implementation evidence here.

Blockers:

- Production stack, database provider, auth provider, deployment target, exact business rules, and NOPOL template are not yet confirmed.

Next:

- Establish a zero-dependency executable domain foundation while these product decisions remain explicit.

### TODO-004 — Foundation scaffold

Status: IN_PROGRESS

Implemented:

- Added a minimal Node.js package manifest with test and lint commands.
- Added a deterministic priority-engine domain module and automated tests as the first vertical slice.
- Added project-level README and ignore rules.

Tests:

- Pending after files are created.

Notes:

- This is intentionally not presented as the production web application. It is a runnable domain foundation that avoids inventing an external database/auth integration before the open questions are resolved.

### TODO-015 — Deterministic priority engine

Status: DONE

Implemented:

- Added active request filtering.
- Added priority classification for overdue, due today, due tomorrow, upcoming, and low urgency requests.
- Added deterministic ordering by priority, deadline, submission time, and request ID.

Proposed rule used by the foundation:

- `CRITICAL`: deadline before today.
- `HIGH`: deadline today.
- `MEDIUM_HIGH`: deadline tomorrow.
- `NORMAL`: deadline 2–7 days from today.
- `LOW`: deadline more than 7 days from today.

This remains `PROPOSED` until product confirms timezone, weekends/holidays, and SLA thresholds.

Tests:

- `npm test` — 3 passed, 0 failed.
- `npm run lint` — passed.

Verification notes:

- The default Node test isolation attempted to spawn a child process and failed with Windows `EPERM` in this environment. The test script now uses `--test-isolation=none`, which runs the same tests without the restricted spawn path.
- The tie-break test was corrected so a request two days from the reference date is asserted as `NORMAL`, matching the proposed classification rule.

### TODO-004 — Foundation scaffold update

Status: IN_PROGRESS

Completed in this increment:

- `package.json` with reproducible zero-dependency `test` and `lint` commands.
- `.gitignore` for dependencies, build output, coverage, logs, and environment files.
- `README.md` describing the current boundary and documentation workflow.
- `src/domain/priority.js` and `test/priority.test.js`.

Verification:

- `npm test` passed: 3/3 tests.
- `npm run lint` passed.
- Date parsing was tightened to reject calendar-invalid ISO dates such as `2026-02-30`.

Remaining:

- TypeScript, application framework, database, authentication, CI, and production build remain pending TODO-003 through TODO-008 because the stack and provider decisions are not confirmed.

### Documentation format update

Status: DONE

Changed:

- Converted `Docs/todo.md` from status tables into Markdown checklist items.
- Completed tasks use `[x]`; unfinished tasks use `[ ]` while retaining task metadata and status values.

## 2026-09-07 — UI-only implementation

### TODO-008 — UI foundation

Status: IN_PROGRESS

Implemented:

- Added static UI preview under `ui/` with local/mock state only.
- Added Dashboard Monitoring page with KPI cards, priority queue, operational alerts, request-flow chart, inventory health, and recent activity.
- Added separate Requests, Card Inventory, Customer Portal, NOPOL Applications, and Reports & Audit pages.
- Added responsive navigation, mobile drawer, language-toggle placeholder, local search/filter affordances, forms, toast feedback, and hash-based page switching.
- Added project-level `AGENTS.md` to persist RTK, ECC, Ponytail, Caveman, UI/backend boundary, and documentation workflow rules.
- Added `UI-README.md` with static-server instructions and explicit backend boundary.
- Added persisted UI design system at `design-system/card-operations-console/MASTER.md` using the UI/UX Pro Max design-system search.

Files:

- `ui/index.html`
- `ui/styles.css`
- `ui/app.js`
- `UI-README.md`
- `AGENTS.md`
- `design-system/card-operations-console/MASTER.md`

Tests:

- `npm test` — 3 passed, 0 failed.
- `npm run lint` — passed.
- `npm run ui:check` — passed.
- Static server smoke test returned HTTP 200 for `ui/index.html`, `ui/styles.css`, and `ui/app.js`.

Boundary:

- No database, API, authentication, storage, upload service, PDF service, or backend integration was added.
- Browser automation CLI was unavailable in the environment (`agent-browser: program not found`); verification used JavaScript syntax checks and static HTTP smoke tests.

### ECC / plugin setup

Status: BLOCKED

Attempted:

- `codex plugin marketplace add affaan-m/ECC`
- `codex plugin add ecc@ecc`
- `codex plugin list --json`
- `node scripts/codex/check-plugin-cache.js`
- `npx ecc-universal install --guided codex`
- `git clone https://github.com/affaan-m/ECC.git ECC`
- `npm install` inside `ECC`
- `bash scripts/sync-ecc-to-codex.sh`

Results:

- Active Codex uses account-specific `CODEX_HOME`: `C:\Users\fikri\AppData\Roaming\orca\codex-accounts\70976b2a-d3d6-4e85-aa88-2ef1684c5538\home`.
- `codex plugin add ecc@ecc` returned: `Error: plugin \`ecc\` was not found in marketplace \`ecc\`.`
- `codex plugin list --json` still shows only default `plugin-management` and `deep-research-work`; no ECC marketplace/plugin is registered.
- `npx ecc-universal install --guided codex` hung without output and was stopped after more than one minute.
- Git checkout created only an empty `ECC/.git` with no revision; shell Git/network commands returned no usable refs.
- `npm install` in the empty checkout completed without installing ECC files.
- `node scripts/codex/check-plugin-cache.js` returned `MODULE_NOT_FOUND` because the ECC checkout has no script.
- `bash scripts/sync-ecc-to-codex.sh` failed because Bash/WSL is unavailable: `execvpe(/bin/bash) failed: No such file or directory`.

Decision:

- Keep the project `AGENTS.md` integration rules in place, but do not claim ECC or Ponytail is active globally until `codex plugin list --json` verifies it.
- Retry native ECC install or sync when shell network and a valid Bash/checkout are available.

## 2026-09-07 — Localhost preview fix

### UI preview server

Status: DONE

Problem:

- `http://localhost:4173/ui/` failed because no process was listening on port `4173` after the previous preview server stopped.

Fix:

- Added `npm run ui:serve` to start the static preview consistently from the project root.
- Updated `UI-README.md` with the exact command and reminder to keep the terminal running.

Verification:

- `http://localhost:4173/ui/` returned HTTP 200.
- `http://127.0.0.1:4173/ui/index.html` returned HTTP 200.
- `http://localhost:4173/ui/app.js` returned HTTP 200.

## 2026-09-08 — Agent tooling activation

### TODO-032 — Matt Pocock skills, ECC, and Ponytail

Status: DONE

Implemented:

- Installed all 37 skills from `mattpocock/skills` for Codex in project scope at `.agents/skills`.
- Installed the same 37 skills globally for Codex at `C:\Users\fikri\.agents\skills`.
- Added the ECC marketplace from `affaan-m/ECC` and installed `ecc@ecc` version `2.2.1` into the active account-specific `CODEX_HOME`.
- Added the Ponytail marketplace from `DietrichGebert/ponytail` and installed `ponytail@ponytail` version `4.9.0` into the active account-specific `CODEX_HOME`.
- Added automatic skill-routing rules for Matt Pocock, ECC, and Ponytail to project, canonical global, and active account-specific `AGENTS.md` files.
- Added `skills-lock.json` so the project skill installation is reproducible.

Verification:

- `npx skills@latest list --agent codex --json` reports 37 project skills sourced from `mattpocock/skills`.
- `npx skills@latest list --global --agent codex --json` includes the same 37 globally installed Matt Pocock skills.
- `codex plugin marketplace list --json` reports the `ecc` and `ponytail` Git marketplaces.
- `codex plugin list --json` reports `ecc@ecc` and `ponytail@ponytail` with both `installed: true` and `enabled: true`.
- ECC `node scripts/codex/check-plugin-cache.js` passed; all cached manifest references for skills, MCP servers, and interface assets resolve.
- `codex mcp list --json` reports the enabled `chrome-devtools` MCP server.
- `npm test` passed: 3/3 tests.
- `npm run lint` passed.

Security note:

- The installer reported no Socket alerts for most Matt Pocock skills; `implement-spec` reported one Socket alert. Its aggregate scanners also labelled `code-review`, `claude-handoff`, and `writing-shape` with elevated risk in at least one scanner. Skills run with full agent permissions, so matching skills must still be reviewed before sensitive use.

Activation note:

- The files and plugin registry are active now. A new Codex session or desktop-app restart is required for the current session's generated capability catalog to include newly installed plugin skills and commands.

## 2026-09-08 — Agent tooling expansion

### TODO-033 — no-ai-slop, SkillSpector, and phone-harness

Status: PARTIAL_BLOCKED

Implemented:

- Installed `no-ai-slop` from `petergyang/no-ai-slop` globally at `C:\Users\fikri\.agents\skills\no-ai-slop`.
- Installed `no-ai-slop` for this project at `.agents\skills\no-ai-slop`.
- Installed `skillspector` version `2.11.1` with Python 3.12 through `uv tool install`.
- Installed `skillspector[mcp]` and registered Codex global MCP server `skillspector` as `skillspector mcp`.
- Cloned `https://github.com/NVIDIA/skillspector.git` to `C:\Users\fikri\skillspector`.
- Created `C:\Users\fikri\skillspector\.venv` with Python 3.12 and ran `uv sync --all-extras`, matching the repository `make install-dev` target on Windows where `make` is unavailable.
- Cloned `https://github.com/ShawnPana/phone-harness` to `C:\Users\fikri\.phone-harness` after confirming the target did not exist.
- Read `C:\Users\fikri\.phone-harness\install.md` before installation and read `onboarding.md` for setup flow.
- Added PATH wrappers `C:\Users\fikri\.local\bin\phone-harness.cmd` and `C:\Users\fikri\.local\bin\phone-harness.ps1` so `phone-harness` resolves on this Windows host.
- Registered the `phone-harness` agent skill using `phone-harness skill` output at:
  - `C:\Users\fikri\.agents\skills\phone-harness\SKILL.md`
  - `.agents\skills\phone-harness\SKILL.md`
  - `C:\Users\fikri\AppData\Roaming\orca\codex-accounts\70976b2a-d3d6-4e85-aa88-2ef1684c5538\home\skills\phone-harness\SKILL.md`

Verification:

- `phone-harness --help` works from PATH.
- `phone-harness skill` prints the registered skill body.
- `skillspector --version` reports `SkillSpector v2.11.1`.
- `codex mcp list --json` reports enabled MCP servers `chrome-devtools` and `skillspector`.
- `codex plugin list --json` reports enabled plugins `ecc@ecc`, `ponytail@ponytail`, `plugin-management@openai-curated-remote`, `openai-templates@openai-curated-remote`, and `deep-research-work@openai-curated-remote`.
- Global skill directory contains `no-ai-slop` and `phone-harness`.
- Project skill directory contains `no-ai-slop` and `phone-harness`.

Blockers:

- Full editable `phone-harness` install failed on Windows because its package dependencies include macOS-only PyObjC frameworks. A PATH wrapper was used for the CLI functions that do not need PyObjC.
- `phone-harness --doctor ios` fails on this Windows host with missing PyObjC/Quartz. iPhone control requires macOS with iPhone Mirroring.
- `phone-harness --doctor android` fails because `adb` is not installed or not on PATH. Android onboarding still requires user device setup and USB/Wi-Fi debugging approval.
- Phone onboarding cannot be completed until the user chooses iPhone or Android as the default and performs the required physical phone/permission steps.

## 2026-09-08 — Shareable skill catalog

### TODO-034 — Catalog all installed skills in `skills.md`

Status: DONE

Implemented:

- Created root `skills.md` as a ChatGPT-ready routing catalog.
- Cataloged 437 unique skills: 123 local/system skills and 314 enabled-plugin skills.
- Consolidated duplicate local skill names while retaining every detected scope: Global, Project, Codex canonical, and Codex active.
- Preserved plugin namespaces for ECC, Ponytail, Deep Research, OpenAI Templates, and Plugin Management skills.
- Added a short ChatGPT instruction block explaining that the catalog is a routing reference and does not itself install skills or grant tool access.
- Omitted local absolute paths and account identifiers from the shareable document.

Verification:

- Source-to-catalog comparison: 437 expected names, 437 catalog names, 0 missing, 0 unexpected.
- Catalog duplicate check: 437 entries, 437 unique names, 0 duplicates.
- Plugin groups: ECC 286, Ponytail 6, Deep Research 1, OpenAI Templates 20, Plugin Management 1.
- Confirmed representative entries: `brainstorming`, `no-ai-slop`, `phone-harness`, `ecc:tdd-workflow`, `ecc:security-review`, `ecc:verification-loop`, and all six Ponytail skills.
- Privacy check found no `C:\Users\fikri` path and no active-account identifier in `skills.md`.
- `npm test` passed: 3/3 tests.
- `npm run lint` passed.

Decision:

- The inventory is based on every `SKILL.md` under installed local/system scopes and the `skills/` directory of each currently installed, enabled plugin version.
- Plugin skills are namespaced as `plugin:skill` so the catalog remains unambiguous when pasted into ChatGPT.

## 2026-09-08 — PRD/plan backlog reset and prototype removal

### TODO-001 — Audit requirements and current repository

Status: DONE

Implemented:

- Read `Docs/PRD.md` and `Docs/plan.md` in full and treated them in the declared order: PRD for WHAT/WHY/business rules, plan for architecture and implementation order.
- Replaced the previous `Docs/todo.md` completely with a new 68-task Markdown checklist covering Phase 0 through Phase 10.
- Added priority, dependencies, description, acceptance criteria, test, and explicit status to every task.
- Marked only TODO-001 complete. Kept 64 tasks as `TODO` and 3 policy/input-dependent tasks as `BLOCKED`.
- Recorded the next executable task as TODO-002, architecture and provider decisions.
- Updated `README.md` to describe the repository as a planning workspace with no production application yet.
- Removed obsolete prototype artifacts at the user's request:
  - `UI-README.md`
  - `ui/index.html`
  - `ui/styles.css`
  - `ui/app.js`
  - `design-system/card-operations-console/MASTER.md`
  - `src/domain/priority.js`
  - `test/priority.test.js`
  - `package.json`
  - `package-lock.json`
- Preserved the following non-prototype source references under `Data/` without reading, modifying, or treating their contents as approved product rules:
  - `Data/Template RFID 2025.xlsx`
  - `Data/UPDATE KARTU RFID.xlsx`
  - `Data/WhatsApp Image 2026-09-07 at 11.29.24.jpeg`

Requirements and decisions:

- The target remains two separately deployed frontends with shared backend services and one PostgreSQL database.
- The new backlog covers customer requests, conditional multi-item validation, secure evidence, tracking, approval and priority, RFID range inventory, Stock In/Out, stock requests, NOPOL/PDF, monitoring, reports, notifications, audit, settings, ID/EN, responsive behavior, WCAG 2.2 AA, security, operations, and final verification.
- Existing static UI and JavaScript priority behavior were not accepted as production work. The priority prototype also used levels that did not match the PRD's final Critical/High/Medium/Normal vocabulary and omitted official urgency/request-type policy.
- TODO-024 is blocked pending the optional revision/resubmission policy.
- TODO-042 is blocked because no retained repository asset is identified and approved as the NOPOL template; fields, numbering, signature, and locale requirements also remain unconfirmed.
- TODO-050 is blocked pending report-export formats, fields, limits, and permissions.
- Files under `Data/` remain read-only references until their purpose, privacy classification, field mapping, and any migration/import scope are approved.
- No backend, API, database, authentication, storage, or PDF integration was implemented in this documentation increment.

Verification:

- Checklist structure: 68 tasks, 68 dependency fields, 68 descriptions, 68 acceptance-criteria fields, 68 test fields, and 68 status fields.
- Status consistency: 1 checked/DONE, 64 unchecked/TODO, and 3 unchecked/BLOCKED.
- Task IDs are unique and sequential from TODO-001 through TODO-068.
- All 9 obsolete prototype/package files were confirmed absent after deletion.
- No live documentation outside this historical audit references the deleted prototype paths or commands.
- Requirements keyword review confirmed coverage for Customer Request, Tracking, Priority, RFID, Stock Request, NOPOL/PDF, Dashboard, Reports, Notifications, Audit, RBAC, i18n, WCAG, Security, and E2E.
- Build, typecheck, lint, and application tests are not applicable after removal of the prototype package; production quality gates are scheduled in TODO-003 and TODO-005.
- Git diff verification is unavailable because the workspace is not a Git repository.

Recovery note:

- The removed files cannot be restored through Git because the workspace has no `.git` repository. Their former existence and deletion rationale remain recorded in this audit log.

Next:

- Execute TODO-002 — record architecture and provider decisions before scaffolding the production monorepo.

## 2026-09-08 — Laravel/MySQL architecture documentation revision

### Documentation architecture reset

Status: DONE

Files revised:

- `Docs/PRD.md`
- `Docs/plan.md`
- `Docs/todo.md`
- `Docs/update.md`

Decisions recorded:

- Backend and shared domain ownership use Laravel/Eloquent.
- Internal Dashboard uses Laravel + Inertia.js + React + TypeScript.
- Customer Portal is a standalone React + TypeScript deployment connected only through allowlisted Laravel REST API routes.
- Both frontends use Tailwind CSS + shadcn/ui, deploy on separate domains, and share one Laravel backend plus one MySQL database.
- Target database is MySQL 8 through Biznet/cPanel/phpMyAdmin. MariaDB-compatible operation is allowed when hosting lacks MySQL 8, without changing domain architecture. Laravel migrations remain schema authority.
- PostgreSQL, Prisma, and Next.js were removed from active core-stack tasks.
- Bahasa Indonesia is default with ID/EN toggle. UI, forms, validation, errors, statuses, notifications, tracking, loading/empty states, and applicable PDFs must use translation keys.
- UI is mobile-first across phone, tablet, laptop, and desktop. Tables require adaptive small-screen treatment beyond horizontal overflow. Viewport, touch, keyboard, representative screen-reader, and WCAG 2.2 AA gates are required.
- Customer submissions support multiple vehicles/items, enter the shared Laravel backend directly, and are tracked with Request Number + Tracking Code.
- Approval ranking remains server-side and deterministic: Overdue, Due Today, Urgency, nearest deadline, approved request-type policy, then created time. Ranking never auto-approves; manual overrides require reason and audit.
- Inventory flow is Approved Request, Assign RFID, Stock Out, Update Inventory, Processing, Completed. Laravel transactions, InnoDB row locks, constraints, deterministic lock order, and bounded deadlock retry prevent double assignment.
- NOPOL remains a document-generation flow, not approval. Published template versions and generated PDFs are immutable.

Data-source inspection evidence:

- `Data/Template RFID 2025.xlsx`: sheet ` Pengajuan Perubahan DataRFID  `, range B2:S37, 36 populated-range rows, 257 non-empty cells, and no formulas. Headers cover institution/city, CRM, area, RFID/unique number, NOPOL, vehicle/user, BBK, quota/unit/period, balance mutation, destination, activation, and change type.
- `Data/UPDATE KARTU RFID.xlsx`: sheets `2025` (A1:K128; 246 formulas) and `2026` (A1:L165; 314 formulas). Headers cover date, description, CRM, inbound/outbound ranges, cards in/out, and ending stock. Source typos are mapping concerns, not canonical domain names.
- `Data/template pengajuan.jpeg`: visual review found a request for additional RFID cards with quantity, shipping address, contact person, and PIC. It appears suitable as a Stock Request reference; it is not yet approved as a NOPOL template.
- Sources remained read-only. No customer/CRM sample value or other source PII was copied into documentation.
- Schema/import/template work now depends on a formal source-to-domain mapping.

Checklist changes:

- Replaced TODO-002, TODO-003, and TODO-006 with Laravel/Inertia/React, standalone portal, MySQL/MariaDB, and Biznet/cPanel work.
- Updated related configuration, auth/RBAC, contracts, customer API, storage, inventory locking, stock request, NOPOL, localization, responsive/accessibility, security, deployment, testing, and verification tasks.
- Added TODO-008A for immutable source inspection/mapping before final schema/import/template work.
- Changed TODO-042 from `BLOCKED` to `TODO`: source asset now exists and has been inspected, while suitability and product details remain work for the task.
- Kept only TODO-024 and TODO-050 blocked because their policies remain undefined.
- Kept TODO-001 as the only checked/DONE task; no new implementation task was claimed complete.

Verification:

- Checklist structure: 69 tasks; every task has dependencies, description, acceptance criteria, test, and status.
- Status consistency: 1 checked/DONE, 66 unchecked/TODO, 2 unchecked/BLOCKED; blocked IDs are TODO-024 and TODO-050.
- Active `Docs/todo.md` contains no Next.js, PostgreSQL, or Prisma references. In PRD/plan those names occur only in explicit exclusion statements, never as selected stack.
- Data SHA-256 values before and after documentation edits are identical:
  - `Template RFID 2025.xlsx`: `bc980355e78422889f535121778c8311168d1dec6b617504fce4be4b8021775b`
  - `UPDATE KARTU RFID.xlsx`: `eb31f93e9ca738d6364a257de3b244506d64db77e2a4cadc188e47f832a59e3c`
  - `template pengajuan.jpeg`: `18a51a8262f0df9c6df1476c5ac585a193cf426bd84caaf3479f7217deb60ada`
- Documentation-only scope honored. No Laravel app, frontend, database, API, migration, or backend integration was implemented.
- Application build/tests were not applicable because production scaffolding has not started.
- Git diff remains unavailable because workspace is not a Git repository.

Remaining blockers:

- TODO-024: revision/resubmission policy.
- TODO-050: report export formats, fields, limits, localization, retention, and permissions.

Open decisions handled by executable tasks rather than blocked status:

- TODO-002: actual Biznet/cPanel capabilities, production timezone, domains, storage, cron/queue, and backup constraints.
- TODO-008A: historical import scope and final source-field mapping.
- TODO-042: NOPOL layout suitability, numbering, signature, fields, and locale rules.
- Low-stock threshold and optional request-type weighting remain unset until policy approval.

Next:

- Execute TODO-002 — Record Laravel, MySQL, and Biznet architecture decisions.
- Then execute TODO-008A before schema, import, or template implementation.

## 2026-09-11 - Phase 0-5 implementation increment

Status: IN PROGRESS

### Configuration verified

- Active Codex account configuration parses successfully with `gpt-5.6-terra`, high reasoning effort, low verbosity, ChatGPT forced login, keyring credential storage, 8,000 tool-output limit, 200,000 auto-compact limit, and two enabled subagent threads.
- `codex doctor` confirmed active account `CODEX_HOME` is healthy and network/WebSocket checks pass. No configuration file change was needed because requested values were already installed.

### Implemented

- Added documented local/CI verification entry point, portal public-environment validation, GitHub CI workflow, and separate Vitest configuration. Unit tests run only `src/` tests in `jsdom`; Playwright E2E remains under `e2e/`.
- Corrected PHPStan model/controller types and authenticated-user access. Static analysis now reports zero errors.
- Made internal Inertia mutations return a `303` redirect while preserving JSON responses for REST/API callers.
- Added request workflow rules: `NEW` must enter review before decision; decision retries are idempotent; priority overrides can be removed only with reason and audit record; calculated priorities order correctly when deadlines match; completion requires every request item have RFID assignment.
- Hardened Stock Request workflow: only draft owner or admin can submit; shipping requires AWB/reference; received RFID range quantity must equal requested quantity; failed validation creates no inventory movement.
- Added admin-managed low-stock threshold, daily per-operator notification deduplication, settings audit events, and post-assignment low-stock evaluation.
- Updated internal Request detail, Inventory, and Stock Request screens for status-led actions, assignment visibility, Stock In correction, threshold editing, and responsive card treatment. Portal remains standalone with configured public API base URL.

### Errors fixed

- Portal Vitest initially loaded Playwright specs; dedicated `vitest.config.ts` restricts unit discovery to `src/`.
- Portal component tests then lacked DOM; `jsdom` and shared testing-library setup are explicit.
- PHPStan found two invalid nullsafe accesses on authenticated `User`; changed to direct access.
- Email verification test exposed missing Laravel `MustVerifyEmail` contract/trait on `User`; restored both.
- MariaDB was unavailable during one test attempt (`connection refused`); service availability was restored before isolated real-database suite.

### Verification evidence

- Customer Portal: `npm test` 9 passed; `npm run lint`, `npm run typecheck`, and `npm run build` passed.
- Customer Portal responsive/accessibility smoke: Playwright 4 passed (phone, tablet, desktop, keyboard navigation).
- Laravel: Pint passed; PHPStan passed with zero errors; `php artisan test --compact` passed 42 tests / 215 assertions.
- Real MariaDB: `vendor\\bin\\phpunit --configuration phpunit.mysql.xml --testdox` passed 13 tests / 124 assertions, including approval, inventory locking/range, low-stock, and Stock Request protections.
- Internal Dashboard: `npm run lint`, `npm run typecheck`, and `npm run build` passed.
- Immutable source check passed. SHA-256 values remain:
  - `Template RFID 2025.xlsx`: `bc980355e78422889f535121778c8311168d1dec6b617504fce4be4b8021775b`
  - `UPDATE KARTU RFID.xlsx`: `eb31f93e9ca738d6364a257de3b244506d64db77e2a4cadc188e47f832a59e3c`
  - `template pengajuan.jpeg`: `18a51a8262f0df9c6df1476c5ac585a193cf426bd84caaf3479f7217deb60ada`

### Checklist discipline

- No new TODO item is checked in this increment. Several implemented areas still require complete acceptance evidence: production Biznet/cPanel confirmation, exhaustive configuration/secret tests, translation-key parity, exhaustive state/validation matrices, queue notification/read-state coverage, large-data pagination, concurrent-load testing, and end-to-end API journey coverage.
- TODO-024 and TODO-050 remain only BLOCKED tasks. `Data/` remains unchanged.

Next:

- Complete TODO-002 provider/hosting decision record, then close TODO-008A field-coverage/ambiguity/import-scope review before marking dependent checklist items DONE.

## 2026-09-13 - TODO-002 architecture decision increment

Status: IN PROGRESS

### Recorded decisions

- Expanded ADR-0001 through ADR-0004 into decision records with context, alternatives, consequences, security boundaries, and operational implications.
- Added ADR-0005 for two independent release artifacts: Laravel/Internal Dashboard at configured internal origin and static Customer Portal at configured public origin. Laravel remains sole API and database owner.
- Confirmed deployment contract: build frontend artifacts before upload; do not require production Node process; baseline queue remains `sync`; scheduler/worker activation requires verified cPanel capability.
- Set server timestamps to UTC and business date/default presentation to `Asia/Jakarta`; Indonesian remains default UI locale with English support through keys.
- Recorded cPanel safety controls: explicit CORS allowlist, session/CSRF only on internal origin, private Laravel storage, migrations as schema authority, and phpMyAdmin as operating/inspection tool only.

### Provider evidence and constraints

- Official Biznet sources confirm PHP Selector, MySQL databases, subdomains, SSL, and plan-specific backup. Node.js is advertised only for Large/Extra plans.
- Official Biznet Laravel guidance confirms cPanel deployment, MySQL configuration, and domain/subdomain routing. Separate shared-hosting guidance warns terminal/Composer access is not guaranteed.
- Updated `Docs/hosting-capability.md` with these constraints and exact account-level release checks.

### Verification

- Reviewed five ADRs, hosting capability baseline, and TODO-002 dependency/status. No application source or `Data/` file changed.
- TODO-002 changed from `TODO` to `IN_PROGRESS`; it is not checked because selected-account PHP/database/version, SSH/Composer, cron/worker, limits, domains/TLS, backup/restore, and remote-DB evidence are still required before production.

Next:

- Redesign the Internal Dashboard Monitoring page as a local-state UI slice, then run frontend typecheck/build and record evidence.

## 2026-09-13 - Monitoring Dashboard UI redesign increment

Status: IN PROGRESS

### Design decision

- Monitoring is an internal B2B operations page: calm cobalt accent, light slate surfaces, high information density, semantic alert colors, consistent soft radii, and no decorative gradients or motion.
- UI remains local example data. Every example metric/queue/activity string is explicitly marked as design data; no Laravel metric query, API, or database integration was added.
- Responsive behavior uses stacked summary tiles and queue rows on small screens, with direct drill-down links to existing Request, Inventory, and Stock Request screens.

### Implemented

- Replaced placeholder Dashboard with Monitoring layout: summary, priority queue, attention alert, recent activity, and operational shortcuts.
- Activated authenticated `/dashboard` as an Inertia `Dashboard` page and corrected Monitoring navigation to that route.
- Added authenticated route feature test.
- Updated `.21st/design.json` with product tokens and durable Monitoring visual decision. 21st component search could not run because the `21st` executable/package is unavailable in this environment; existing project primitives and installed Lucide icons were reused.

### Verification

- Red-green route test: initial `/dashboard` redirect failed as expected; after implementation `DashboardTest` passed with 10 assertions.
- `npm run lint`, `npm run typecheck`, and `npm run build` passed for Internal Dashboard.
- Pint initially reported only `routes/web.php` import ordering; formatted with Pint, then `pint --test` passed.
- PHPStan passed with zero errors.
- Full Laravel suite passed: 43 tests / 225 assertions.

### Checklist discipline

- TODO-048 remains unchecked. Its required server metrics, reconciliation, RBAC projections, freshness/error states, and dependencies TODO-030/TODO-036/TODO-041 are not implemented.
- `Data/` remains unchanged.

Next:

- Complete TODO-008A field-coverage, ambiguity, and import/reference scope review, or continue the next approved local UI slice without claiming backend integration.

## 2026-09-13 - Audit Activity UI slice

Status: IN PROGRESS

### Implemented

- Added authenticated `/audit` route and `Audit/Index` Inertia page.
- Added Activity navigation to the internal sidebar.
- Built local-state Audit Activity UI: semantic category filters, search, responsive event rows, zero-result state, and detail dialog.
- The page uses explicit local example events only. It does not query `AuditLog`, expose audit metadata, add API routes, or change database behavior.

### Accessibility and quality review

- Applied Web Interface Guidelines review to the new page: search input now has an accessible label, `name`, autocomplete policy, visible keyboard focus, decorative-icon hiding, semantic filter grouping, and modal overscroll containment.
- Initial route test failed with expected `404`. After route/page implementation it failed once with `500` because current Vite manifest lacked the new page entry. `npm run build` regenerated the manifest; this was an asset-build state issue, not a route or API issue.

### Verification

- `AuditPageTest` passed: 10 assertions.
- `npm run lint`, `npm run typecheck`, and `npm run build` passed.
- Pint passed.
- PHPStan passed with zero errors.
- Full Laravel suite passed: 44 tests / 235 assertions.

### Checklist discipline

- TODO-052 remains unchecked. It requires centralized server-side event coverage, redaction, authorization, filtering, pagination, and audit integration tests.
- `Data/` remains unchanged.

Next:

- Run visual browser review for `/audit` under authenticated session, then continue TODO-008A or another approved local UI slice. Do not integrate mock Audit Activity data with backend until TODO-051/TODO-052 dependencies are complete.

## 2026-09-13 - Document History UI slice

Status: IN PROGRESS

### Implemented

- Added the authenticated `/documents` route and updated the internal sidebar Document navigation.
- Added a mobile-first `Documents/Index` Inertia shell for the NOPOL document flow: template selection, data entry, preview, PDF generation, download/history, and versioning expectations.
- Kept the screen deliberately non-operational: no template creation, request data lookup, PDF generation, download, document records, API calls, or database changes were added.
- Made the unresolved state explicit: template layout, numbering, signature, and language rules remain pending TODO-042 before generation can be enabled.

### Verification

- Red-green route test: initial `/documents` request returned expected `404`; after implementation `DocumentsPageTest` passed with 10 assertions.
- First asset build did not expose the new page to the active Vite manifest; a repeated build confirmed the entry, after which the route test passed. This was an asset-build state issue, not a route or API failure.
- `npm run lint` and `npm run typecheck` passed.
- Pint passed.
- PHPStan passed with zero errors.
- Full Laravel suite passed: 45 tests / 245 assertions.

### Checklist discipline

- TODO-043 through TODO-046 and TODO-053 remain unchecked: they require the still-pending template decision plus real document, versioning, access-control, storage, and history behavior.
- `Data/` remains unchanged.

### Local runtime verification

- Laravel development server started successfully at `http://127.0.0.1:8000`; `GET /login` returned HTTP 200 on 2026-09-14. The server is local-only and has no deployment or backend integration implication.
- The local SQLite migration history is inconsistent: `2026_09_08_000000_create_card_management_tables` is marked as run, but its domain tables (for example `customers` and `user_notifications`) are absent. `db:seed` and the pending 2026-09-11 migration consequently fail. No reset or destructive repair was performed.
- Created only the three minimal local UI demo accounts (`admin@example.test`, `reviewer@example.test`, and `viewer@example.test`) so authenticated Monitoring, Activity, and Document shell screens can be reviewed. Their password is the development-only `password` value. Transactional requests, inventory, and stock flows remain unavailable until the local migration state is repaired.

Next:

- Run an authenticated visual browser review of `/documents`, or continue TODO-008A before further document implementation. Do not connect this shell to Laravel data or PDF generation until its dependencies are complete.

## 2026-09-14 - Demo runtime, database recovery, and login UI increment

Status: IN PROGRESS

### Database recovery and demo data

- Diagnosed the local SQLite state: the migration history marked `2026_09_08_000000_create_card_management_tables` as complete while its domain tables were absent. The pending migration therefore failed on `user_notifications`, and the standard seeder failed on `customers`.
- Preserved the inconsistent local database before recovery at `backend/database/database.pre-demo-20260914-0936.sqlite` (SHA-256 `c08d057b4af5a76770607cb2a1e5395c52047e36f2391d13ac72910c666c3c89`).
- With explicit demo-database authorization, ran `php artisan migrate:fresh --seed --force`. All five migrations and the deterministic seeder completed successfully.
- The rebuilt local demo database contains three role accounts, seven seeded Customer Requests, three RFID Cards, one Document Template, and one Audit Log. A portal-compatible synthetic request was also submitted and tracked successfully for the demo.

### UI/UX

- Redesigned the shared guest/authentication surface and Login page for the Indonesian-first internal demo: clear app identity, mobile-safe viewport height, visible heading hierarchy, high-contrast cobalt action, 44px session control, accessible inline status, and preserved password-manager autocomplete values.
- Added a component test for the Indonesian login form and configured Vitest's `@` alias. The test was initially placed below `Pages/`; production build showed it being captured by the Inertia glob as a 465 kB browser asset. It was moved to `resources/js/test/`, eliminating the test asset from the production build.
- Applied Taste Skill only to the public/auth surface. It explicitly excludes dense dashboards, so dashboard UI remains on the existing product-UI pattern. Applied UI/UX Pro Max accessibility guidance for password-manager support and visible focus.

### Runtime and verification

- Internal Laravel demo remains available at `http://127.0.0.1:8000`; Customer Portal development server is available separately at `http://localhost:5174`.
- Browser-equivalent session check: admin login returned `302`; normal authenticated navigation returned `200` for `/dashboard`, `/requests`, `/inventory`, `/stock-requests`, `/audit`, `/documents`, and `/profile`.
- Customer Portal: `npm test -- --run` passed (9 tests), `npm run build` passed, and `GET /` returned `200`.
- Public boundary: Customer Portal CORS preflight returned `204` with only the configured origin/method/headers. Multipart request submission returned `201`; tracking the returned credentials returned `200` with status `NEW` and a safe projection.
- Internal UI: `npm run test:ui` passed (1 test), `npm run lint`, `npm run typecheck`, and `npm run build` passed. Pint and PHPStan passed. Full Laravel suite passed: 45 tests / 245 assertions.
- `Data/` was not changed; source hashes remain `bc980355e78422889f535121778c8311168d1dec6b617504fce4be4b8021775b`, `eb31f93e9ca738d6364a257de3b244506d64db77e2a4cadc188e47f832a59e3c`, and `18a51a8262f0df9c6df1476c5ac585a193cf426bd84caaf3479f7217deb60ada`.

### Checklist discipline

- No TODO item was checked. TODO-003 through TODO-006 and feature items remain open because clean-install, production MySQL/MariaDB, hosting, localization, visual-device, and acceptance gates are not complete.

Next:

- Conduct a real-browser responsive review at phone, tablet, laptop, and desktop viewports for both origins, then continue TODO-008A source mapping before extending production document behavior.

## 2026-09-14 — Scope rebaseline: customer submission, procurement, reports, notifications, and UI direction

Status: DOCUMENTATION COMPLETE — no application implementation started

### Requirement decisions recorded

- Replaced the seven-type customer request scope with four explicit choices: Kartu baru, Kartu rusak, Kartu error system, and Kartu hilang. Aktivasi kartu, perubahan data, and mutasi saldo are removed from active scope.
- Replaced the customer portal’s multi-kendaraan/item form with a template-led flow: customer downloads the approved version of `Data/Template RFID 2025.xlsx`, then uploads Pengajuan Kartu and Bukti Bayar as separate private artefacts.
- Renamed “Tanggal dibutuhkan” to “Tanggal Permintaan”; it is not a customer-controlled priority deadline. Priority deadline/SLA must be derived by server policy once that policy is supplied.
- Defined CRM validation target as one to thirteen alphanumeric characters, with no whitespace or symbols.
- Replaced Stock Request and shipping lifecycle with Admin-owned Catatan Pengadaan. Its requested and received data are distinct; receipt must atomically create Stock In, update inventory, write audit/history, and notify without double-counting.
- Removed NOPOL Documents/template/PDF flow from the active MVP. Existing Documents UI is explicitly legacy/non-production and has a removal task; it was not deleted in this documentation-only increment.
- Defined reports as Customer Request Detail plus Summary derived from request detail and Stock In/Stock Out. Excel and PDF exports are required, subject to remaining authorization/retention/row-limit policy.
- Defined notification baseline as an authorized in-app inbox for internal users and tracking-credential-bound customer notifications. Web Push is an opt-in additional channel requiring HTTPS/VAPID/capability verification; it is not the only notification path.

### UI/UX direction recorded

- Added `Docs/uiux-direction.md` from the supplied visual references. Internal UI is a calm, high-density operational dashboard with adaptive data views; Customer Portal is focused on download, two uploads, submission, tracking, and safe notifications.
- Required responsive and accessibility matrix covers 320/375/768/1024/1440 px, orientation, touch, keyboard, 200% zoom, ID/EN, and WCAG 2.2 AA. Tables require an adaptive treatment rather than horizontal overflow alone.
- Conversation images are treated as visual inspiration only; no image asset, layout, or copy was copied into the project.

### Files revised

- Replaced `Docs/PRD.md` with the current requirements, canonical glossary, active/deferred scope, integrity rules, report/export contract, and notification boundary.
- Replaced `Docs/plan.md` with dependency-aware implementation phases and verification journeys for the updated scope.
- Replaced `Docs/todo.md` with 47 executable Markdown checklist tasks. Only TODO-001 and documentation-only TODO-001A are checked; every application task remains unchecked pending its acceptance/test evidence.
- Added `Docs/uiux-direction.md` for durable UI decision and device/accessibility acceptance criteria.

### Verification

- Read-only SHA-256 check confirms `Data/` is unchanged:
  - `Template RFID 2025.xlsx`: `bc980355e78422889f535121778c8311168d1dec6b617504fce4be4b8021775b`
  - `UPDATE KARTU RFID.xlsx`: `eb31f93e9ca738d6364a257de3b244506d64db77e2a4cadc188e47f832a59e3c`
  - `template pengajuan.jpeg`: `18a51a8262f0df9c6df1476c5ac585a193cf426bd84caaf3479f7217deb60ada`
- Checklist static review: 47 task entries, 2 checked documentation/audit entries, and explicit tasks for Kartu error system, Catatan Pengadaan receipt, and Excel/PDF export.
- Final read-only documentation contract check passed: four request types, removed request types, CRM rule, procurement replacement, Excel/PDF reports, notification channel, UI direction link, audit entry, and checklist terminology were all found.
- No Laravel, React, migration, API, database, source `Data/`, runtime, or deployment file changed in this increment.

### Remaining blockers

- Confirm whether Kota, Nama PIC, Nomor Telepon, and Email are retained, optional, or removed from Customer Portal.
- Define file formats, maximum sizes, and requiredness for Pengajuan Kartu and Bukti Bayar.
- Define server-side SLA/deadline policy used by overdue/due-today priority ordering.
- Define the authoritative unique RFID range/list source captured at Catatan Pengadaan receipt. Quantity alone cannot safely create assignable RFID cards.
- Define report export role permissions, retention, and row limits; define revision/resubmission policy; verify Web Push channel/hosting capability.

Next:

- Execute TODO-008A mapping finalization, then resolve TODO-008B product policies before any schema or public upload/download implementation.

## 2026-09-14 — Customer request and procurement demo slice

Status: IN PROGRESS — UI/UX and database/API slice implemented; production hardening and report/export work remain open.

### Decisions applied

- Customer Portal retains only institution name, CRM, request type, and Tanggal Permintaan. CRM is 1–13 alphanumeric characters. The active types are Kartu baru, Kartu rusak, Kartu error system, and Kartu hilang.
- The customer flow is template-led: required Pengajuan Kartu `.xlsx` (maximum 3 MB) plus required Bukti Bayar (`pdf`, `jpg`, `jpeg`, or `png`, maximum 5 MB). Kota, PIC customer, telephone, email, vehicle/item fields, activation, data change, and balance mutation are rejected by the public contract.
- Server-derived SLA defaults are configured as 3 days (new card), 2 days (damaged), and 1 day (system error/lost), calculated from request date. Queue ranking remains review order only.
- Catatan Pengadaan is Admin-only. A receipt must give an inclusive RFID start/end range whose derived quantity exactly equals QTY diterima; this is the selected safe source for creating unique cards. Receipt is one-time and atomically records Stock In, cards, movement, audit, and internal notification.
- Reports are admin-only in product scope. Resubmission is not in the MVP; a rejected customer submits a new request. In-app notifications are the current baseline; Web Push remains dependent on HTTPS/VAPID/Biznet capability.

### Implemented

- Added migration for `request_date`, typed request evidence, `procurement_notes`, procurement-to-range/movement source links, and customer notification records. Existing historical Stock Request tables were preserved to avoid destructive migration.
- Replaced the public request validation/action contract, including typed private evidence, safe tracking credentials, idempotency, initial customer/internal notifications, and SLA-derived deadline.
- Added public `GET /api/public/v1/request-template`, which downloads the immutable source template without modifying it.
- Added Admin Catatan Pengadaan create/list/receipt endpoints and responsive Inertia workspace. Navigation now labels the feature Catatan Pengadaan and no longer exposes Documents/NOPOL.
- Rebuilt the Customer Portal around the supplied customer UI direction: focused ID/EN submit/track journey, four request types, download CTA, separate file controls, inline server errors, touch-sized controls, and reduced-motion support. The admin UI follows the supplied operational-dashboard direction with adaptive cards instead of an overflow-only table.
- Updated `Docs/data-mapping.md` and `test/CONTEXT.md` to use canonical active domain language.

### Verification and repairs

- Initial red tests correctly exposed missing public contract and procurement routes. After implementation, focused tests passed: **7 tests / 41 assertions**.
- Full Laravel suite initially exposed obsolete customer factory fields after the migration removed them. Factory and non-production seeder were rebaselined, then full suite passed: **47 tests / 256 assertions** (`php artisan test --compact`).
- Customer Portal tests passed: **9 tests** (`npm test -- --run`); Customer Portal production build passed (`npm run build`).
- Internal Dashboard production build passed (`npm run build`).
- Pint initially identified import/braces formatting in two changed PHP files; after formatting, `vendor/bin/pint --test` passed and the focused Laravel contract suite remained green (7 tests / 41 assertions).
- Immutable source hash check passed unchanged:
  - `Template RFID 2025.xlsx`: `bc980355e78422889f535121778c8311168d1dec6b617504fce4be4b8021775b`
  - `UPDATE KARTU RFID.xlsx`: `eb31f93e9ca738d6364a257de3b244506d64db77e2a4cadc188e47f832a59e3c`
  - `template pengajuan.jpeg`: `18a51a8262f0df9c6df1476c5ac585a193cf426bd84caaf3479f7217deb60ada`

### Checklist status

- No implementation task was marked `[x]`; each affected task is `IN_PROGRESS` because its remaining acceptance evidence is not yet complete.
- TODO-035 is no longer `BLOCKED`: the receipt range policy is decided and implemented. TODO-023 remains blocked by Web Push hosting capability, TODO-024 by the intentionally deferred resubmission policy, and TODO-040 by export retention/row-limit policy.

### Remaining next work

- Add report detail/summary data model, Admin report UI, and admin-only Excel/PDF exports after deciding retention and row limits.
- Complete private-upload hardening (file signatures/malware strategy/cleanup), template hash/version response tests, real MySQL/MariaDB locking tests, and actual device/WCAG review.
- Add visible customer/admin notification inboxes and defer Web Push implementation until Biznet HTTPS/VAPID capability is proven.
# 2026-09-17 — Final PM policy alignment

- Removed automatic request-type SLA/deadline calculation; active queue now orders manual priority, oldest `request_date`, then oldest `created_at`.
- Removed active Documents/NOPOL and Stock Request routes; legacy tables/classes remain only for migration compatibility.
- Removed active RequestItem dependency from RFID assignment/completion; assignment is request-level and emits customer `PROCESSING` notification.
- Restricted private evidence download to Admin/Reviewer and aligned report policy to Admin-only, Excel 5,000 rows, PDF 1,000 rows, seven-day private retention.
- Tests were updated for the final workflow. ECC remains unavailable in the active plugin list.

# 2026-09-17 — MVP queue, reports, and hardening increment

### Implemented

- Added request target H+1, `Utamakan`, stable queue ordering, target labels, daily due/late in-app notifications, per-user deduplication, and audit coverage.
- Removed legacy StockRequest production action/controller/request/model code and active Documents/NOPOL pages/routes; legacy database migrations remain untouched for compatibility.
- Added Admin-only detail/summary Reports with request-date filters, Excel/PDF row limits, private seven-day export metadata, audit, expiry-aware download, and scheduled cleanup.
- Hardened uploads with signature checks and transaction-failure cleanup; request-level RFID assignment remains independent of RequestItem and PROCESSING notification is covered.
- Added report and target-notification feature tests; updated dashboard wording to simple operational labels.

### Verification

- Laravel: **51 tests / 247 assertions passed**.
- Pint: passed.
- PHPStan: passed with no errors.
- Internal frontend: typecheck, lint, and production build passed.
- Customer Portal: **9 tests passed** and production build passed.
- `migrate:status`: all migrations, including the two new MVP migrations, are applied.
- `schedule:list`: target notification at 08:00 and export cleanup at 02:00 are registered.
- `Data/` was not modified.
- Dashboard KPI/queue and Activity page now read live database/AuditLog data; static placeholder metrics/events were removed.

### Remaining blockers

- Malware scanning cannot be claimed until Biznet capability/tooling is verified; current baseline is allowlist, MIME/signature, private non-executable storage, and cleanup.
- MySQL/MariaDB concurrency, XAMPP, and Biznet production/TLS/cron/backup/restore verification are environment-dependent; current automated suite uses the configured test database.
- Full WCAG/device/browser verification and complete dashboard metric reconciliation remain; Web Push and revision/resubmit remain deferred by policy.

### Follow-up implementation

- Dashboard metrics, live queue, stock summary, notification count, and recent activity now use database sources; Audit Activity uses paginated immutable AuditLog with safe actor/entity metadata and authorization.
- Public template download now exposes version `2025` and the verified SHA-256 hash; public API remains rate-limited.
- Viewer request detail receives evidence metadata without a download URL; Admin/Reviewer retain authorized download access.
- Request and report desktop tables now have mobile card views so core actions do not require horizontal scrolling.
- Composer and npm security audits report no known high-severity vulnerabilities.
- Reports now include localized ID/EN column/title labels; request and report tables provide mobile card views while desktop retains dense tables.
- Customer Portal E2E expectation was aligned with the final MVP: template download replaces the removed vehicle/item action.

### Final verification follow-up

- Re-ran Laravel verification after the security-header assertion and evidence-viewer UI fix: **51 tests / 254 assertions passed**.
- Pint, PHPStan, internal typecheck/lint/build, and Customer Portal tests (**9 passed**) plus build passed.
- Viewer request detail now renders evidence as metadata-only when no authorized download URL is returned; Admin/Reviewer retain the download action.
- Migration status is fully applied; scheduled due/late notification and export cleanup jobs remain registered.
- Customer Portal Playwright discovery reports the configured browser cases as skipped/non-runnable in this environment; this is not claimed as a passing browser E2E result.
- Root-level `npm run build` is not applicable because the runnable frontend manifests are under `backend/` and `customer-portal/`.

## 2026-09-17 — Internal locale toggle hardening

Implemented:

- Added authenticated `PATCH /locale` endpoint with strict `id|en` validation.
- Internal ID/EN control now persists the selected locale on the authenticated user instead of being a placeholder.
- Added regression coverage for locale persistence.

Verification:

- Laravel: **52 tests / 256 assertions passed**.
- Pint, PHPStan, internal typecheck/lint/build passed.
- Customer Portal: **9 tests passed**, typecheck/lint/build passed.
- Internal navigation no longer has a dead `#settings` placeholder; “Pengaturan” now opens the existing `/profile` page and has active-state handling.
- Request queue target badges now show the operator labels “Selesai”, “Sesuai target”, “Harus selesai hari ini”, or “Terlambat” without exposing raw enum codes in the primary UI.
- Fixed empty `prioritized` query handling so the default queue does not accidentally filter out “Utama” requests; added regression coverage.
- Full Laravel regression after the queue fix: **54 tests / 269 assertions passed**.

### Browser accessibility/responsive verification

- Installed the project’s Playwright Chromium runtime and ran Customer Portal E2E across 320, 375, 768, 1024, and 1440 px widths plus keyboard navigation: **6 tests passed**.
- Each viewport check verified the primary heading, template download, tracking controls, and no horizontal body overflow.
- Manual real-device, orientation, 200% zoom, and full WCAG 2.2 AA review remain open; they are not claimed from this automated check.
- Added `@axe-core/playwright`; initial Customer Portal automated axe scan initially found contrast failures, which were corrected in `App.css`; final ID/EN browser run: **7 passed / 0 failed**, including axe in both locales.
- Added optional ClamAV-compatible malware scanning configuration (`MALWARE_SCAN_ENABLED`, binary, timeout) with safe argument execution and fail-closed behavior; local default remains disabled until hosting capability is verified.
- Full Laravel regression after malware and rate-limit hardening: **57 tests / 302 assertions passed**.
- Added public API per-IP throttle regression; focused public flow now passes **8 tests / 71 assertions**.

Remaining:

- Full translation-key migration for every internal page and manual device/WCAG review remain open; the toggle persistence is now functional but does not by itself prove complete translation parity.

## 2026-09-17 — Export formula-injection hardening

- Spreadsheet export now prefixes formula-like cell values beginning with `=`, `+`, `-`, or `@` so exported values remain text.
- Added a regression test using a formula-like institution name.
- Laravel verification: **53 tests / 258 assertions passed**; Pint and PHPStan passed.

### Compatibility check

- PHP `8.2.12` exposes PDO MySQL and SQLite drivers, but no local MySQL/MariaDB server is reachable on `127.0.0.1:3306`; compatibility/concurrency remains an environment blocker rather than a claimed pass.
- `TODO-025` is now checked `[x]` because route, navigation, and regression evidence satisfy its acceptance criteria.

## 2026-09-18 — Runtime demo validation and single-seed correction

### Implemented

- Corrected `backend/database/seeders/DatabaseSeeder.php` to create exactly one demo request (`DEMO-001`) instead of four demo requests.
- Kept all three internal demo roles: `admin@example.test`, `reviewer@example.test`, and `viewer@example.test`; password remains `password` for local demo use.
- Reset local SQLite development database with `migrate:fresh --seed`; verified 3 users, 1 customer, 1 request, and 3 available RFID cards.

### Runtime verification

- Laravel full suite: **58 tests / 317 assertions passed**.
- Role and workflow focus (approval, procurement, reports, public request, notifications, inventory): **27 tests / 214 assertions passed**.
- Internal UI: typecheck, lint, production build, and UI tests passed.
- Customer Portal: **9 unit tests passed**, typecheck, lint, production build passed.
- Customer Portal browser/accessibility run: **7 Playwright tests passed** across 320, 375, 768, 1024, and 1440 px plus ID/EN axe checks.
- HTTP smoke checks: backend login 200, portal 200, public template endpoint 200.
- All migrations applied; scheduled jobs remain registered at 08:00 and 02:00.

### Public demo

- ngrok tunnel online: `https://ravioli-partly-fried.ngrok-free.dev`
- Public checks passed for `/login` and `/api/public/v1/request-template`.
- Tunnel forwards to local Laravel backend port 8000. Customer Portal remains local at `http://127.0.0.1:5174`; it uses the backend origin configured by its Vite environment.

### Remaining environment limits

- ngrok URL is ephemeral and only remains available while the local ngrok process runs.
- MySQL/MariaDB concurrency, Biznet production/TLS/cron/backup/restore, malware scanning capability, manual device review, and full internal translation parity remain unclaimed as documented blockers.

## 2026-09-18 — Customer public-flow verification and local-origin repair

### Fixed

- Added `http://127.0.0.1:5174` to the backend CORS allowlist; both `localhost:5174` and `127.0.0.1:5174` now receive valid public API CORS responses.
- Changed Customer Portal's no-env local API fallback to `http://127.0.0.1:8000`; `VITE_API_URL` still overrides this for hosted environments.
- Added CORS regression coverage in `backend/tests/Feature/CorsTest.php`.

### Customer verification

- Real multipart customer submission with the supplied Excel template and JPEG proof returned **201 Created** and created a new request with generated tracking credentials.
- Real tracking request for `DEMO-001` with `demo-tracking` returned **200 OK**, status `NEW`, and timeline data.
- Customer Portal existing browser suite remains **7 passed**, including responsive and axe checks.
- Direct browser upload test exposed a PHP built-in-server transport quirk: Chromium's multipart fetch remained pending while the same public endpoint completed **201** through curl/Playwright APIRequest. The portal now has deterministic local origin configuration; production should use a real web server, not `artisan serve`, for external browser upload testing.

## 2026-09-18 — Internal Laravel UI browser verification and URL boundary

### Diagnosis and verification

- Initial internal UI smoke test used English labels against the localized login page and falsely reported a missing UI; selectors were corrected to `Alamat email`, `Kata sandi`, and `Masuk`.
- Browser smoke then passed for Admin, Reviewer, and Viewer: login redirected to `/dashboard`, heading rendered, and no page errors or HTTP 4xx/5xx asset responses were observed.
- Laravel `/login` returns 200 and unauthenticated `/` redirects to `/login`; backend Vite manifest and compiled assets are present.

### URL boundary

- Internal Laravel UI: `http://127.0.0.1:8000`.
- Customer Portal: `http://127.0.0.1:5174` (separate origin, intentionally isolated from internal UI).
- Customer Portal connects to Laravel public API through `VITE_API_URL`; backend CORS accepts both local portal origins. Hosted deployment can replace these with a customer subdomain/path without changing the public API contract.
- Public ngrok currently exposes Laravel backend at `https://ravioli-partly-fried.ngrok-free.dev`; the portal remains a separate local origin because the free ngrok endpoint is already occupied by the backend.

## 2026-09-18 — Fullstack QA/QE/engineering gate

### Verification passed

- Laravel: **59 tests / 318 assertions passed**.
- Pint: passed.
- PHPStan: **80/80 files, no errors**.
- Internal frontend: typecheck, lint, UI tests, and production build passed.
- Customer Portal: **9 unit tests passed**, typecheck, lint, production build passed.
- Customer Portal Playwright + axe: **7/7 passed** across 320, 375, 768, 1024, and 1440 px.
- Laravel browser smoke: Admin, Reviewer, and Viewer login/dashboard rendering passed; no page errors or HTTP 4xx/5xx asset responses.
- Composer audit and npm audit: no known vulnerability advisories/high vulnerabilities.
- Migrations: all applied. Scheduler: daily notification and export cleanup registered.
- Runtime: Laravel login 200, Customer Portal 200, ngrok login 200, CORS preflight 204, public template/tracking checks passed.

### Audit notes

- Internal and customer applications remain intentionally separated by origin: Laravel `:8000`, Customer Portal `:5174`; public workflow joins through `VITE_API_URL` and CORS.
- `artisan serve` is a local development server. Production browser upload verification requires the planned real web server/TLS deployment; no production hosting claim is made.
- Repository has no `.git` metadata, so commit-based two-axis diff review could not run. Static standards/spec review and all executable quality gates above were completed.

## 2026-09-18 — Final Kartu Baru workflow, quota, and shared ngrok

### Implemented

- Added CRM-scoped `customer_quotas` (default total 30) and `customer_quota_usages` history with free/paid allocation.
- Added `requested_quantity` to customer requests. Template RFID and Bukti Bayar are mandatory on every request; quota only allocates free/paid quantity at approval.
- Approval now locks the CRM quota inside the existing transaction, rechecks remaining quota, increments only free allocation, records paid overage, and rejects missing proof defensively.
- Added normalized institution name and duplicate warning by name across different CRM values. CRM remains the only customer identity key; no auto-merge occurs.
- Internal Dashboard now renders quota total, used, remaining, and usage history. Public requirements response intentionally omits quota numbers.
- Customer Portal now sends requested quantity and checks a server-authoritative `payment_required` flag. Local Vite proxy routes public API and internal Laravel paths through the same origin.
- Vite allows the active ngrok host, so one public endpoint serves Customer Portal at `/` and Internal Dashboard at `/login`/`/dashboard`.

### Verification

- New quota feature tests: **5 passed / 35 assertions**.
- Laravel full suite after implementation: **64 passed / 353 assertions**.
- Pint: passed after auto-formatting; PHPStan: **86/86 files, no errors**.
- Internal frontend: typecheck, lint, and build passed.
- Customer Portal: **9 tests passed**, typecheck, lint, build passed.
- Final sample DB reset to one seeded demo request, then one customer sample submitted through ngrok with the immutable XLSX template and payment proof: **201 Created** (`CRMFINAL1`, quantity 1); response entered Laravel `customer_requests` and was visible through the dashboard data path.
- Ngrok HTTP smoke: Customer Portal `/` 200, internal `/login` 200, requirements API 200, template download 200. Current tunnel: `https://ravioli-partly-fried.ngrok-free.dev` → Customer Portal `:5174` → Laravel `:8000` proxy/API.

### Errors fixed

- Missing quota endpoint/models/tables and mandatory per-request payment rules.
- Null optional payment file was passed to malware scanner/storage; file collection now filters absent uploads safely.
- Existing new-card callers without quantity remain backward-compatible with server default quantity 1.
- Dummy upload used in the first ngrok browser probe was not a valid XLSX and correctly returned 422; probe was corrected to use `Data/Template RFID 2025.xlsx`.
- Vite rejected the ngrok host with 403; `allowedHosts` and same-origin proxy routing were added.

### Current blockers

- Browser automation of multipart upload through the external ngrok URL still hits the known PHP built-in-server transport hang; the same ngrok multipart endpoint succeeds with the real XLSX via curl in 201, and production should use a real web server/TLS instead of `artisan serve`.
- MySQL/MariaDB concurrency and Biznet production capability/backup/TLS verification remain environment blockers; SQLite application tests and transaction/lock code are complete.

## 2026-09-18 — Pre-user-input full recheck

- No new data was created during this check.
- Local runtime: Customer Portal 200 and Laravel login 200.
- Shared ngrok: Customer Portal 200, internal login 200, quota requirements API 200, template download 200.
- All migrations are applied, including `2026_09_18_000004_add_kartu_baru_quota`; public quota/request routes are registered.
- Laravel: **64 tests / 353 assertions passed**.
- Internal frontend: UI test, typecheck, lint, and build passed.
- Customer Portal: **9 unit tests passed**, typecheck, lint, and build passed.
- Customer Portal browser/accessibility: **7/7 passed**.
- Ready for user input testing through `https://ravioli-partly-fried.ngrok-free.dev/`.

## 2026-09-18 — Mandatory payment proof clarification

- Updated the domain contract: every Customer Request has its own `requested_quantity`, and every request must upload both Template RFID and Bukti Bayar.
- Changed the public requirements contract to report `payment_required: true` consistently; quota values remain private to Internal Dashboard.
- Added regression coverage for quantity 10: submission without payment proof returns 422; the same request with valid proof returns 201.
- Rechecked: Laravel **64 tests / 356 assertions**, Customer Portal **9 tests**, Playwright/axe **7/7**, frontend typecheck/lint/build, Pint, and PHPStan all passed.

## 2026-09-18 — Internal Dashboard ngrok UI repair

- Root cause: Laravel/Inertia generated absolute asset URLs to `http://127.0.0.1:8000` when reached through the Vite/ngrok proxy, so the internal login/dashboard HTML loaded without usable UI assets.
- Fix: Vite proxy now forwards the public host/protocol and Laravel trusts the forwarded proxy headers. Assets now resolve to the active HTTPS ngrok origin; no localhost asset URL remains in the public login HTML.
- Browser smoke through ngrok passed for Admin, Reviewer, and Viewer: login succeeded and the dashboard rendered `Monitoring RFID` and `Quota Kartu Baru` without page errors or failed HTTP requests.
- Final HTTP checks: Customer Portal `/` **200**, internal `/login` **200**, unauthenticated `/dashboard` **302** to login, and quota requirements API **200**.
- Final regression: Laravel **64 tests / 356 assertions**, PHPStan **86/86**, Pint passed, Internal UI test/typecheck/lint/build passed, Customer Portal **9 tests** plus typecheck/lint/build passed, and Playwright/axe **7/7** passed.
# 2026-09-23 — Revisi UI dan workflow terbaru

### Implemented

- Penerimaan Catatan Pengadaan sekarang parsial: setiap penerimaan memakai qty aktual, menambah Stock In dan inventory cards, lalu berstatus PARTIAL sampai requested quantity terpenuhi. Input RFID awal/akhir dihapus dari form.
- Stock In manual dihapus dari halaman Inventory; halaman tersebut menampilkan ledger qty dan filter semua/hari ini/kemarin/minggu/bulan/custom melalui query periode.
- `requested_quantity` diwajibkan untuk semua request type, bukti bayar menjadi opsional, dan quota 30/free-paid allocation tidak lagi memblokir approval.
- Report/export dibuka untuk Admin dan Viewer read-only; menu Laporan sidebar dihapus dan Report tetap dapat diakses dari Monitoring.

### Verification

- Procurement flow: **3 tests passed / 16 assertions**.
- Backend typecheck: passed.
- Full legacy suite: **56 passed, 8 failed** karena test lama masih mengharuskan payment proof/quota dan fixture request lama tidak mengirim `requested_quantity`; perlu migrasi test ke requirement baru.

### Blockers / follow-up

- Popup notifikasi header belum menampilkan daftar unread secara inline; saat ini header menyediakan akses ke inbox role-scoped.
- Filter custom inventory dan pilihan periode report perlu browser verification lanjutan.

## Release closure — 2026-09-23

### Implemented

- Unified runtime uses Laravel + Inertia + React: public `/pengajuan` and `/tracking`, authenticated internal `/admin`; standalone portal/CORS is no longer an active runtime dependency.
- Final RBAC is Admin and Viewer. Viewer can read and export reports but cannot mutate requests/procurement/inventory or download private evidence; guest `/admin` redirects to login.
- Procurement now supports actual-quantity partial receipt without receipt RFID start/end. Receipt generates Stock In, keeps business/audit history, and rejects zero/negative/over-receipt.
- Every request type requires quantity; payment proof is optional; quota-30 approval flow is disabled.
- Inventory manual Stock In UI was removed and period filters include custom. Report is reached from Monitoring with daily/weekly/monthly/yearly/custom filters and PDF/Excel export.
- Header notification popup, Activity category filter, Indonesian default + EN toggle, collapsible/sidebar tooltip/mobile drawer were retained or completed.

### Verification evidence

- `rtk php artisan test`: **65 passed, 342 assertions**.
- `rtk npm run test:ui`: **1 file / 1 test passed**.
- `rtk npm run typecheck`: passed.
- `rtk npm run lint`: passed.
- `rtk npm run build`: passed.
- Security targeted scan for hardcoded `sk-`, `api_key`, and `console.log` in application code: no matches.
- ECC security-review, TDD workflow, and verification-loop instructions were read from the installed local ECC skill directory. RTK `0.48.0` and Caveman skill were verified; no reinstall performed.

### Browser QA evidence

- MCP browser smoke passed for public `/pengajuan`, ID/EN toggle, internal login, Admin `/dashboard`, responsive 390px mobile drawer, collapsible sidebar, and header notification popup.
- Lighthouse accessibility score: **100 mobile** and **100 desktop**. Console error/warning scan: no messages.
- One local HTTP Lighthouse best-practice/SEO check remains environment-related (HTTPS is unavailable on `127.0.0.1`); production TLS is a deployment gate, not an application defect.

### Recheck and user tunnel — 2026-09-24

- Recheck suite: **65 passed / 342 assertions**, UI test passed, typecheck passed, lint passed, build passed.
- Added explicit negative coverage for quantity `0`/negative and application upload over 3 MB; targeted suite passed **9 tests / 79 assertions**.
- Ngrok verified: `https://ravioli-partly-fried.ngrok-free.dev` returns HTTP 200 and browser smoke reaches `/pengajuan` after the free-tier warning.
- Tunnel command: `ngrok http 8099`; local Laravel server: `php artisan serve --host=127.0.0.1 --port=8099`.
# 2026-09-24 — Final UI closure and browser verification

- Public `/pengajuan`: title changed to **Pengajuan Perubahan Data Kartu RFID**, submit/track tabs, clearer template/payment document group, optional payment proof, responsive spacing, and minimal transitions.
- Removed Notifikasi from sidebar and removed its page route/component; header bell popup remains and links to Pengaturan preferences. `/admin` is Admin-only and `/viewer` is Viewer-only.
- Fixed dashboard `emerald` tone mapping React crash after login and improved contrast/sidebar collapsed alignment. Logout hover is red with transition.
- Verification: `rtk php artisan test` **66 passed / 333 assertions**; UI test **1/1**; typecheck, lint, build passed; Lighthouse accessibility **100 desktop / 100 mobile**; no browser console errors.
- User tunnel: `https://ravioli-partly-fried.ngrok-free.dev` remains available.
# 2026-09-24 — Two-card public form and settings polish

- Public form split into **Informasi Pengajuan** and **Dokumen Pendukung** cards while retaining one logical submission form.
- Template download moved beside document uploads with the intended Unduh → Isi → Upload flow. Browser-native file controls are wrapped in accessible labeled controls with filename feedback.
- Tracking remains a separate tab and never renders alongside the request form. Settings spacing was reduced and remaining profile/password/account labels were translated to Indonesian.
- Verification: 66 Laravel tests / 333 assertions, UI test 1/1, typecheck, lint, and build passed. Mobile public tab switch passed; Lighthouse accessibility 100 and console clean.
# 2026-09-24 — RFID Operations navbar/sidebar closure

- Rebuilt `AuthenticatedLayout` navigation using a fixed collapsible sidebar pattern: 256px expanded with labels and 64px icon-only rail when collapsed.
- Added compact active route styling, hover/focus states, keyboard-visible tooltip labels in collapsed mode, mobile drawer/backdrop, and bottom avatar/name/role/logout behavior.
- Kept the shared Admin/Viewer menu and header notification bell, ID/EN toggle, and Sesi aman controls. Logout hover/focus is red with a short transition.
- No `ResizablePanelGroup` or new dependency was introduced; existing React, Tailwind, Lucide, Inertia patterns were reused.
- Browser QA: desktop expanded/collapsed + tooltip, mobile drawer, active route, popup/header controls, Admin `/admin`, Viewer restriction `/viewer` (Admin receives 403), and Lighthouse accessibility 100 passed. Console clean.
- Public form recheck: fixed Card 01 label/input stacking; made Card 02 download a full-width green action before uploads; removed visible `(opsional)` from payment label while retaining optional backend validation. Fresh isolated browser context confirmed guest `/admin` redirects to `/login`; authenticated sessions continue directly to the dashboard by design.
- Navbar component integration 2026-09-24: extracted the existing RFID sidebar into `backend/resources/js/Components/ui/navbars.tsx`, preserving fixed 256/64px collapse, active state, tooltip, mobile drawer, role footer, and logout focus styling. The supplied resizable demo was adapted without `ResizablePanelGroup` because it is not appropriate for the application sidebar.
- Verification after extraction: typecheck, lint, UI test, production build, and Laravel suite passed; 66 tests / 333 assertions.
- Public root fix 2026-09-24: verified `/` was redirecting to `/pengajuan`; changed it to render `Public/RequestPortal` directly and kept `/pengajuan` as a compatibility redirect. Added regression coverage for both routes.
- Public date-field fix 2026-09-24: date input is now explicitly block-level so `Tanggal permintaan` stays above the control instead of appearing inline beside it. Typecheck, lint, and production build passed; ngrok browser snapshot verified the root form.
- Procurement/sidebar refinement 2026-09-24: receipt action now uses a clipboard icon and green styling with responsive columns; Admin can delete only unreceived DRAFT notes, with immutable audit entry and backend protection for received notes. Sidebar nav horizontal overflow was removed; minimized tooltip behavior retained. Full verification: 68 Laravel tests / 349 assertions, UI test, lint, typecheck, build, and ngrok browser QA passed.
- Sidebar/auth recheck 2026-09-24: collapsed header now centers the brand mark with a non-overlapping expand control; collapsed footer stacks avatar and logout; sidebar nav clips horizontal overflow. Isolated ngrok guest context confirmed `/admin` goes to `/login`; an already authenticated Admin going directly to `/dashboard` is expected session behavior and was left intact.
- Sidebar persistence fix 2026-09-24: collapsed state is stored in `localStorage` and restored across Inertia route navigation; content padding transitions with the sidebar width. Browser QA collapsed the sidebar, navigated to Aktivitas, and confirmed it remained collapsed.
- Header toggle refinement 2026-09-24: minimized mode places the “Buka sidebar” control directly below the brand icon in the header; the footer remains limited to avatar and logout. Expanded mode keeps the header “Ciutkan sidebar” control.
- Sidebar spacing polish reverted 2026-09-24: restored the prior header, rail, and footer sizing after UI review requested the previous appearance.
- Navbar code cleanup 2026-09-24: adapted the supplied shadcn navigation pattern into the existing Inertia sidebar with typed menu data, reusable navigation links, existing route behavior, and no new dependency or resizable-panel demo.
- Header/footer alignment fix 2026-09-24: minimized mode now keeps the brand and expand control on one 64px header row; the footer uses compact avatar and logout controls that fit the rail without clipping.
- Sidebar reference integration 2026-09-29: applied the supplied shadcn navigation pattern to the existing RFID sidebar while retaining the project’s Inertia routes, local state persistence, Lucide icons, responsive drawer, and no resizable-panel dependency.
- Full verification 2026-09-29: ECC is installed and enabled; RTK commands execute successfully; Laravel suite passed 68 tests / 349 assertions; typecheck, lint, UI test, and production build passed. Ngrok and Laravel were restarted after detecting both processes had stopped. Guest `/admin` was verified in browser to redirect to `/login`, while public `/` rendered the customer request page.
- Ngrok recheck 2026-09-29: detected a stale PHP process without a listener on port 8099 and replaced only those PHP server processes. Laravel now listens on `127.0.0.1:8099`; local `/` and public ngrok `/` return 200, guest `/admin` returns 302 to `/login`, public `/login` returns 200, and ngrok inspection API returns 200. `/pengajuan` redirects to `/` by design as the public entry-point compatibility route.
- Sidebar/session polish 2026-09-29: aligned collapsed sidebar header and footer with the same two-column geometry so brand, toggle, avatar, and logout controls stay centered without clipping. Internal session lifetime is now 480 minutes (8 hours idle) in `.env` and `.env.example`; logout and later login remain available through the existing auth flow. Verification: config shows 480, typecheck, lint, UI test, production build, Laravel suite (68 tests / 349 assertions), and ngrok root (200) passed.
- Mini sidebar/auth refinement 2026-09-29: collapsed navigation now uses vertical control groups: brand followed by the expand arrow below the header, and avatar followed by logout below the footer divider. Internal sessions no longer persist after browser close (`SESSION_EXPIRE_ON_CLOSE=true`), the optional remember-session control was removed, and backend login ignores remember requests. Guest `/admin` still redirects to `/login`; role restrictions remain enforced. Verification: targeted auth/entrypoint tests 8 passed, full Laravel suite 68/349, typecheck, lint, UI test, build, config check, and ngrok checks passed.
- Mini header alignment fix 2026-09-29: moved the collapsed expand control into a dedicated 32px row below the brand header with its own divider and fixed hit area, preventing the arrow from appearing clipped or crowded. Typecheck, lint, UI test, and production build passed.
- Public mobile upload refinement 2026-09-29: supporting-document controls now use a responsive three-column grid with a flexible label and non-wrapping file-picker action. Long upload labels wrap inside their own area instead of colliding with the button on narrow screens. Typecheck, lint, UI test, and production build passed.
- Responsive QA 2026-09-29: verified public request tabs and form at mobile, tablet, and desktop viewport sizes with no horizontal overflow; login form also fits mobile and retains connected labels. Ngrok browser QA found no console errors or failed asset requests. Lighthouse mobile reached Accessibility 100 and Best Practices 100 after fixing the login landmark, public language-button accessible name, and download-button contrast.
- Final release gate 2026-09-30: repository verification initially found six Pint formatting violations and a PHPStan error in `/admin`/`/viewer` route closures caused by using `abort_unless(...) ?? redirect(...)`. Pint fixed the formatting; route closures were rewritten with explicit authorization guard and redirect. Verification then passed Composer validation/audit, Pint, PHPStan, Laravel 68 tests/349 assertions, backend lint/typecheck/UI test/build, customer-portal lint/typecheck/9 tests/build, immutable Data hashes, and no diff whitespace errors.
- Demo/deploy documentation 2026-09-30: updated project README, PRD, plan, todo, verification, and hosting capability docs to describe the active guest root website, protected internal routes, local build/run steps, final QA evidence, and remaining account-specific hosting checks. Local `.env`, dependencies, and generated artifacts remain excluded from release commits.
