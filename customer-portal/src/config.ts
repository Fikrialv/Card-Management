export function apiBaseUrl(rawValue: string | undefined, fallback = typeof window !== 'undefined' ? window.location.origin : 'http://127.0.0.1:8000'): string {
  const value = rawValue?.trim() || fallback
  const url = new URL(value)

  if (!['http:', 'https:'].includes(url.protocol) || url.username || url.password || url.pathname !== '/' || url.search || url.hash) {
    throw new Error('VITE_API_URL must be an HTTP(S) origin without credentials, path, query, or fragment')
  }

  return url.origin
}
