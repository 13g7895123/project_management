<template>
  <div class="h-64">
    <!-- Loading State -->
    <div v-show="loading" class="h-64 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
      <div class="text-center">
        <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]" role="status">
          <span class="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]">載入中...</span>
        </div>
        <p class="mt-2 text-gray-500 dark:text-gray-400">載入收入趨勢...</p>
      </div>
    </div>
    
    <!-- Error State -->
    <div v-show="error && !loading" class="h-64 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
      <div class="text-center">
        <svg class="mx-auto h-12 w-12 text-red-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-red-500 dark:text-red-400 text-sm">{{ error }}</p>
        <button @click="retryChart" class="mt-2 text-primary-600 hover:text-primary-700 text-sm underline">重試</button>
      </div>
    </div>
    
    <!-- Canvas for Chart -->
    <div v-show="!loading && !error" class="h-full w-full relative">
      <canvas ref="chartCanvas" class="w-full h-full"></canvas>
    </div>
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
  
  try {
    // Only run on client side
    if (!process.client || typeof window === 'undefined') {
      console.log('Not on client side, skipping chart init')
      loading.value = false
      return
    }
    
    console.log('Starting chart initialization...')
    
    // Wait for DOM to be ready and hydration to complete
    await nextTick()
    await new Promise(resolve => setTimeout(resolve, 200))
    
    // Multiple attempts to find canvas element with retry logic
    let attempts = 0
    const maxAttempts = 10
    
    while (!chartCanvas.value && attempts < maxAttempts) {
      console.log(`Canvas element check attempt ${attempts + 1}/${maxAttempts}`)
      await new Promise(resolve => setTimeout(resolve, 100))
      attempts++
    }
    
    // Final check for canvas element
    if (!chartCanvas.value) {
      console.error('Canvas element not found after multiple attempts')
      error.value = 'Canvas element not found'
      loading.value = false
      return
    }
    
    console.log('Canvas element found successfully')
    
    // Check if we have revenue data
    if (!props.revenueData || props.revenueData.length === 0) {
      console.warn('No revenue data found, using fallback data for demo')
      // Don't show error, just keep loading state off but no chart
      loading.value = false
      return
    }

    // Try to get Chart.js from multiple sources
    let Chart = null
    
    try {
      // First try to get from Nuxt plugin
      const nuxtApp = useNuxtApp()
      Chart = nuxtApp.$Chart
      console.log('Chart from plugin:', !!Chart)
    } catch (e) {
      console.log('Plugin not available, trying direct import')
    }
    
    // If plugin didn't provide Chart, try direct import
    if (!Chart) {
      try {
        console.log('Attempting direct Chart.js import...')
        const chartModule = await import('chart.js/auto')
        Chart = chartModule.default || chartModule.Chart || chartModule
        console.log('Direct import successful:', !!Chart)
      } catch (importError) {
        console.error('Direct import failed:', importError)
        throw new Error(`Failed to load Chart.js: ${importError.message}`)
      }
    }
    
    if (!Chart) {
      throw new Error('Chart.js is not available from any source')
    }

    // Destroy existing chart if it exists
    if (chartInstance.value) {
      chartInstance.value.destroy()
      chartInstance.value = null
    }

    // Get context
    const ctx = chartCanvas.value.getContext('2d')
    if (!ctx) {
      throw new Error('Cannot get 2D context from canvas')
    }
    
    const labels = props.revenueData.map(item => item.month_name || item.month)
    const actualRevenue = props.revenueData.map(item => item.revenue || 0)
    const expectedRevenue = props.revenueData.map(item => item.expected_revenue || 0)

    console.log('Creating chart with data:', { labels, actualRevenue, expectedRevenue })

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
    
    console.log('Chart created successfully')
    loading.value = false
  } catch (err) {
    console.error('Error creating chart:', err)
    error.value = `圖表載入失敗: ${err.message}`
    loading.value = false
  }
}

// Retry chart loading function
const retryChart = async () => {
  console.log('Retrying chart initialization')
  error.value = null
  loading.value = true
  
  // Give a brief moment before retrying
  await new Promise(resolve => setTimeout(resolve, 500))
  await initChart()
}

// Watch for data changes
watch(() => props.revenueData, async (newData) => {
  console.log('Revenue data changed:', newData)
  if (newData && newData.length > 0) {
    await nextTick()
    await initChart()
  }
}, { immediate: false })

onMounted(async () => {
  console.log('RevenueChart mounted')
  
  // Only proceed if we're on the client
  if (!process.client) {
    console.log('Server-side rendering, skipping chart initialization')
    loading.value = false
    return
  }
  
  // Wait for hydration and DOM to be fully ready
  await new Promise(resolve => setTimeout(resolve, 800))
  
  // Always try to initialize chart, even without data (will show empty state gracefully)
  await initChart()
})

onUnmounted(() => {
  if (chartInstance.value) {
    chartInstance.value.destroy()
  }
})
</script>