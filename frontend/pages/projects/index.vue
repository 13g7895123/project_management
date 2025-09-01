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
    <div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm border border-gray-200 dark:border-gray-700 p-6">
      <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">搜尋專案</label>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="搜尋專案名稱..."
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white transition-colors duration-200"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">業主</label>
          <select
            v-model="filterClientId"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white transition-colors duration-200"
          >
            <option value="">全部業主</option>
            <option v-for="client in clients" :key="client?.id || 'empty'" :value="client?.id">
              {{ client?.name || '未知業主' }}
            </option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">專案類別</label>
          <select
            v-model="filterCategory"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white transition-colors duration-200"
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
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white transition-colors duration-200"
          >
            <option value="">全部狀態</option>
            <option value="pending_evaluation">待評估</option>
            <option value="contacted">已接洽</option>
            <option value="in_progress">進行中</option>
            <option value="completed">已完成</option>
            <option value="paid">已收款</option>
            <option value="no_follow_up">無下文</option>
          </select>
        </div>
        <div class="flex items-end">
          <button
            @click="clearFilters"
            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200"
          >
            清除篩選
          </button>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]" role="status">
        <span class="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]">載入中...</span>
      </div>
      <p class="mt-2 text-gray-500 dark:text-gray-400">正在載入專案資料...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700 rounded-md p-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ error }}</p>
          <button @click="loadProjects" class="mt-2 text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200 underline">
            重新載入
          </button>
        </div>
      </div>
    </div>

    <!-- Projects Table -->
    <div v-else class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full">
          <thead>
            <tr class="border-b border-gray-200 dark:border-gray-700">
              <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white w-72">
                專案名稱
              </th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">
                業主
              </th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">
                類別
              </th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">
                金額
              </th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">
                狀態
              </th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">
                接洽日期
              </th>
              <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white">
                操作
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800">
            <tr
              v-for="project in filteredProjects"
              :key="project?.id || 'empty'"
              class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
            >
              <td class="px-6 py-4 w-72">
                <div class="max-w-xs">
                  <div 
                    class="text-base font-semibold text-gray-900 dark:text-white truncate cursor-help mb-1" 
                    :title="project?.name || '未知專案'"
                  >
                    {{ project?.name || '未知專案' }}
                  </div>
                  <div 
                    class="text-sm text-gray-600 dark:text-gray-400 truncate cursor-help" 
                    :title="project?.description || '無描述'"
                  >
                    {{ project?.description || '無描述' }}
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-base font-medium text-gray-900 dark:text-white">
                  {{ project?.client?.name || '未知業主' }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium"
                  :class="getCategoryClass(project?.category)"
                >
                  {{ getCategoryLabel(project?.category) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-base font-semibold text-gray-900 dark:text-white">
                  {{ formatTWD(project?.amount || 0) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium"
                  :class="getStatusClass(project?.status)"
                >
                  {{ getStatusLabel(project?.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-base text-gray-600 dark:text-gray-400">
                  {{ formatDate(project?.contact_date) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex justify-end gap-2">
                  <button 
                    @click="editProject(project)" 
                    class="p-2 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 dark:text-indigo-400 dark:hover:text-indigo-300 dark:hover:bg-indigo-900/20 rounded-lg transition-colors duration-200"
                    title="編輯專案"
                  >
                    <PencilIcon class="w-4 h-4" />
                  </button>
                  <button 
                    @click="handleDeleteProject(project?.id)" 
                    class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed" 
                    :disabled="!project?.id"
                    title="刪除專案"
                  >
                    <TrashIcon class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Pagination Controls -->
      <div v-if="totalPages > 1" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div class="flex items-center gap-4">
            <div class="text-sm text-gray-700 dark:text-gray-300">
              顯示第 {{ (currentPage - 1) * perPage + 1 }} - {{ Math.min(currentPage * perPage, totalItems) }} 筆，共 {{ totalItems }} 筆專案
            </div>
            <div class="flex items-center gap-2">
              <label class="text-sm text-gray-700 dark:text-gray-300">每頁顯示:</label>
              <select 
                v-model="perPage" 
                @change="handlePerPageChange(perPage)"
                class="px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
              >
                <option v-for="option in perPageOptions" :key="option" :value="option">
                  {{ option }}
                </option>
              </select>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <button
              @click="previousPage"
              :disabled="currentPage === 1"
              class="px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
            >
              上一頁
            </button>
            
            <div class="flex items-center gap-1">
              <button
                v-for="page in Math.min(5, totalPages)"
                :key="page"
                @click="goToPage(page)"
                :class="[
                  'px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200',
                  currentPage === page
                    ? 'bg-primary-600 text-white shadow-sm'
                    : 'border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600'
                ]"
              >
                {{ page }}
              </button>
              
              <span v-if="totalPages > 5" class="px-2 text-gray-500 dark:text-gray-400">...</span>
              
              <button
                v-if="totalPages > 5 && currentPage < totalPages - 2"
                @click="goToPage(totalPages)"
                class="px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200"
              >
                {{ totalPages }}
              </button>
            </div>
            
            <button
              @click="nextPage"
              :disabled="currentPage === totalPages"
              class="px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
            >
              下一頁
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
// Page metadata
definePageMeta({
  middleware: 'auth'
})
import { PlusIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/outline'

const { getProjects, deleteProject } = useProjects()
const { getClients } = useClients()
const { formatTWD } = useCurrency()
const { showSuccess, showError, showDeleteConfirm, showLoading, close } = useSweetAlert()

// Reactive data
const projects = ref([])
const searchQuery = ref('')
const filterCategory = ref('')
const filterStatus = ref('')
const filterClientId = ref('')
const loading = ref(false)
const error = ref(null)

// Pagination state
const currentPage = ref(1)
const perPage = ref(5)
const perPageOptions = [5, 10, 25, 50, 100]
const totalItems = ref(0)
const totalPages = computed(() => Math.ceil(totalItems.value / perPage.value))

// Client data for filter dropdown
const clients = ref([])
const loadingClients = ref(false)

// Computed properties
const filteredProjects = computed(() => {
  const filtered = projects.value.filter(project => {
    const matchesSearch = project.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         project.description.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesCategory = !filterCategory.value || project.category === filterCategory.value
    const matchesStatus = !filterStatus.value || project.status === filterStatus.value
    const matchesClient = !filterClientId.value || (project.client_id && project.client_id.toString() === filterClientId.value.toString())
    
    return matchesSearch && matchesCategory && matchesStatus && matchesClient
  })
  
  // Update total items for pagination
  totalItems.value = filtered.length
  
  // Apply pagination
  const startIndex = (currentPage.value - 1) * perPage.value
  const endIndex = startIndex + perPage.value
  
  return filtered.slice(startIndex, endIndex)
})

// Methods
const loadProjects = async () => {
  loading.value = true
  error.value = null
  
  try {
    const response = await getProjects({
      search: searchQuery.value,
      category: filterCategory.value,
      status: filterStatus.value,
      client_id: filterClientId.value
    })
    
    if (response.success && response.data) {
      // Handle paginated response: response.data.data.data contains the actual project array
      // Backend structure: {success: true, data: {data: [...], ...pagination}, message}
      // After useApi wrapper: {success: true, data: {success: true, data: {data: [...]}}, error: null}
      const backendResponse = response.data
      
      if (backendResponse.success && backendResponse.data) {
        // Laravel paginated response structure: backendResponse.data.data contains the actual project array
        if (Array.isArray(backendResponse.data.data)) {
          projects.value = backendResponse.data.data || []
        } else if (Array.isArray(backendResponse.data)) {
          // Fallback: in case the response is not paginated
          projects.value = backendResponse.data || []
        } else {
          projects.value = []
          error.value = '載入專案資料失敗：API回應格式不正確'
        }
      } else {
        // Handle case where backend response doesn't have expected structure
        projects.value = []
        error.value = backendResponse.message || '載入專案資料失敗：格式錯誤'
      }
    } else {
      projects.value = []
      error.value = response.error?.message || '載入專案資料失敗'
    }
  } catch (err) {
    console.error('Load projects error:', err)
    projects.value = []
    error.value = '載入專案資料時發生錯誤，請稍後再試'
  } finally {
    loading.value = false
  }
}

const clearFilters = () => {
  searchQuery.value = ''
  filterCategory.value = ''
  filterStatus.value = ''
  filterClientId.value = ''
  currentPage.value = 1  // Reset to first page
  loadProjects()
}

// Handle per page change
const handlePerPageChange = (newPerPage) => {
  perPage.value = newPerPage
  currentPage.value = 1  // Reset to first page when changing per page
}

// Pagination methods
const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
  }
}

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

const handleDeleteProject = async (projectId) => {
  const project = projects.value.find(p => p.id === projectId)
  if (!project) return
  
  const result = await showDeleteConfirm(
    '確認刪除專案',
    `確定要刪除專案「${project.name}」嗎？此操作無法復原。`,
    '確認刪除',
    '取消'
  )
  
  if (result.isConfirmed) {
    try {
      showLoading('刪除專案中...', '正在處理刪除操作')
      const response = await deleteProject(projectId)
      
      close()
      if (response.success) {
        // Remove from local array
        projects.value = projects.value.filter(project => project.id !== projectId)
        showSuccess('專案刪除成功', `專案「${project.name}」已成功刪除`)
      } else {
        throw new Error(response.error?.message || '刪除失敗')
      }
    } catch (error) {
      close()
      showError('專案刪除失敗', error.message || '無法刪除專案，請稍後再試')
    }
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
    website: 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700',
    script: 'bg-green-50 text-green-700 border border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700',
    server: 'bg-yellow-50 text-yellow-700 border border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-700',
    custom: 'bg-purple-50 text-purple-700 border border-purple-200 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-700'
  }
  return classes[category] || 'bg-gray-50 text-gray-700 border border-gray-200 dark:bg-gray-900/30 dark:text-gray-300 dark:border-gray-700'
}

const getStatusLabel = (status) => {
  const labels = {
    pending_evaluation: '待評估',
    contacted: '已接洽',
    in_progress: '進行中',
    completed: '已完成',
    paid: '已收款',
    no_follow_up: '無下文'
  }
  return labels[status] || status
}

const getStatusClass = (status) => {
  const classes = {
    pending_evaluation: 'bg-orange-50 text-orange-700 border border-orange-200 dark:bg-orange-900/30 dark:text-orange-300 dark:border-orange-700',
    contacted: 'bg-gray-50 text-gray-700 border border-gray-200 dark:bg-gray-900/30 dark:text-gray-300 dark:border-gray-700',
    in_progress: 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700',
    completed: 'bg-green-50 text-green-700 border border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700',
    paid: 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-700',
    no_follow_up: 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700'
  }
  return classes[status] || 'bg-gray-50 text-gray-700 border border-gray-200 dark:bg-gray-900/30 dark:text-gray-300 dark:border-gray-700'
}

const formatDate = (dateString) => {
  if (!dateString) return '未設定'
  return new Date(dateString).toLocaleDateString('zh-TW')
}

// Edit method - navigate to edit page
const editProject = (project) => {
  if (!project?.id) return
  navigateTo(`/projects/${project.id}`)
}

// Load clients for filter dropdown
const loadClients = async () => {
  loadingClients.value = true
  
  try {
    const response = await getClients({ per_page: 100 })
    
    if (response.success && response.data) {
      const backendResponse = response.data
      if (backendResponse.success && backendResponse.data && backendResponse.data.data) {
        clients.value = backendResponse.data.data || []
      } else {
        clients.value = []
      }
    } else {
      clients.value = []
    }
  } catch (error) {
    console.error('載入業主失敗:', error)
    clients.value = []
  } finally {
    loadingClients.value = false
  }
}

// Load data on mount
onMounted(async () => {
  // Check for search parameter from URL (e.g., from 404 page)
  const route = useRoute()
  if (route.query.q) {
    searchQuery.value = route.query.q
  }
  
  // Check for client_id parameter from URL (from client view projects)
  if (route.query.client_id) {
    filterClientId.value = route.query.client_id
  }
  
  // Load clients for filter dropdown
  await loadClients()
  
  loadProjects()
})

// Watch filters for real-time filtering and reset pagination
watch([searchQuery, filterCategory, filterStatus, filterClientId, perPage], () => {
  currentPage.value = 1  // Reset to first page when filters or per page changes
})
</script>