/**
 * Chart.js plugin for client-side only
 */
export default defineNuxtPlugin(async () => {
  // Only run on client side
  if (process.client) {
    try {
      console.log('Loading Chart.js plugin...')
      
      // Use a more reliable import method
      let Chart = null
      
      try {
        // Try importing from chart.js/auto first (recommended)
        const chartModule = await import('chart.js/auto')
        Chart = chartModule.default || chartModule.Chart || chartModule
        console.log('Chart.js/auto import successful:', !!Chart)
      } catch (autoError) {
        console.warn('chart.js/auto import failed, trying alternative:', autoError.message)
        
        try {
          // Fallback to manual registration
          const chartCore = await import('chart.js')
          const Chart_Class = chartCore.Chart || chartCore.default?.Chart
          
          if (Chart_Class) {
            // Register required components
            const {
              CategoryScale,
              LinearScale,
              PointElement,
              LineElement,
              Title,
              Tooltip,
              Legend,
              Filler
            } = chartCore
            
            Chart_Class.register(
              CategoryScale,
              LinearScale,
              PointElement,
              LineElement,
              Title,
              Tooltip,
              Legend,
              Filler
            )
            
            Chart = Chart_Class
            console.log('Manual Chart.js registration successful')
          }
        } catch (manualError) {
          console.error('Manual Chart.js registration also failed:', manualError.message)
        }
      }
      
      if (Chart) {
        console.log('Chart.js loaded successfully and provided to Nuxt app')
        
        // Make Chart.js globally available
        return {
          provide: {
            Chart
          }
        }
      } else {
        console.error('Chart.js could not be loaded from any method')
        
        return {
          provide: {
            Chart: null
          }
        }
      }
    } catch (error) {
      console.error('Critical error in Chart.js plugin:', error)
      
      return {
        provide: {
          Chart: null
        }
      }
    }
  }
  
  // Server-side: provide null
  return {
    provide: {
      Chart: null
    }
  }
})