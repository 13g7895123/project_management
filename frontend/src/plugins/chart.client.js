/**
 * Chart.js plugin for client-side only
 */
export default defineNuxtPlugin(async () => {
  // Only run on client side
  if (process.client) {
    try {
      // Import Chart.js with all components
      const chartModule = await import('chart.js')
      const { Chart, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, Filler } = chartModule
      
      // Register all required components
      Chart.register(
        CategoryScale,
        LinearScale,
        PointElement,
        LineElement,
        Title,
        Tooltip,
        Legend,
        Filler
      )
      
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