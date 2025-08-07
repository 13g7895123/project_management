<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6">
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
        分析概覽
      </h2>
      <p class="text-gray-600 dark:text-gray-300">
        詳細的業務分析數據和圖表統計
      </p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6">
      <div class="animate-pulse">
        <div class="h-4 bg-gray-300 rounded w-1/4 mb-4"></div>
        <div class="space-y-3">
          <div class="h-4 bg-gray-300 rounded w-full"></div>
          <div class="h-4 bg-gray-300 rounded w-3/4"></div>
          <div class="h-4 bg-gray-300 rounded w-1/2"></div>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" v-if="!loading">
      <div
        v-for="stat in statsCards"
        :key="stat.title"
        class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6"
      >
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div :class="stat.iconClass" class="w-8 h-8 rounded-md flex items-center justify-center">
              <component :is="stat.icon" class="w-5 h-5 text-white" />
            </div>
          </div>
          <div class="ml-5 w-0 flex-1">
            <dl>
              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                {{ stat.title }}
              </dt>
              <dd class="text-lg font-medium text-gray-900 dark:text-white">
                {{ stat.value }}
              </dd>
            </dl>
          </div>
        </div>
      </div>
    </div>

    <!-- Revenue Trend Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6" v-if="!loading">
      <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">收入趨勢</h3>
      <div class="h-64 flex items-center justify-center text-gray-500 dark:text-gray-400">
        {{ revenueTrend.length > 0 ? `${revenueTrend.length} 個月的數據` : '暫無收入趨勢數據' }}
      </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6" v-if="!loading">
      <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">近期活動</h3>
      <div class="space-y-3" v-if="activities.length > 0">
        <div
          v-for="activity in activities"
          :key="activity?.id || 'empty'"
          class="flex items-center space-x-3 py-2"
          v-if="activity"
        >
          <div class="flex-shrink-0">
            <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm text-gray-900 dark:text-white">{{ activity?.description || '無描述' }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(activity?.created_at) }}</p>
          </div>
        </div>
      </div>
      <p v-else class="text-gray-500 dark:text-gray-400">暫無近期活動</p>
    </div>
  </div>
</template>

<script setup>
// Page metadata
definePageMeta({
  middleware: 'auth'
})

import {
  CurrencyDollarIcon,
  BriefcaseIcon,
  UsersIcon,
  CheckCircleIcon
} from '@heroicons/vue/24/outline'

const { 
  getDashboardStats, 
  getMonthlyRevenueTrend, 
  getRecentActivities 
} = useDashboard()

// Reactive data
const loading = ref(true)
const error = ref(null)
const dashboardStats = ref({})
const revenueTrend = ref([])
const activities = ref([])

// Computed stats cards
const statsCards = computed(() => [
  {
    title: '總收入',
    value: `NT$${(dashboardStats.value.total_revenue || 0).toLocaleString()}`,
    icon: CurrencyDollarIcon,
    iconClass: 'bg-green-500'
  },
  {
    title: '專案總數',
    value: dashboardStats.value.total_projects || 0,
    icon: BriefcaseIcon,
    iconClass: 'bg-blue-500'
  },
  {
    title: '業主總數',
    value: dashboardStats.value.total_clients || 0,
    icon: UsersIcon,
    iconClass: 'bg-purple-500'
  },
  {
    title: '已完成專案',
    value: dashboardStats.value.completed_projects || 0,
    icon: CheckCircleIcon,
    iconClass: 'bg-green-500'
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
      dashboardStats.value = statsResponse.data
    }

    // Load revenue trend
    const trendResponse = await getMonthlyRevenueTrend(12)
    if (trendResponse.success) {
      revenueTrend.value = trendResponse.data
    }

    // Load recent activities
    const activitiesResponse = await getRecentActivities(10)
    if (activitiesResponse.success) {
      activities.value = activitiesResponse.data
    }
  } catch (err) {
    error.value = err.message || '載入數據失敗'
    console.error('Dashboard loading error:', err)
  } finally {
    loading.value = false
  }
}

const formatDate = (dateString) => {
  if (!dateString) return '未設定'
  return new Date(dateString).toLocaleDateString('zh-TW', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Load data on mount
onMounted(() => {
  loadDashboardData()
})
</script>