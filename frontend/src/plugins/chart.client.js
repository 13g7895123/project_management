/**
 * Chart.js plugin for client-side only
 */
export default defineNuxtPlugin(async () => {
  // Only run on client side
  if (process.client) {
    try {
      console.log('Loading Chart.js plugin...')
      
      // Import Chart.js with auto-registration
      const chartJsModule = await import('chart.js/auto')
      let Chart = chartJsModule.default || chartJsModule.Chart || chartJsModule
      
      console.log('Chart.js auto import result:', !!Chart)
      
      // If auto import doesn't work, try manual registration
      if (!Chart) {
        console.log('Auto import failed, trying manual registration...')
        
        const {
          Chart: ChartClass,
          CategoryScale,
          LinearScale,
          PointElement,
          LineElement,
          Title,
          Tooltip,
          Legend,
          Filler
        } = await import('chart.js')
        
        ChartClass.register(
          CategoryScale,
          LinearScale,
          PointElement,
          LineElement,
          Title,
          Tooltip,
          Legend,
          Filler
        )
        
        Chart = ChartClass
      }
      
      if (Chart) {
        console.log('Chart.js loaded successfully and provided to Nuxt app')
      } else {
        console.error('Chart.js could not be loaded')
      }
      
      // Make Chart.js globally available
      return {
        provide: {
          Chart
        }
      }
    } catch (error) {
      console.error('Failed to load Chart.js in plugin:', error)
      
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