<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">專案管理</h1>
        <p class="text-gray-600 dark:text-gray-300">管理所有專案資訊</p>
      </div>
      <NuxtLink
        to="/projects/create"
        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
      >
        <PlusIcon class="w-4 h-4 mr-2" />
        新增專案
      </NuxtLink>
    </div>

    <!-- Filter and Search -->
    <div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">搜尋專案</label>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="搜尋專案名稱..."
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">專案類別</label>
          <select
            v-model="filterCategory"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
          >
            <option value="">全部類別</option>
            <option value="website">網站</option>
            <option value="script">腳本</option>
            <option value="server">伺服器</option>
            <option value="custom">自訂</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">專案狀態</label>
          <select
            v-model="filterStatus"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
          >
            <option value="">全部狀態</option>
            <option value="contacted">已接洽</option>
            <option value="in_progress">進行中</option>
            <option value="completed">已完成</option>
            <option value="paid">已收款</option>
          </select>
        </div>
        <div class="flex items-end">
          <button
            @click="clearFilters"
            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
          >
            清除篩選
          </button>
        </div>
      </div>
    </div>

    <!-- Projects Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-900">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                專案名稱
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                業主
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                類別
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                金額
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                狀態
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                接洽日期
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                操作
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr
              v-for="project in filteredProjects"
              :key="project.id"
              class="hover:bg-gray-50 dark:hover:bg-gray-700"
            >
              <td class="px-6 py-4 whitespace-nowrap">
                <div>
                  <div class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ project.name }}
                  </div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ project.description }}
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                {{ project.client }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="getCategoryClass(project.category)"
                >
                  {{ getCategoryLabel(project.category) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                NT${{ project.amount.toLocaleString() }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="getStatusClass(project.status)"
                >
                  {{ getStatusLabel(project.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                {{ formatDate(project.contact_date) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex justify-end space-x-2">
                  <button class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
                    <PencilIcon class="w-4 h-4" />
                  </button>
                  <button @click="handleDeleteProject(project.id)" class="text-red-600 hover:text-red-900 dark:text-red-400">
                    <TrashIcon class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { PlusIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/outline'

const { getProjects, deleteProject } = useProjects()

// Reactive data
const projects = ref([])
const searchQuery = ref('')
const filterCategory = ref('')
const filterStatus = ref('')
const loading = ref(false)
const error = ref(null)

// Computed properties
const filteredProjects = computed(() => {
  return projects.value.filter(project => {
    const matchesSearch = project.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         project.description.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesCategory = !filterCategory.value || project.category === filterCategory.value
    const matchesStatus = !filterStatus.value || project.status === filterStatus.value
    
    return matchesSearch && matchesCategory && matchesStatus
  })
})

// Methods
const loadProjects = async () => {
  loading.value = true
  error.value = null
  
  const response = await getProjects({
    search: searchQuery.value,
    category: filterCategory.value,
    status: filterStatus.value
  })
  
  if (response.success) {
    projects.value = response.data.data || response.data || []
  } else {
    error.value = response.error?.message || '載入專案資料失敗'
    // Fallback to mock data for development
    projects.value = [
      {
        id: 1,
        name: 'ABC公司官網',
        description: '企業形象網站建置',
        client: 'ABC公司',
        category: 'website',
        amount: 50000,
        status: 'completed',
        contact_date: '2024-01-15'
      },
      {
        id: 2,
        name: '自動化備份腳本',
        description: '伺服器備份自動化',
        client: 'XYZ企業',
        category: 'script',
        amount: 15000,
        status: 'in_progress',
        contact_date: '2024-01-20'
      },
      {
        id: 3,
        name: '伺服器維護',
        description: '定期系統維護',
        client: '123公司',
        category: 'server',
        amount: 30000,
        status: 'paid',
        contact_date: '2024-01-10'
      }
    ]
  }
  
  loading.value = false
}

const clearFilters = () => {
  searchQuery.value = ''
  filterCategory.value = ''
  filterStatus.value = ''
  loadProjects()
}

const handleDeleteProject = async (projectId) => {
  if (!confirm('確定要刪除此專案嗎？')) return
  
  const response = await deleteProject(projectId)
  
  if (response.success) {
    // Remove from local array
    projects.value = projects.value.filter(project => project.id !== projectId)
  } else {
    alert(response.error?.message || '刪除失敗')
  }
}

const getCategoryLabel = (category) => {
  const labels = {
    website: '網站',
    script: '腳本',
    server: '伺服器',
    custom: '自訂'
  }
  return labels[category] || category
}

const getCategoryClass = (category) => {
  const classes = {
    website: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    script: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    server: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    custom: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200'
  }
  return classes[category] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
}

const getStatusLabel = (status) => {
  const labels = {
    contacted: '已接洽',
    in_progress: '進行中',
    completed: '已完成',
    paid: '已收款'
  }
  return labels[status] || status
}

const getStatusClass = (status) => {
  const classes = {
    contacted: 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
    in_progress: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    completed: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    paid: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200'
  }
  return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('zh-TW')
}

// Load data on mount
onMounted(() => {
  loadProjects()
})

// Watch filters for real-time filtering
watch([searchQuery, filterCategory, filterStatus], () => {
  // Debounce API calls if needed
  // For now, just filter locally
})
</script>