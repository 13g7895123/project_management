export const useTheme = () => {
  const colorMode = useColorMode()
  const themeStore = useThemeStore()
  const websiteSettingsStore = useWebsiteSettingsStore()

  // Computed properties for reactive theme state
  const isDark = computed(() => colorMode.value === 'dark')
  const isLight = computed(() => colorMode.value === 'light')
  const isSystem = computed(() => colorMode.preference === 'system')

  // Theme toggle function
  const toggleTheme = () => {
    // Check if dark mode is enabled in website settings
    const websiteSettingsStore = useWebsiteSettingsStore()
    if (!websiteSettingsStore.enableDarkMode) {
      return // Don't allow theme toggle if disabled
    }
    
    const newMode = colorMode.value === 'dark' ? 'light' : 'dark'
    setTheme(newMode)
  }

  // Set specific theme
  const setTheme = (mode) => {
    if (!websiteSettingsStore.enableDarkMode && mode === 'dark') {
      return // Don't allow setting dark mode if disabled
    }
    
    // Update Nuxt color mode
    colorMode.preference = mode
    
    // Update website settings store and save to both API and localStorage
    websiteSettingsStore.themeMode = mode
    websiteSettingsStore.saveSettings()
    
    // Also manually save to the Nuxt color mode storage for immediate persistence
    if (process.client) {
      localStorage.setItem('website-theme-mode', mode)
      
      // Add smooth transition
      document.documentElement.classList.add('theme-transition')
      
      // Apply theme immediately
      const html = document.documentElement
      if (mode === 'dark' || (mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        html.classList.add('dark')
      } else {
        html.classList.remove('dark')
      }
      
      setTimeout(() => {
        document.documentElement.classList.remove('theme-transition')
      }, 300)
    }
  }

  // Set primary color with immediate visual feedback
  const setPrimaryColor = (color) => {
    themeStore.setPrimaryColor(color)
  }

  // Initialize theme on first load
  const initializeTheme = () => {
    if (process.client) {
      // Load from multiple sources with priority order:
      // 1. Nuxt color mode storage (most recent user choice)
      // 2. Website settings store
      // 3. Default to system
      const savedThemeMode = localStorage.getItem('website-theme-mode')
      const preferredMode = savedThemeMode || websiteSettingsStore.themeMode || 'system'
      
      // Set the theme mode if different
      if (preferredMode !== colorMode.preference) {
        colorMode.preference = preferredMode
      }
      
      // Update website settings store to match
      if (websiteSettingsStore.themeMode !== preferredMode) {
        websiteSettingsStore.themeMode = preferredMode
      }
      
      // Initialize primary color
      themeStore.initializePrimaryColor()
      
      // Ensure theme class is properly set immediately
      nextTick(() => {
        const html = document.documentElement
        const currentTheme = colorMode.value
        
        // Remove both classes first
        html.classList.remove('dark', 'light')
        
        // Add appropriate class
        if (currentTheme === 'dark') {
          html.classList.add('dark')
        } else {
          html.classList.add('light')
        }
        
        // Apply website settings theme as well
        websiteSettingsStore.applyThemeSettings()
      })
    }
  }

  // Watch for system theme changes
  if (process.client) {
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
    mediaQuery.addListener(() => {
      if (colorMode.preference === 'system') {
        // Force a re-evaluation
        nextTick(() => {
          document.body.offsetHeight
        })
      }
    })
  }

  return {
    // State
    isDark,
    isLight,
    isSystem,
    colorMode,
    primaryColor: computed(() => themeStore.primaryColor),
    
    // Methods
    toggleTheme,
    setTheme,
    setPrimaryColor,
    initializeTheme
  }
}