/**
 * Chart.js plugin for client-side only
 */
export default defineNuxtPlugin(async () => {
  // Only run on client side
  if (process.client) {
    try {
      // Import Chart.js with auto-registration
      const chartJsModule = await import('chart.js/auto')
      const Chart = chartJsModule.default || chartJsModule.Chart
      
      // If auto import doesn't work, try manual registration
      if (!Chart) {
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
      
      // Make Chart.js globally available
      return {
        provide: {
          Chart
        }
      }
    } catch (error) {
      console.error('Failed to load Chart.js:', error)
      
      return {
        provide: {
          Chart: null
        }
      }
    }
  }
  
  return {
    provide: {
      Chart: null
    }
  }
})