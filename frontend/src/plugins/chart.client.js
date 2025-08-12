/**
 * Chart.js plugin for client-side only
 */
export default defineNuxtPlugin(async () => {
  // Only run on client side
  if (process.client) {
    try {
      // Dynamically import Chart.js
      const { Chart, registerables } = await import('chart.js')
      Chart.register(...registerables)
      
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