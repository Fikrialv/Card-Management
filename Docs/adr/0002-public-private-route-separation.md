# ADR-0002: Separate public and internal deployment boundaries

Status: Accepted  
Date: 2026-09-13

## Context

Customer request and tracking must be public while approval, inventory, audit, and staff data remain internal. Both experiences use one Laravel backend and database, but must not share a public client bundle or authorization boundary.

## Decision

- Internal Dashboard is served only from configured internal origin with Laravel session authentication and CSRF protection.
- Customer Portal is separately deployed origin. It may call only `/api/public/v1/*` endpoints documented in public contracts.
- Public CORS is explicit origin allowlist, never `*` with credentials. Public routes are rate-limited and return safe resource projections.
- Internal routes, Inertia payloads, controllers, and fields are never imported into `customer-portal/` or exposed by public API Resources.
- Tracking requires both Request Number and Tracking Code; it creates no internal session or exposes private notes, audit details, staff identity, or unrestricted customer data.

## Alternatives rejected

- Hiding internal controls in one public SPA: client visibility is not authorization.
- Sharing internal JSON resources with portal: risks private-field exposure.

## Consequences

Every endpoint needs explicit public/internal classification, authorization test, and resource projection. Two origins require release-time CORS, cookie, TLS, DNS, and callback verification.
