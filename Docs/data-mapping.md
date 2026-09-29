# Data Source Mapping

Status: approved reference baseline. Files in `Data/` are immutable. `Template RFID 2025.xlsx` is served only as the controlled download asset; it is not parsed or imported on submission.

## Source inventory

| Source | Reference scope | Active use |
|---|---|---|
| `Template RFID 2025.xlsx` | Customer card-request fields and template layout | Downloaded by Customer Portal; customer re-uploads a completed `.xlsx` as Pengajuan Kartu. |
| `UPDATE KARTU RFID.xlsx` | Historical Stock In/Out ledger and RFID-range reference | Range/ledger vocabulary only. Historical import remains out of scope. |
| `template pengajuan.jpeg` | Historical procurement-request visual | Visual reference only; not a NOPOL/PDF template. |

## Active customer request mapping

| Source / policy | Canonical field | Rule |
|---|---|---|
| NAMA INSTANSI | `customer.institution_name` | Required; trim; max 160. |
| NO CRM / CRM | `customer.crm_number` | Required; 1–13 ASCII alphanumeric characters; uppercased. |
| Product policy | `customer_request.request_type` | Exactly `new_card`, `damaged_card`, `system_error_card`, or `lost_card`. |
| Product policy | `customer_request.request_date` | Required request date; not customer-controlled deadline. |
| Customer-provided completed template | `request_evidence.artifact_type=application` | Required `.xlsx`, maximum 3 MB, private storage. |
| Customer-provided proof | `request_evidence.artifact_type=payment_proof` | Required `pdf`, `jpg`, `jpeg`, or `png`, maximum 5 MB, private storage. |

Kota, area, PIC customer, phone, email, inline vehicle/NOPOL/BBK/kuota fields, activation, data changes, and balance mutation are intentionally outside the active public request contract. The template may contain historical fields; it is a submitted artefact, not a reason to recreate removed portal fields.

## Inventory and procurement mapping

| Source / policy | Canonical field | Rule |
|---|---|---|
| Ledger `TANGGAL` | `stock_movement.occurred_on` | ISO date. |
| Ledger `URAIAN` | `stock_movement.description` | Source/receipt reference. |
| Inbound start/end | `rfid_range.start_number`, `end_number` | Digits only; inclusive, unique, no overlap. |
| Derived range count | `rfid_range.quantity`, `stock_movement.quantity` | `end - start + 1`; never manually trusted. |
| Admin Catatan Pengadaan | `procurement_note.request_date`, `pic_name`, `requested_quantity`, `notes` | Admin-created request-side note. |
| Admin receipt | `received_on`, `received_quantity`, RFID start/end | Receipt quantity must equal range count; one atomic Stock In updates inventory. |

Quantity alone cannot create uniquely assignable RFID cards. A concrete inclusive start/end range is required at receipt. Historical workbook rows are not imported without approved owner, cut-off, deduplication, and reconciliation policy.

## Privacy and publication

- The template remains source-controlled; its SHA-256 is recorded in `Docs/update.md`.
- Public customers receive only request number, tracking code, and safe status projection.
- Evidence and all historical reference values stay in private storage/log boundaries.
- The JPEG does not authorize a document/PDF implementation.
