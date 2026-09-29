import { describe, expect, it } from 'vitest'
import { apiBaseUrl } from './config'

describe('apiBaseUrl', () => {
  it('uses the current portal origin when the variable is absent', () => {
    expect(apiBaseUrl(undefined)).toBe(window.location.origin)
  })

  it('normalizes a valid deployment origin', () => {
    expect(apiBaseUrl('https://api.example.test/')).toBe('https://api.example.test')
  })

  it.each(['javascript:alert(1)', 'https://user:secret@example.test', 'https://example.test/api', 'https://example.test?secret=x'])(
    'rejects unsafe or malformed public configuration: %s',
    (value) => expect(() => apiBaseUrl(value)).toThrow(/VITE_API_URL/),
  )
})
