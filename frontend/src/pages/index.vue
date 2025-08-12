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
          <div class="text-center">
            <svg class="mx-auto h-12 w-12 text-red-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-red-500 dark:text-red-400 text-sm">{{ chartError }}</p>
            <button @click="retryChart" class="mt-2 text-primary-600 hover:text-primary-700 text-sm underline">重試</button>
          </div>
        </div>
        <div v-else class="h-64">
          <ClientOnly>
            <RevenueChart :revenue-data="revenueData" />
            <template #fallback>
              <div class="h-64 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                <div class="text-center">
                  <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]" role="status">
                    <span class="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]">載入中...</span>
                  </div>
                  <p class="mt-2 text-gray-500 dark:text-gray-400">載入收入趨勢...</p>
                </div>
              </div>
            </template>
          </ClientOnly>
        </div>
      </div>

      <!-- Activity Feed -->
      <div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          專案動態
        </h3>
        <div v-if="activities && activities.length > 0" class="space-y-4">
          <div
            v-for="activity in activities"
            :key="activity?.id || 'empty'"
            class="flex items-start space-x-3"
          >
            <div class="w-2 h-2 mt-2 bg-primary-500 rounded-full"></div>
            <div class="flex-1">
              <p class="text-sm text-gray-900 dark:text-white">{{ activity?.description || '無描述' }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">{{ activity?.time || '未知時間' }}</p>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
          <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
          </svg>
          <p>暫無專案動態</p>
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
// import { Chart, registerables } from 'chart.js'

const { getDashboardStats, getRecentActivities, getMonthlyRevenueTrend } = useDashboard()

// Reactive data
const loading = ref(true)
const error = ref(null)
const dashboardData = ref({})
const activities = ref([])

// Chart data
const loadingChart = ref(true)
const chartError = ref(null)
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
        // If unexpected format, set empty activities
        console.warn('Unexpected activities format, showing empty activities')
        activities.value = []
      }
    } else {
      // If API fails, set empty activities
      console.warn('Activities API failed, showing empty activities')
      activities.value = []
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
  console.log('Starting loadRevenueChart')
  loadingChart.value = true
  chartError.value = null

  try {
    console.log('Calling getMonthlyRevenueTrend API')
    const response = await getMonthlyRevenueTrend() // Get last 6 months + next 1 month
    console.log('API response received:', response)
    if (response.success && response.data) {
      const backendResponse = response.data
      if (backendResponse.success && Array.isArray(backendResponse.data)) {
        revenueData.value = backendResponse.data
      } else if (Array.isArray(backendResponse.data)) {
        revenueData.value = backendResponse.data
      } else {
        // If API response is unexpected format, create empty data
        console.warn('Unexpected API response format, showing empty chart')
        revenueData.value = []
      }
    } else {
      // If API fails, create empty data
      console.warn('Revenue API failed, showing empty chart')
      revenueData.value = []
    }
    
    await nextTick()
    loadingChart.value = false
  } catch (err) {
    console.error('Chart loading error:', err)
    chartError.value = `載入收入趨勢失敗: ${err.message}`
    loadingChart.value = false
  }
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

// Retry chart loading function
const retryChart = () => {
  console.log('Retrying chart load')
  chartError.value = null
  loadRevenueChart()
}

// Load data on mount
onMounted(async () => {
  console.log('Dashboard component mounted')
  
  // Ensure DOM is fully rendered
  await nextTick()
  
  await loadDashboardData()
  console.log('Dashboard data loaded, starting chart load')
  
  // Load chart data
  await loadRevenueChart()
  console.log('Chart load completed')
})
</script>