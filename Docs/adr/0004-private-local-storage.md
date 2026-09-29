# ADR-0004: Private local storage on cPanel

Status: Accepted  
Date: 2026-09-13

## Context

Evidence uploads and generated PDFs contain customer and operational data. cPanel local file management and backups do not make public web paths an authorization mechanism.

## Decision

- Evidence and PDFs use Laravel private local storage outside web document roots.
- File names and paths are server generated. Download streams only after policy authorization; permanent public object URLs are forbidden.
- Validation checks declared type, extension, MIME/signature where practical, size, count, and request ownership. Failed database/file operations perform cleanup.
- Provider backup is recovery input, not replacement for application backup, restore, retention, and access-review procedures.

## Alternatives rejected

- `public` disk or File Manager links: bypass application authorization.
- Cloud object storage before multi-server/capacity need: adds unapproved infrastructure and credentials.

## Consequences

Release verification proves `storage` and `bootstrap/cache` write permissions, private-path placement, backup retention, and restore access. A future object-storage move retains Laravel filesystem abstraction and needs ADR amendment.
