# ADR-0001: Laravel ownership on Biznet/cPanel

Status: Accepted  
Date: 2026-09-13

## Context

RFID operations need one authoritative domain model, session-protected internal operations, and separately deployed public customer experience. Biznet NEO Web Hosting documents PHP Selector, MySQL databases, subdomains, SSL, and Laravel deployment through cPanel. Shared plans do not guarantee a persistent Node process or terminal/Composer access on the account.

## Decision

- `backend/` is the sole Laravel application and domain owner. It owns migrations, Eloquent, authorization, queues, private storage, internal routes, and public REST routes.
- Internal Dashboard deploys with Laravel at configured internal origin and uses Inertia.js, React, TypeScript, Tailwind, and shadcn/ui.
- `customer-portal/` is separate React/TypeScript static build at different public origin. It calls only allowlisted Laravel public REST endpoints.
- Production frontend assets are built in CI/local release tooling, then uploaded as immutable artifacts. Production never requires Node process.
- Queue baseline is synchronous. Database queue worker is enabled only after selected account proves safe worker supervision; scheduler uses `php artisan schedule:run` only after cron capability is verified.
- Server timestamps are UTC. Business dates, due-today calculations, and default presentation use `Asia/Jakarta`; localized rendering remains keyed and supports Indonesian and English.

## Alternatives rejected

- Next.js/Prisma/PostgreSQL: adds second domain owner and conflicts with cPanel/MySQL operating model.
- One combined public/internal SPA: weakens public/private deployment and bundle boundaries.
- Runtime Node build: unavailable or unreliable on supported shared-hosting plans.

## Consequences and security boundary

- Laravel release owns API and internal dashboard rollback; Customer Portal release rolls back independently.
- Internal origin uses Laravel session, CSRF, RBAC, Policies/Gates, and Inertia routes.
- Public origin receives no internal bundle/module and has only explicit CORS origins, rate-limited public API routes, Form Requests, and safe API Resources.

## Sources

- [Biznet Laravel on cPanel](https://kb.biznetgio.com/id_ID/informasi/cara-deploy-website-laravel-pada-hosting-cpanel)
- [Biznet NEO Web Hosting capabilities](https://kb.biznetgio.com/id_ID/getting-started/getting-started-neo-web-hosting)
