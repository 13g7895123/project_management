export const useTheme = () => {
  const colorMode = useColorMode()
  const themeStore = useThemeStore()

  // Computed properties for reactive theme state
  const isDark = computed(() => colorMode.value === 'dark')
  const isLight = computed(() => colorMode.value === 'light')
  const isSystem = computed(() => colorMode.preference === 'system')

  // Theme toggle function
  const toggleTheme = () => {
    const newMode = colorMode.value === 'dark' ? 'light' : 'dark'
    setTheme(newMode)
  }

  // Set specific theme
  const setTheme = (mode) => {
    colorMode.preference = mode
    
    // Add smooth transition
    if (process.client) {
      document.documentElement.classList.add('theme-transition')
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
      // Initialize primary color
      themeStore.initializePrimaryColor()
      
      // Ensure theme class is properly set
      nextTick(() => {
        const html = document.documentElement
        const currentTheme = colorMode.value
        
        if (currentTheme === 'dark') {
          html.classList.add('dark')
        } else {
          html.classList.remove('dark')
        }
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