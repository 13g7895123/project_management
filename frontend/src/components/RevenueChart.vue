<template>
  <div class="h-64">
    <div v-if="loading" class="h-64 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
      <div class="text-center">
        <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]" role="status">
          <span class="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]">載入中...</span>
        </div>
        <p class="mt-2 text-gray-500 dark:text-gray-400">載入收入趨勢...</p>
      </div>
    </div>
    <div v-else-if="error" class="h-64 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
      <div class="text-center">
        <svg class="mx-auto h-12 w-12 text-red-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-red-500 dark:text-red-400 text-sm">{{ error }}</p>
        <button @click="initChart" class="mt-2 text-primary-600 hover:text-primary-700 text-sm underline">重試</button>
      </div>
    </div>
    <canvas v-else ref="chartCanvas" class="w-full h-full"></canvas>
  </div>
</template>

<script setup>
const props = defineProps({
  revenueData: {
    type: Array,
    default: () => []
  }
})

const chartCanvas = ref(null)
const chartInstance = ref(null)
const loading = ref(true)
const error = ref(null)
const { formatChartCurrency } = useCurrency()

const initChart = async () => {
  loading.value = true
  error.value = null
  
  // Wait for DOM to be ready with a small delay
  await nextTick()
  await new Promise(resolve => setTimeout(resolve, 100))
  
  if (!chartCanvas.value) {
    console.error('Canvas element not found')
    error.value = 'Canvas element not found'
    loading.value = false
    return
  }
  
  if (!props.revenueData || props.revenueData.length === 0) {
    console.warn('No revenue data found, using fallback data for demo')
    error.value = '暫無收入數據'
    loading.value = false
    return
  }

  try {
    // Only run on client side
    if (process.client && typeof window !== 'undefined') {
      // Try to get Chart.js from the plugin first
      const nuxtApp = useNuxtApp()
      let Chart = nuxtApp.$Chart
      
      // If plugin didn't provide Chart, try direct import
      if (!Chart) {
        console.log('Chart.js not available from plugin, trying direct import')
        try {
          const chartModule = await import('chart.js/auto')
          Chart = chartModule.default || chartModule.Chart
        } catch (importError) {
          throw new Error(`Failed to load Chart.js: ${importError.message}`)
        }
      }
      
      if (!Chart) {
        throw new Error('Chart.js is not available')
      }

      // Check if canvas is still available
      if (!chartCanvas.value) {
        throw new Error('Chart canvas not available')
      }

      // Destroy existing chart if it exists
      if (chartInstance.value) {
        chartInstance.value.destroy()
      }

      const ctx = chartCanvas.value.getContext('2d')
      
      const labels = props.revenueData.map(item => item.month_name || item.month)
      const actualRevenue = props.revenueData.map(item => item.revenue || 0)
      const expectedRevenue = props.revenueData.map(item => item.expected_revenue || 0)

      chartInstance.value = new Chart(ctx, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [{
            label: '實際收入',
            data: actualRevenue,
            borderColor: 'rgb(99, 102, 241)',
            backgroundColor: 'rgba(99, 102, 241, 0.1)',
            tension: 0.1,
            fill: true
          }, {
            label: '預期收入',
            data: expectedRevenue,
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            borderDash: [5, 5],
            tension: 0.1,
            fill: false
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: true,
              position: 'top',
              labels: {
                usePointStyle: true,
                padding: 20
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function(value) {
                  return formatChartCurrency(value)
                }
              }
            }
          },
          elements: {
            point: {
              radius: 4,
              hoverRadius: 6
            }
          }
        }
      })
    }
    
    loading.value = false
  } catch (err) {
    console.error('Error creating chart:', err)
    error.value = '圖表載入失敗: ' + err.message
    loading.value = false
  }
}

// Watch for data changes
watch(() => props.revenueData, (newData) => {
  if (newData && newData.length > 0) {
    nextTick(() => {
      initChart()
    })
  }
}, { immediate: false }) // Set to false to avoid double initialization

onMounted(() => {
  nextTick(() => {
    if (props.revenueData && props.revenueData.length > 0) {
      initChart()
    }
  })
})

onUnmounted(() => {
  if (chartInstance.value) {
    chartInstance.value.destroy()
  }
})
</script>