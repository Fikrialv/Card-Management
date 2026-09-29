import { useEffect, useMemo, useState } from 'react'
import type { FormEvent } from 'react'
import { ArrowRight, CheckCircle2, CreditCard, Download, FileSpreadsheet, Globe2, Search, ShieldCheck, Upload } from 'lucide-react'
import './App.css'
import { apiBaseUrl } from './config'

type Locale = 'id' | 'en'
type View = 'request' | 'tracking'
type Errors = Record<string, string[]>
type PortalTimeline = { status?: string; reason?: string; at?: string }
type PortalNotification = { id: number | string; type: string; data: Record<string, unknown>; read_at: string | null }

const copy = {
  id: {
    brand: 'RFID Service', requestTab: 'Buat pengajuan', trackingTab: 'Lacak pengajuan', eyebrow: 'Portal layanan pelanggan',
    title: 'Pengajuan kartu RFID', intro: 'Kirim data pengajuan dan bukti yang diperlukan dalam satu alur yang jelas.',
    trust: 'File Anda disimpan aman dan hanya diproses tim operasional.', customerTitle: 'Data pengajuan', customerHelp: 'Gunakan data institusi yang terdaftar.',
    institution: 'Nama institusi', crm: 'Nomor CRM', requestType: 'Jenis pengajuan', requestDate: 'Tanggal permintaan',
    templateTitle: 'Siapkan berkas', templateHelp: 'Unduh lalu lengkapi template pengajuan kartu sebelum mengunggahnya.', download: 'Unduh template Excel',
    application: 'Template RFID', applicationHelp: 'File Excel (.xlsx), maksimum 3 MB.', quantity: 'Jumlah kartu baru', payment: 'Bukti bayar', paymentHelp: 'Wajib pada setiap pengajuan, maksimum 5 MB.',
    submit: 'Kirim pengajuan', submitting: 'Mengirim…', success: 'Pengajuan berhasil dikirim', requestNumber: 'Nomor pengajuan', trackingCode: 'Kode pelacakan',
    saveCode: 'Simpan kedua informasi ini untuk melihat perkembangan pengajuan.', trackTitle: 'Lacak pengajuan', trackIntro: 'Masukkan nomor pengajuan dan kode pelacakan yang Anda terima.',
    track: 'Lihat status', tracking: 'Mencari…', status: 'Status pengajuan', emptyTrack: 'Status akan tampil di sini setelah pencarian berhasil.',
    error: 'Permintaan tidak dapat diproses. Periksa data yang ditandai lalu coba lagi.', required: 'Wajib diisi.',
    timeline: 'Perkembangan', notifications: 'Pemberitahuan', unread: 'Baru', markRead: 'Tandai dibaca',
    types: { new_card: 'Kartu baru', damaged_card: 'Kartu rusak', system_error_card: 'Kartu error system', lost_card: 'Kartu hilang' },
  },
  en: {
    brand: 'RFID Service', requestTab: 'Create request', trackingTab: 'Track request', eyebrow: 'Customer service portal',
    title: 'RFID card request', intro: 'Submit the request data and required evidence in one clear flow.',
    trust: 'Your files are stored securely and processed only by operations.', customerTitle: 'Request details', customerHelp: 'Use your registered institution details.',
    institution: 'Institution name', crm: 'CRM number', requestType: 'Request type', requestDate: 'Request date',
    templateTitle: 'Prepare files', templateHelp: 'Download and complete the card request template before uploading it.', download: 'Download Excel template',
    application: 'RFID template', applicationHelp: 'Excel file (.xlsx), maximum 3 MB.', quantity: 'Number of new cards', payment: 'Payment proof', paymentHelp: 'Required on every request, maximum 5 MB.',
    submit: 'Submit request', submitting: 'Submitting…', success: 'Request submitted', requestNumber: 'Request number', trackingCode: 'Tracking code',
    saveCode: 'Save both details to follow your request.', trackTitle: 'Track request', trackIntro: 'Enter the request number and tracking code you received.',
    track: 'View status', tracking: 'Searching…', status: 'Request status', emptyTrack: 'The status will appear here after a successful search.',
    error: 'The request could not be processed. Check the marked fields and try again.', required: 'Required.',
    timeline: 'Progress', notifications: 'Notifications', unread: 'New', markRead: 'Mark as read',
    types: { new_card: 'New card', damaged_card: 'Damaged card', system_error_card: 'System error card', lost_card: 'Lost card' },
  },
} as const

const apiBase = apiBaseUrl(import.meta.env.VITE_API_URL)

function Field({ label, name, type = 'text', required, error }: { label: string; name: string; type?: string; required?: boolean; error?: string }) {
  const errorId = `${name}-error`
  return <label className="field"><span>{label}{required && <span aria-hidden="true"> *</span>}</span><input name={name} type={type} required={required} aria-invalid={Boolean(error)} aria-describedby={error ? errorId : undefined} />{error && <small id={errorId}>{error}</small>}</label>
}

function FileField({ label, name, accept, help, error, required = true }: { label: string; name: string; accept: string; help: string; error?: string; required?: boolean }) {
  const errorId = `${name}-error`
  return <label className="upload-field"><span className="upload-icon"><Upload size={18} /></span><span><strong>{label}{required && ' *'}</strong><small>{help}</small></span><input name={name} type="file" required={required} accept={accept} aria-invalid={Boolean(error)} aria-describedby={error ? errorId : undefined} />{error && <em id={errorId}>{error}</em>}</label>
}

function App() {
  const [locale, setLocale] = useState<Locale>('id')
  const [view, setView] = useState<View>('request')
  const [requestType, setRequestType] = useState('new_card')
  const [paymentRequired, setPaymentRequired] = useState(true)
  const [busy, setBusy] = useState(false)
  const [idempotencyKey, setIdempotencyKey] = useState(() => crypto.randomUUID())
  const [result, setResult] = useState<Record<string, unknown> | null>(null)
  const [trackingCredentials, setTrackingCredentials] = useState<{ request_number: string; tracking_code: string } | null>(null)
  const [errors, setErrors] = useState<Errors>({})
  const t = copy[locale]
  const typeEntries = useMemo(() => Object.entries(t.types), [t])

  useEffect(() => { document.documentElement.lang = locale }, [locale])
  const fieldError = (name: string) => errors[name]?.[0]
  const changeView = (next: View) => { setView(next); setResult(null); setTrackingCredentials(null); setErrors({}) }

  const refreshRequirements = async (form: HTMLFormElement) => {
    const values = new FormData(form)
    if (values.get('request_type') !== 'new_card') { setPaymentRequired(true); return }
    const query = new URLSearchParams({ crm_number: String(values.get('crm_number') ?? ''), institution_name: String(values.get('institution_name') ?? ''), requested_quantity: String(values.get('requested_quantity') ?? '1') })
    try {
      const response = await fetch(`${apiBase}/api/public/v1/request-requirements?${query.toString()}`, { headers: { Accept: 'application/json' } })
      if (response.ok) { const payload = await response.json() as { data?: { payment_required?: boolean } }; setPaymentRequired(payload.data?.payment_required !== false) }
    } catch { /* submit remains server-authoritative */ }
  }

  const handleRequestChange = (event: FormEvent<HTMLFormElement>) => { void refreshRequirements(event.currentTarget) }

  const submitRequest = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault(); setBusy(true); setErrors({})
    const form = new FormData(event.currentTarget)
    form.set('request_type', requestType)
    try {
      const response = await fetch(`${apiBase}/api/public/v1/requests`, { method: 'POST', headers: { Accept: 'application/json', 'Idempotency-Key': idempotencyKey }, body: form })
      const payload = await response.json() as { data?: Record<string, unknown>; errors?: Errors }
      if (!response.ok) { setErrors(payload.errors ?? { form: [t.error] }); return }
      setResult(payload.data ?? payload); setIdempotencyKey(crypto.randomUUID())
    } catch { setErrors({ form: [t.error] }) } finally { setBusy(false) }
  }

  const trackRequest = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault(); setBusy(true); setErrors({})
    try {
      const response = await fetch(`${apiBase}/api/public/v1/tracking`, { method: 'POST', headers: { Accept: 'application/json', 'Content-Type': 'application/json' }, body: JSON.stringify(Object.fromEntries(new FormData(event.currentTarget))) })
      const payload = await response.json() as { data?: Record<string, unknown>; errors?: Errors }
      if (!response.ok) { setResult(null); setErrors(payload.errors ?? { form: [t.error] }); return }
      setResult(payload.data ?? payload)
      setTrackingCredentials(Object.fromEntries(new FormData(event.currentTarget)) as { request_number: string; tracking_code: string })
    } catch { setResult(null); setErrors({ form: [t.error] }) } finally { setBusy(false) }
  }

  const markNotificationRead = async (notificationId: number) => {
    if (!trackingCredentials || !result) return
    await fetch(`${apiBase}/api/public/v1/tracking/notifications/read`, { method: 'POST', headers: { Accept: 'application/json', 'Content-Type': 'application/json' }, body: JSON.stringify({ ...trackingCredentials, notification_id: notificationId }) })
    const notifications = Array.isArray(result.notifications) ? result.notifications as PortalNotification[] : []
    setResult({ ...result, notifications: notifications.map((notification) => notification.id === notificationId ? { ...notification, read_at: new Date().toISOString() } : notification) })
  }

  const timeline = result && Array.isArray(result.timeline) ? result.timeline as PortalTimeline[] : []
  const notifications = result && Array.isArray(result.notifications) ? result.notifications as PortalNotification[] : []

  return <div className="portal-shell">
    <header className="topbar"><a className="brand" href="#main"><span className="brand-mark"><CreditCard size={20} /></span><span>{t.brand}</span></a><div className="language"><Globe2 size={17} /><button className={locale === 'id' ? 'active' : ''} onClick={() => setLocale('id')}>ID</button><span>/</span><button className={locale === 'en' ? 'active' : ''} onClick={() => setLocale('en')}>EN</button></div></header>
    <main id="main"><section className="hero-block"><p className="eyebrow">{t.eyebrow}</p><h1>{view === 'request' ? t.title : t.trackTitle}</h1><p className="lede">{view === 'request' ? t.intro : t.trackIntro}</p><div className="segmented" role="navigation"><button className={view === 'request' ? 'selected' : ''} onClick={() => changeView('request')}>{t.requestTab}</button><button className={view === 'tracking' ? 'selected' : ''} onClick={() => changeView('tracking')}><Search size={17} /> {t.trackingTab}</button></div></section>
      {view === 'request' ? <form className="content-grid" onSubmit={submitRequest} onChange={handleRequestChange} noValidate><div className="form-stack">
        {errors.form && <div className="error" role="alert">{errors.form[0]}</div>}
        <section className="panel"><div className="section-heading"><span>01</span><div><h2>{t.customerTitle}</h2><p>{t.customerHelp}</p></div></div><div className="fields two-col"><Field label={t.institution} name="institution_name" required error={fieldError('institution_name')} /><Field label={t.crm} name="crm_number" required error={fieldError('crm_number')} /><label className="field"><span>{t.requestType} *</span><select name="request_type" value={requestType} onChange={(event) => { setRequestType(event.target.value); setPaymentRequired(event.target.value !== 'new_card') }}>{typeEntries.map(([value, label]) => <option value={value} key={value}>{label}</option>)}</select></label><Field label={t.requestDate} name="request_date" type="date" required error={fieldError('request_date')} />{requestType === 'new_card' && <Field label={t.quantity} name="requested_quantity" type="number" required error={fieldError('requested_quantity')} />}</div></section>
        <section className="panel"><div className="section-heading"><span>02</span><div><h2>{t.templateTitle}</h2><p>{t.templateHelp}</p></div></div><a className="template-link" href={`${apiBase}/api/public/v1/request-template`}><Download size={18} /> {t.download}<FileSpreadsheet size={17} /></a><div className="upload-grid"><FileField label={t.application} name="application_file" accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" help={t.applicationHelp} error={fieldError('application_file')} /><FileField label={t.payment} name="payment_proof" accept=".pdf,image/jpeg,image/png" help={t.paymentHelp} error={fieldError('payment_proof')} required={paymentRequired} /></div></section>
      </div><aside className="submit-card"><div className="secure-note"><ShieldCheck size={19} /><span>{t.trust}</span></div>{result ? <div className="success" role="status"><CheckCircle2 size={28} /><h2>{t.success}</h2><p>{t.saveCode}</p><dl><div><dt>{t.requestNumber}</dt><dd>{String(result.request_number ?? '—')}</dd></div><div><dt>{t.trackingCode}</dt><dd>{String(result.tracking_code ?? '—')}</dd></div></dl></div> : <button className="button primary full" disabled={busy} type="submit">{busy ? t.submitting : t.submit}<ArrowRight size={18} /></button>}</aside></form> : <section className="tracking-layout"><form className="panel tracking-form" onSubmit={trackRequest}>{errors.form && <p className="error" role="alert">{errors.form[0]}</p>}<Field label={t.requestNumber} name="request_number" required error={fieldError('request_number')} /><Field label={t.trackingCode} name="tracking_code" required error={fieldError('tracking_code')} /><button className="button primary full" disabled={busy} type="submit"><Search size={18} />{busy ? t.tracking : t.track}</button></form><aside className="panel status-card"><div className="status-icon"><Search size={22} /></div><h2>{t.status}</h2>{result ? <div className="tracking-result"><div className="tracking-summary"><span>{String(result.request_number ?? '—')}</span><strong>{String(result.status ?? '—')}</strong></div><section aria-labelledby="timeline-title"><h3 id="timeline-title">{t.timeline}</h3>{timeline.length ? <ol className="timeline">{timeline.map((item, index) => <li key={`${String(item.at)}-${index}`}><span className="timeline-dot" aria-hidden="true" /><div><strong>{String(item.status ?? '—')}</strong>{item.reason && <p>{String(item.reason)}</p>}<time>{item.at ? new Date(String(item.at)).toLocaleString(locale === 'id' ? 'id-ID' : 'en-US') : '—'}</time></div></li>)}</ol> : <p>{t.emptyTrack}</p>}</section><section aria-labelledby="notifications-title" className="notification-list"><div className="result-heading"><h3 id="notifications-title">{t.notifications}</h3><span>{notifications.filter((item) => !item.read_at).length} {t.unread}</span></div>{notifications.length ? notifications.map((item) => <article key={String(item.id)} className={item.read_at ? 'notification' : 'notification unread'}><div><strong>{String(item.type).replaceAll('.', ' ')}</strong><p>{item.data && typeof item.data === 'object' && 'status' in item.data ? `Status: ${String((item.data as { status?: unknown }).status)}` : t.notifications}</p></div>{!item.read_at && <button type="button" onClick={() => markNotificationRead(Number(item.id))}>{t.markRead}</button>}</article>) : <p>{t.emptyTrack}</p>}</section></div> : <p>{t.emptyTrack}</p>}</aside></section>}
    </main><footer><span>{t.brand}</span><span>© {new Date().getFullYear()}</span></footer>
  </div>
}

export default App
