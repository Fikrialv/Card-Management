# ADR-0003: MySQL 8 with MariaDB-compatible Laravel schema

Status: Accepted  
Date: 2026-09-13

## Context

Biznet cPanel exposes MySQL database management. Biznet also documents MariaDB as available cPanel/WHM engine choice, so exact engine and version must be checked on purchased account. RFID allocation requires transactional integrity and row locking.

## Decision

- MySQL 8 is target engine; MariaDB-compatible operation is accepted without domain-model change.
- Laravel migrations are schema authority. phpMyAdmin is inspection/operating tool, never manual schema drift.
- Tables use InnoDB, UTF-8 compatible collation, foreign keys, unique constraints, transactions, deterministic lock order, and bounded deadlock retry where allocation requires it.
- Avoid vendor-specific SQL unless ADR documents tested MySQL/MariaDB equivalent.
- Isolated test databases verify migrations and locking against compatible real engine.

## Alternatives rejected

- PostgreSQL/Prisma: conflicts with approved hosting and Laravel ownership.
- phpMyAdmin-managed schema: cannot provide repeatable migration/rollback evidence.

## Consequences

Actual engine/version, collation, timezone behavior, and remote-connection policy are mandatory release checks. Migration failures require rollback/restore runbook execution before retry.

## Sources

- [Biznet Laravel database setup](https://kb.biznetgio.com/id_ID/informasi/cara-deploy-website-laravel-pada-hosting-cpanel)
- [Biznet MySQL to MariaDB guidance](https://kb.biznetgio.com/id_ID/instalasi/cara-merubah-mysql-ke-mariadb-di-whm)
