import { fireEvent, render, screen } from '@testing-library/react'
import { describe, expect, it } from 'vitest'
import App from './App'

describe('Customer Portal', () => {
  it('uses Indonesian by default with the agreed request fields and uploads', () => {
    render(<App />)
    expect(screen.getByRole('heading', { name: /Pengajuan kartu RFID/i })).toBeInTheDocument()
    expect(screen.getByLabelText(/Nomor CRM/i)).toBeInTheDocument()
    expect(screen.getByRole('option', { name: /Kartu error system/i })).toBeInTheDocument()
    expect(screen.queryByText(/Data kendaraan/i)).not.toBeInTheDocument()
    expect(screen.getByRole('link', { name: /Unduh template Excel/i })).toBeInTheDocument()
    expect(screen.getByLabelText(/Template RFID/i)).toHaveAttribute('accept', expect.stringContaining('.xlsx'))
    expect(screen.getByLabelText(/Bukti bayar/i)).toBeRequired()
    expect(screen.getByLabelText(/Jumlah kartu baru/i)).toBeRequired()
  })

  it('switches interface language to English', () => {
    render(<App />)
    fireEvent.click(screen.getByRole('button', { name: 'EN' }))
    expect(screen.getByRole('heading', { name: /RFID card request/i })).toBeInTheDocument()
  })

  it('opens tracking with request number and tracking code', () => {
    render(<App />)
    fireEvent.click(screen.getByRole('button', { name: /Lacak pengajuan/i }))
    expect(screen.getByLabelText(/Nomor pengajuan/i)).toBeInTheDocument()
    expect(screen.getByLabelText(/Kode pelacakan/i)).toBeInTheDocument()
  })
})
