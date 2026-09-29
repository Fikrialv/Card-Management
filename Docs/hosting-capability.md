# Biznet/cPanel Capability Baseline

Checked: 2026-09-29.

## Confirmed from provider documentation

- Biznet cPanel Hosting advertises PHP Selector, databases, subdomains, SSL, and backup on supported tiers. Built-in Acronis backup is listed for Medium, Large, and Extra, not Small.
- Biznet documents Laravel deployment with Composer, `.env`, MySQL, and domain/subdomain configuration, while separate shared-hosting guidance says terminal/Composer access is not guaranteed.
- Node.js is listed only for Large and Extra. Build all frontend artifacts before upload; no production Node dependency is allowed.
- cPanel-managed MySQL and phpMyAdmin are available; MariaDB is a documented WHM option.
- Build artifacts can be uploaded through File Manager.

Sources:

- <https://www.biznetgio.com/product/hosting-cpanel>
- <https://kb.biznetgio.com/id_ID/informasi/cara-deploy-website-laravel-pada-hosting-cpanel>
- <https://www.biznetgio.com/product/cpanel-license>
- <https://kb.biznetgio.com/id_ID/instalasi/cara-merubah-mysql-ke-mariadb-di-whm>

## Conservative deployment contract

- PHP minimum: 8.2.
- Database: MySQL 8 preferred; compatible MariaDB accepted; InnoDB required.
- Frontend assets: built outside production, then deployed as artifacts.
- Queue: `sync` baseline. Enable database queue worker only after persistent worker or safe cron cadence is confirmed.
- Scheduler: cPanel cron calling `php artisan schedule:run` when account allows it.
- Storage: Laravel private local disk. Public storage symlink is not required for evidence/PDF.
- Backup: provider backup does not replace application restore drills.
- Deployment domains: internal app/API and Customer Portal separated by configured origins.

## Account-level deployment checks

Before production release, verify actual plan: PHP extensions, exact MySQL/MariaDB version, shell/Composer access, cron minimum interval, document-root control, process/memory limits, outbound mail, backup retention/restore access, and remote DB policy. Also verify both domains/TLS, CORS origin, session cookie domain, private storage path/permissions, and migration/rollback runbook. These are release checks, not blockers for local implementation.

## Local release evidence

The application has a production build and a documented single-origin runtime under `backend/`. The guest root is ready for public use, while internal routes remain protected by Laravel authentication and role checks. Before publishing to a hosting account, replace local `.env` values, set `APP_ENV=production` and `APP_DEBUG=false`, configure HTTPS, run migrations with `--force`, and verify backups/restores. No hosting credentials are stored in this repository.
