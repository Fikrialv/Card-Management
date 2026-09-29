# UI/UX Direction — RFID Operations

## Source and usage

This direction translates conversation reference images #7–#15 into product-specific rules. They are inspiration only; no layout, copy, or asset is copied. The UI must remain original, accessible, Indonesian-first, and suitable for RFID operations.

## Internal Dashboard

- Use a quiet operations shell: stable sidebar, compact contextual header, single clear page title, and semantic status colors.
- Monitoring combines related KPI, attention items, priority queue, stock health, activity, and notification summary. Each card links to the feature page; it must not become a second workflow.
- Pengajuan, Inventory, Catatan Pengadaan, Reports, and Activity use a clear primary action, filter/search bar, data surface, empty/loading/error state, then detail drawer/page.
- Desktop data surfaces may use dense tables. On tablet/phone, retain the primary identifier/status/action, convert secondary columns to stacked details, and expose row detail progressively. Never rely on horizontal scrolling alone.
- Catatan Pengadaan is a focused record: requested data and receipt data appear as distinct, editable sections with receipt/Stock In impact plainly shown before confirmation.
- Internal notifications use an inbox/bell summary, unread count, safe preview, read state, and permission-aware deep link.

## Customer Portal

- Begin with a short, clear choice: download template, complete it, then submit both files. Explain tracking credentials before submit confirmation.
- Keep form fields to approved customer/request data only. Do not show vehicle cards or multi-item controls.
- File controls identify artefact purpose separately: Pengajuan Kartu and Bukti Bayar; show format/size guidance, selected file name, error, retry, and privacy statement.
- Tracking has two credential inputs, a clear status timeline, plain-language next action, and customer-safe notification feed.
- Do not hide required content behind hover or decorative animation; preserve keyboard order and 44px minimum touch targets.

## Visual system

- Neutral/slate backgrounds, white surfaces, one deep blue operational accent, and semantic success/warning/danger colors with WCAG AA contrast.
- Use moderate radii, crisp 1px borders, generous spacing around forms, and restrained icon use. Avoid gradients, glassmorphism, excessive pills, and dashboard-card repetition without hierarchy.
- Typography must make status, page title, field label, helper/error text, and primary action distinguishable at 200% zoom.
- Respect `prefers-reduced-motion`; focus indicator is visible and never color-only.

## Required test matrix

Test critical portal and internal flows at 320px/375px phone, 768px tablet, 1024px laptop, and 1440px desktop; portrait/landscape where relevant; mouse/touch/keyboard; 200% zoom; ID/EN; and automated plus manual WCAG 2.2 AA checks.
