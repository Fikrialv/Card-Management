# Card Management & Customer Request System

Planning workspace for the RFID card inventory and customer-request product.

## Current state

The runnable product is in `backend/`: one Laravel 12 + Inertia + React application serving the public customer website at `/` and authenticated internal operations at `/login`, `/admin`, and `/viewer`. The public entry point does not require customer authentication and provides request submission, template download, optional payment proof, and tracking.

The internal surface is role-protected: Admin can operate requests, inventory, and procurement; Viewer is read-only with report/export access. Guest access to `/admin` and `/viewer` is redirected to `/login`. Sessions do not persist after the browser closes.

`customer-portal/` remains a separately buildable legacy/reference client and is not the active runtime boundary. `Data/` files are immutable source references and are never treated as fixtures or committed secrets.

The current implementation has passed the repository verification gate and responsive browser QA. Hosting-specific checks (PHP extensions, database credentials, domain/TLS, cron, backups, and mail) remain account-level deployment configuration.

## Local run and production build

```powershell
cd backend
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve --host=127.0.0.1 --port=8099
```

For the full repository gate from the root, use `pwsh ./scripts/verify.ps1 -SkipBrowser`. Do not commit `.env`, `vendor/`, `node_modules/`, or generated build output.

For deployment, set `APP_ENV=production`, `APP_DEBUG=false`, a real `APP_URL`, database credentials, private storage, queue/mail settings, and HTTPS before running `php artisan migrate --force` and publishing the built `public/build` assets.

## Documentation workflow

- `Docs/PRD.md` — WHAT, WHY, and business rules.
- `Docs/plan.md` — architecture and implementation order.
- `Docs/todo.md` — executable checklist, dependencies, acceptance criteria, tests, and status.
- `Docs/update.md` — factual implementation evidence, decisions, failures, and blockers.

Do not mark a product task complete until its acceptance criteria and tests are recorded in `Docs/update.md`.
