/**
 * Chart.js plugin for client-side only
 */
import { Chart, registerables } from 'chart.js'

export default defineNuxtPlugin(() => {
  // Only run on client side
  if (process.client) {
    try {
      console.log('Loading Chart.js plugin...')
      
      // Register all Chart.js components
      Chart.register(...registerables)
      console.log('Chart.js registered successfully')
      
      // Make Chart.js globally available
      return {
        provide: {
          Chart
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