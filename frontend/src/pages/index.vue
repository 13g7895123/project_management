<template>
  <div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6">
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
        專案管理儀表板
      </h2>
      <p class="text-gray-600 dark:text-gray-300">
        管理您的專案、業主資訊，並追蹤專案進度和收入統計。
      </p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div
        v-for="stat in stats"
        :key="stat.name"
        class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6"
      >
        <div class="flex items-center">
          <div class="p-3 rounded-lg bg-primary-100 dark:bg-primary-900">
            <component :is="getIcon(stat.icon)" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ stat.name }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stat.value }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Chart Section -->
      <div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          收入趨勢
        </h3>
        <div v-if="loadingChart" class="h-64 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
          <div class="text-center">
            <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]" role="status">
              <span class="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]">載入中...</span>
            </div>
            <p class="mt-2 text-gray-500 dark:text-gray-400">載入收入趨勢...</p>
          </div>
        </div>
        <div v-else-if="chartError" class="h-64 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
          <p class="text-red-500 dark:text-red-400">{{ chartError }}</p>
        </div>
        <div v-else class="h-64">
          <canvas ref="chartCanvas" class="w-full h-full"></canvas>
        </div>
      </div>

      <!-- Activity Feed -->
      <div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          專案動態
        </h3>
        <div class="space-y-4">
          <div
            v-for="activity in activities"
            :key="activity?.id || 'empty'"
            class="flex items-start space-x-3"
            v-if="activity"
          >
            <div class="w-2 h-2 mt-2 bg-primary-500 rounded-full"></div>
            <div class="flex-1">
              <p class="text-sm text-gray-900 dark:text-white">{{ activity?.description || '無描述' }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">{{ activity?.time || '未知時間' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Additional Revenue Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6">
        <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">待收款項</h4>
        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">
          NT${{ (dashboardData.pending_revenue || 0).toLocaleString() }}
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">已完成待收</p>
      </div>
      
      <div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6">
        <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">潛在收入</h4>
        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
          NT${{ (dashboardData.potential_revenue || 0).toLocaleString() }}
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">進行中專案</p>
      </div>
      
      <div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6">
        <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">逾期專案</h4>
        <p class="text-2xl font-bold text-red-600 dark:text-red-400">
          {{ dashboardData.overdue_projects || 0 }}
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">超過預期完成日</p>
      </div>
      
      <div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6">
        <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">即將到期</h4>
        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
          {{ dashboardData.upcoming_deadlines || 0 }}
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">7天內到期</p>
      </div>
    </div>
  </div>
</template>

<script setup>
// Page metadata
definePageMeta({
  middleware: 'auth'
})
import {
  UsersIcon,
  CurrencyDollarIcon,
  ChartBarIcon,
  ClockIcon,
  FolderIcon
} from '@heroicons/vue/24/outline'

const { getDashboardStats, getRecentActivities, getMonthlyRevenueTrend } = useDashboard()

// Reactive data
const loading = ref(true)
const error = ref(null)
const dashboardData = ref({})
const activities = ref([])

// Chart data
const loadingChart = ref(true)
const chartError = ref(null)
const chartCanvas = ref(null)
const chartInstance = ref(null)
const revenueData = ref([])

// Computed stats
const stats = computed(() => [
  { 
    name: '總專案數', 
    value: dashboardData.value.total_projects || 0, 
    icon: 'FolderIcon' 
  },
  { 
    name: '已收入', 
    value: `NT$${(dashboardData.value.total_revenue || 0).toLocaleString()}`, 
    icon: 'CurrencyDollarIcon' 
  },
  { 
    name: '預期總收入', 
    value: `NT$${(dashboardData.value.expected_revenue || 0).toLocaleString()}`, 
    icon: 'ChartBarIcon' 
  },
  { 
    name: '進行中專案', 
    value: dashboardData.value.in_progress_projects || 0, 
    icon: 'ClockIcon' 
  }
])

// Methods
const loadDashboardData = async () => {
  loading.value = true
  error.value = null

  try {
    // Load dashboard stats
    const statsResponse = await getDashboardStats()
    if (statsResponse.success && statsResponse.data) {
      const backendResponse = statsResponse.data
      if (backendResponse.success) {
        dashboardData.value = backendResponse.data
      } else {
        dashboardData.value = backendResponse
      }
    } else {
      // Fallback to mock data for development
      dashboardData.value = {
        total_projects: 15,
        total_revenue: 150000,
        in_progress_projects: 3,
        total_clients: 8
      }
    }

    // Load recent activities
    const activitiesResponse = await getRecentActivities(6)
    if (activitiesResponse.success && activitiesResponse.data) {
      const backendResponse = activitiesResponse.data
      
      if (backendResponse.success && Array.isArray(backendResponse.data)) {
        activities.value = backendResponse.data
      } else if (Array.isArray(backendResponse)) {
        activities.value = backendResponse
      } else {
        activities.value = []
        console.warn('Unexpected activities response format:', backendResponse)
      }
    } else {
      activities.value = []
      console.error('Failed to load activities:', activitiesResponse.error)
    }
  } catch (err) {
    error.value = err.message || '載入數據失敗'
    console.error('Dashboard loading error:', err)
  } finally {
    loading.value = false
  }
}

// Chart methods
const loadRevenueChart = async () => {
  loadingChart.value = true
  chartError.value = null

  try {
    const response = await getMonthlyRevenueTrend(6) // Get last 6 months
    if (response.success && response.data) {
      const backendResponse = response.data
      if (backendResponse.success && Array.isArray(backendResponse.data)) {
        revenueData.value = backendResponse.data
        await nextTick()
        initChart()
      } else {
        throw new Error('Invalid revenue data format')
      }
    } else {
      throw new Error(response.error?.message || '載入收入趨勢失敗')
    }
  } catch (err) {
    chartError.value = err.message || '載入收入趨勢失敗'
    console.error('Chart loading error:', err)
  } finally {
    loadingChart.value = false
  }
}

const initChart = async () => {
  if (!chartCanvas.value || !revenueData.value.length) return

  // Dynamically import Chart.js
  const { Chart, registerables } = await import('chart.js')
  Chart.register(...registerables)

  // Destroy existing chart if it exists
  if (chartInstance.value) {
    chartInstance.value.destroy()
  }

  const ctx = chartCanvas.value.getContext('2d')
  
  const labels = revenueData.value.map(item => item.month_name || item.month)
  const data = revenueData.value.map(item => item.revenue || 0)

  chartInstance.value = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: '月收入',
        data: data,
        borderColor: 'rgb(99, 102, 241)',
        backgroundColor: 'rgba(99, 102, 241, 0.1)',
        tension: 0.1,
        fill: true
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return 'NT$' + value.toLocaleString()
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

const iconComponents = {
  UsersIcon,
  CurrencyDollarIcon,
  ChartBarIcon,
  ClockIcon,
  FolderIcon
}

const getIcon = (iconName) => {
  return iconComponents[iconName] || ChartBarIcon
}

// Load data on mount
onMounted(async () => {
  await loadDashboardData()
  await loadRevenueChart()
})

// Cleanup chart on unmount
onUnmounted(() => {
  if (chartInstance.value) {
    chartInstance.value.destroy()
  }
})
</script>