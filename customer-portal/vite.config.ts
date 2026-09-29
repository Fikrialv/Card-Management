import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'
import { defineConfig } from 'vitest/config'
import type { ProxyOptions } from 'vite'
import { fileURLToPath, URL } from 'node:url'

const backendProxy: ProxyOptions = {
  target: 'http://127.0.0.1:8000',
  changeOrigin: false,
  configure: (proxy) => {
    proxy.on('proxyReq', (proxyReq, request) => {
      const host = request.headers.host ?? '127.0.0.1:5174'
      proxyReq.setHeader('host', host)
      proxyReq.setHeader('x-forwarded-host', host)
      proxyReq.setHeader('x-forwarded-proto', host.endsWith('ngrok-free.dev') ? 'https' : 'http')
    })
  },
}

// https://vite.dev/config/
export default defineConfig({
  plugins: [react(), tailwindcss()],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },
  server: {
    port: 5174,
    allowedHosts: ['ravioli-partly-fried.ngrok-free.dev'],
    proxy: {
      '/api': backendProxy,
      '/login': backendProxy,
      '/logout': backendProxy,
      '/dashboard': backendProxy,
      '/requests': backendProxy,
      '/request-evidence': backendProxy,
      '/inventory': backendProxy,
      '/procurement-notes': backendProxy,
      '/reports': backendProxy,
      '/profile': backendProxy,
      '/locale': backendProxy,
      '/notifications': backendProxy,
      '/audit': backendProxy,
      '/build': backendProxy,
    },
  },
  test: {
    include: ['src/**/*.{test,spec}.{ts,tsx}'],
    exclude: ['e2e/**', 'node_modules/**'],
  },
})
