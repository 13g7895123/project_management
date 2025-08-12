export default defineNuxtPlugin(() => {
  // Initialize website settings on client side
  const websiteSettingsStore = useWebsiteSettingsStore()
  
  // Load settings from localStorage
  websiteSettingsStore.loadSettings()
  
  // Set up page title management
  const router = useRouter()
  
  // Listen for settings changes and update document properties
  if (process.client) {
    window.addEventListener('website-settings-changed', (event) => {
      const settings = event.detail
      
      // Update favicon
      let favicon = document.querySelector('link[rel="icon"]')
      if (!favicon) {
        favicon = document.createElement('link')
        favicon.rel = 'icon'
        document.head.appendChild(favicon)
      }
      favicon.href = settings.faviconUrl
      
      // Force title update based on current route
      nextTick(() => {
        const { usePageTitle } = await import('~/composables/usePageTitle')
        usePageTitle()
      })
    })
    
    // Handle route-based title updates
    router.afterEach(() => {
      nextTick(() => {
        const pageTitles = {
          '/': '儀表板',
          '/dashboard': '儀表板',
          '/profile': '個人檔案',
          '/settings': '設定',
          '/settings/theme': '主題設定',
          '/settings/website': '網站設定',
          '/settings/ui': '介面設定',
          '/settings/users': '用戶管理',
          '/clients': '業主管理',
          '/clients/create': '新增業主',
          '/projects': '專案管理',
          '/projects/create': '新增專案',
          '/help': '幫助中心'
        }
        
        const currentPath = router.currentRoute.value.path
        const pageTitle = pageTitles[currentPath]
        
        if (pageTitle) {
          websiteSettingsStore.updateDocumentTitle(pageTitle)
        } else {
          websiteSettingsStore.updateDocumentTitle()
        }
      })
    })
  }
})