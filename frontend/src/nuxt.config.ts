export default defineNuxtConfig({
  devtools: { enabled: true },
  ssr: true,
  modules: [
    '@nuxt/ui',
    '@pinia/nuxt'
  ],
  css: ['~/assets/css/main.css'],
  runtimeConfig: {
    // Private keys (only available on server-side)
    // Public keys (exposed to client-side)
    public: {
      apiBaseUrl: process.env.BACKEND_API_URL || '/api',
      backendUrl: process.env.BACKEND_URL || process.env.BACKEND_API_URL,
      backendHost: process.env.BACKEND_HOST || 'localhost',
      backendPort: process.env.BACKEND_PORT || '9018'
    }
  },
  nitro: {
    devProxy: {
      '/api': {
        target: 'http://localhost:9018/api',
        changeOrigin: true,
        prependPath: true,
      }
    }
  },
  colorMode: {
    preference: 'light', // Default to light mode
    fallback: 'light',
    hid: 'nuxt-color-mode-script',
    globalName: '__NUXT_COLOR_MODE__',
    componentName: 'ColorScheme',
    classPrefix: '',
    classSuffix: '',
    storageKey: 'nuxt-color-mode'
  },
  // Disable problematic nuxt-icon server bundle
  icon: {
    serverBundle: false
  },
  // Alternative: disable icon module entirely if still causing issues
  // icon: false
})