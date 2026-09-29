# Verification

Run all installed quality gates from the repository root:

```powershell
pwsh ./scripts/verify.ps1
```

Add `-WithMariaDb` when the isolated `rfid_codex_test_20260908` database is available. Add `-SkipBrowser` only for a fast local check; CI always runs Playwright and MySQL 8 integration tests.

The entry point validates Composer metadata/security advisories, formatting, PHPStan, Laravel tests, both TypeScript applications, production builds, and immutable `Data/` hashes. Browser QA is run against the active Laravel/ngrok URL when the browser service is available. The verified 2026-09-29 gate passed: Laravel 68 tests/349 assertions, customer-portal 9 tests, all typecheck/lint/build gates, PHPStan, Pint, Composer audit, and source hashes.

The final public smoke matrix covers `/` at mobile, tablet, and desktop sizes, the separate tracking tab, `/login`, guest `/admin` redirect, no horizontal overflow, console/network cleanliness, and accessibility/browser best practices. Hosting checks remain deployment-specific and are listed in `Docs/hosting-capability.md`.
