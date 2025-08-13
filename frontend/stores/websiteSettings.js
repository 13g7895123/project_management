export const useWebsiteSettingsStore = defineStore('websiteSettings', () => {
  const { getSettings, updateSettings, resetDefaults: apiResetDefaults } = useWebsiteSettingsApi()
  // Website basic settings
  const websiteName = ref('專案管理系統')
  const websiteSecondaryName = ref('Project Management')
  const websiteTitle = ref('專案管理系統')
  const showLogo = ref(false)
  const logoUrl = ref('')
  const faviconUrl = ref('/favicon.ico')
  
  // Feature toggles
  const enableMultilingual = ref(true)
  const enableSearch = ref(true)
  const enableNotifications = ref(true)
  const showFooter = ref(true)
  
  // Theme settings integration
  const enableDarkMode = ref(true)
  
  // Load settings from API or localStorage fallback
  const loadSettings = async () => {
    if (process.client) {
      try {
        // Try to load from API first
        const response = await getSettings()
        if (response.success) {
          const apiSettings = response.data
          
          // Map API settings to store
          websiteName.value = apiSettings.website_primary_name || '專案管理系統'
          websiteSecondaryName.value = apiSettings.website_secondary_name || 'Project Management'
          websiteTitle.value = apiSettings.website_primary_name || '專案管理系統'
          logoUrl.value = apiSettings.logo_data || ''
          showLogo.value = !!apiSettings.logo_data
          faviconUrl.value = apiSettings.favicon_data || '/favicon.ico'
          
          enableMultilingual.value = apiSettings.multilingual_enabled !== undefined ? apiSettings.multilingual_enabled : false
          enableSearch.value = apiSettings.search_enabled !== undefined ? apiSettings.search_enabled : true
          enableNotifications.value = apiSettings.notifications_enabled !== undefined ? apiSettings.notifications_enabled : true
          showFooter.value = apiSettings.footer_enabled !== undefined ? apiSettings.footer_enabled : true
          enableDarkMode.value = apiSettings.dark_mode_enabled !== undefined ? apiSettings.dark_mode_enabled : true
          
          // Save to localStorage as backup
          saveToLocalStorage()
          
          // Update document elements
          updateDocumentTitle()
          updateFavicon()
          return
        }
      } catch (error) {
        console.warn('Failed to load settings from API, falling back to localStorage:', error)
      }

      // Fallback to localStorage
      const savedSettings = localStorage.getItem('website-settings')
      if (savedSettings) {
        try {
          const settings = JSON.parse(savedSettings)
          
          websiteName.value = settings.websiteName || '專案管理系統'
          websiteSecondaryName.value = settings.websiteSecondaryName || 'Project Management'
          websiteTitle.value = settings.websiteTitle || '專案管理系統'
          showLogo.value = settings.showLogo || false
          logoUrl.value = settings.logoUrl || ''
          faviconUrl.value = settings.faviconUrl || '/favicon.ico'
          
          enableMultilingual.value = settings.enableMultilingual !== undefined ? settings.enableMultilingual : false
          enableSearch.value = settings.enableSearch !== undefined ? settings.enableSearch : true
          enableNotifications.value = settings.enableNotifications !== undefined ? settings.enableNotifications : true
          showFooter.value = settings.showFooter !== undefined ? settings.showFooter : true
          enableDarkMode.value = settings.enableDarkMode !== undefined ? settings.enableDarkMode : true
          
          // Update document title
          updateDocumentTitle()
          
          // Update favicon
          updateFavicon()
        } catch (error) {
          console.error('Error loading website settings from localStorage:', error)
        }
      }
    }
  }
  
  // Save settings to localStorage only
  const saveToLocalStorage = () => {
    if (process.client) {
      const settings = {
        websiteName: websiteName.value,
        websiteSecondaryName: websiteSecondaryName.value,
        websiteTitle: websiteTitle.value,
        showLogo: showLogo.value,
        logoUrl: logoUrl.value,
        faviconUrl: faviconUrl.value,
        enableMultilingual: enableMultilingual.value,
        enableSearch: enableSearch.value,
        enableNotifications: enableNotifications.value,
        showFooter: showFooter.value,
        enableDarkMode: enableDarkMode.value
      }
      
      localStorage.setItem('website-settings', JSON.stringify(settings))
    }
  }

  // Save settings to API and localStorage
  const saveSettings = async () => {
    if (process.client) {
      try {
        // Prepare API payload
        const apiPayload = {
          website_primary_name: websiteName.value || '',
          website_secondary_name: websiteSecondaryName.value || '',
          logo_data: logoUrl.value || '',
          favicon_data: faviconUrl.value || '',
          multilingual_enabled: Boolean(enableMultilingual.value),
          search_enabled: Boolean(enableSearch.value),
          notifications_enabled: Boolean(enableNotifications.value),
          footer_enabled: Boolean(showFooter.value),
          dark_mode_enabled: Boolean(enableDarkMode.value)
        }

        // Save to API
        const response = await updateSettings(apiPayload)
        if (response.success) {
          console.log('Settings saved to API successfully')
        } else {
          console.warn('Failed to save settings to API:', response.message)
        }
      } catch (error) {
        console.warn('API save failed, saving to localStorage only:', error)
      }

      // Always save to localStorage as backup
      saveToLocalStorage()
      
      // Update document title
      updateDocumentTitle()
      
      // Update favicon
      updateFavicon()
      
      // Trigger a global event for other components
      window.dispatchEvent(new CustomEvent('website-settings-changed', { 
        detail: {
          websiteName: websiteName.value,
          websiteSecondaryName: websiteSecondaryName.value,
          websiteTitle: websiteTitle.value,
          showLogo: showLogo.value,
          logoUrl: logoUrl.value,
          faviconUrl: faviconUrl.value,
          enableMultilingual: enableMultilingual.value,
          enableSearch: enableSearch.value,
          enableNotifications: enableNotifications.value,
          showFooter: showFooter.value,
          enableDarkMode: enableDarkMode.value
        }
      }))
    }
  }
  
  // Update document title
  const updateDocumentTitle = (pageTitle = null) => {
    if (process.client) {
      // Use page title if provided, otherwise use website title
      const titleToUse = pageTitle ? `${pageTitle} - ${websiteTitle.value}` : websiteTitle.value
      document.title = titleToUse
    }
  }
  
  // Update favicon
  const updateFavicon = () => {
    if (process.client) {
      let favicon = document.querySelector('link[rel="icon"]')
      if (!favicon) {
        favicon = document.createElement('link')
        favicon.rel = 'icon'
        document.head.appendChild(favicon)
      }
      favicon.href = faviconUrl.value
    }
  }
  
  // Upload logo file
  const uploadLogo = async (file) => {
    return new Promise((resolve, reject) => {
      if (!file) {
        reject(new Error('No file provided'))
        return
      }
      
      // Create FileReader to convert file to base64 for demo purposes
      // In production, you would upload to a server
      const reader = new FileReader()
      reader.onload = (e) => {
        const dataURL = e.target.result
        logoUrl.value = dataURL
        showLogo.value = true
        saveSettings()
        resolve(dataURL)
      }
      reader.onerror = reject
      reader.readAsDataURL(file)
    })
  }
  
  // Upload favicon file
  const uploadFavicon = async (file) => {
    return new Promise((resolve, reject) => {
      if (!file) {
        reject(new Error('No file provided'))
        return
      }
      
      const reader = new FileReader()
      reader.onload = (e) => {
        const dataURL = e.target.result
        faviconUrl.value = dataURL
        saveSettings()
        resolve(dataURL)
      }
      reader.onerror = reject
      reader.readAsDataURL(file)
    })
  }
  
  // Reset to defaults
  const resetToDefaults = async () => {
    try {
      // Reset via API first
      const response = await apiResetDefaults()
      if (response.success) {
        // Load the reset settings from API
        await loadSettings()
        return
      }
    } catch (error) {
      console.warn('API reset failed, using local defaults:', error)
    }

    // Fallback to local reset
    websiteName.value = '專案管理系統'
    websiteSecondaryName.value = 'Project Management'
    websiteTitle.value = '專案管理系統'
    showLogo.value = false
    logoUrl.value = ''
    faviconUrl.value = '/favicon.ico'
    enableMultilingual.value = false
    enableSearch.value = true
    enableNotifications.value = true
    showFooter.value = true
    enableDarkMode.value = true
    
    await saveSettings()
  }
  
  // Computed properties for easy access
  const displayName = computed(() => {
    return showLogo.value && logoUrl.value ? '' : websiteName.value
  })
  
  const displaySecondaryName = computed(() => {
    return showLogo.value && logoUrl.value ? '' : websiteSecondaryName.value
  })
  
  // Initialize on store creation
  if (process.client) {
    loadSettings()
  }
  
  return {
    // State
    websiteName,
    websiteSecondaryName,
    websiteTitle,
    showLogo,
    logoUrl,
    faviconUrl,
    enableMultilingual,
    enableSearch,
    enableNotifications,
    showFooter,
    enableDarkMode,
    
    // Computed
    displayName,
    displaySecondaryName,
    
    // Methods
    loadSettings,
    saveSettings,
    saveToLocalStorage,
    updateDocumentTitle,
    updateFavicon,
    uploadLogo,
    uploadFavicon,
    resetToDefaults
  }
})