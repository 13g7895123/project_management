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
        <div class="h-64 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
          <p class="text-gray-500 dark:text-gray-400">月收入趨勢圖表 (待實作)</p>
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
  </div>
</template>

<script setup>
import {
  UsersIcon,
  CurrencyDollarIcon,
  ChartBarIcon,
  ClockIcon,
  FolderIcon
} from '@heroicons/vue/24/outline'

const { getDashboardStats, getRecentActivities } = useDashboard()

// Reactive data
const loading = ref(true)
const error = ref(null)
const dashboardData = ref({})
const activities = ref([])

// Computed stats
const stats = computed(() => [
  { 
    name: '總專案數', 
    value: dashboardData.value.total_projects || 0, 
    icon: 'FolderIcon' 
  },
  { 
    name: '總收入', 
    value: `NT$${(dashboardData.value.total_revenue || 0).toLocaleString()}`, 
    icon: 'CurrencyDollarIcon' 
  },
  { 
    name: '進行中專案', 
    value: dashboardData.value.in_progress_projects || 0, 
    icon: 'ClockIcon' 
  },
  { 
    name: '活躍業主', 
    value: dashboardData.value.total_clients || 0, 
    icon: 'UsersIcon' 
  }
])

// Methods
const loadDashboardData = async () => {
  loading.value = true
  error.value = null

  try {
    // Load dashboard stats
    const statsResponse = await getDashboardStats()
    if (statsResponse.success) {
      dashboardData.value = statsResponse.data
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
    const activitiesResponse = await getRecentActivities(4)
    if (activitiesResponse.success) {
      activities.value = activitiesResponse.data
    } else {
      // Fallback to mock data for development
      activities.value = [
        { id: 1, description: '完成「ABC公司網站」專案', time: '2小時前' },
        { id: 2, description: '新增業主「XYZ企業」', time: '1天前' },
        { id: 3, description: '收到「伺服器維護」專案款項', time: '2天前' },
        { id: 4, description: '開始執行「自動化腳本」專案', time: '3天前' }
      ]
    }
  } catch (err) {
    error.value = err.message || '載入數據失敗'
    console.error('Dashboard loading error:', err)
  } finally {
    loading.value = false
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

// Load data on mount
onMounted(() => {
  loadDashboardData()
})
</script>