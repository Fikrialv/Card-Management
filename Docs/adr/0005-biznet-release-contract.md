# ADR-0005: Biznet/cPanel deployment and release contract

Status: Accepted with account verification gate  
Date: 2026-09-13

## Decision

One release contains two independently deployable artifacts:

1. Internal Laravel application and Inertia assets at configured internal origin; it owns REST API and MySQL/MariaDB connection.
2. Customer Portal static artifact at configured public origin; it knows only configured API base URL.

TLS is required on both origins. Database credentials and Laravel secrets exist only in backend environment. Portal build receives only allowlisted `VITE_*` public configuration. Build artifacts are created before upload because Biznet plan capabilities differ: PHP Selector is available across listed web-hosting plans, while Node.js is listed only for Large/Extra; shared-hosting terminal access is not guaranteed.

## Account verification gate

Before production, record selected plan and evidence for:

- exact PHP version/extensions, MySQL or MariaDB version, charset/collation, and InnoDB;
- document-root control, SSH/Composer availability, upload limits, process/memory limits, and terminal policy;
- cron availability/minimum interval, queue-worker support, outbound mail, and remote DB policy;
- internal/public domains, TLS renewal, DNS, CORS allowlist, session cookie domain, and portal API origin;
- private storage path/permissions, backup frequency/retention, restore access, and tested restore;
- release, migration, rollback, maintenance, and health-check runbooks.

Missing worker or cron proof keeps queue synchronous and scheduled work disabled; it does not alter Laravel domain architecture.

## Sources

- [Biznet cPanel plan capability table](https://www.biznetgio.com/product/hosting-cpanel)
- [Biznet Laravel deployment guide](https://kb.biznetgio.com/id_ID/informasi/cara-deploy-website-laravel-pada-hosting-cpanel)
- [Biznet Laravel shared-hosting limitations](https://kb.biznetgio.com/id_ID/neo-web-hosting/cara-mengatasi-error-500-pada-website-laravel-di-cpanel)
