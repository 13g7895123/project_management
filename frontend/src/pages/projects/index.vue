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
      <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
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
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">業主</label>
          <select
            v-model="filterClientId"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
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
    <div v-else class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm overflow-hidden">
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
              :key="project?.id || 'empty'"
              class="hover:bg-gray-50 dark:hover:bg-gray-700"
            >
              <td class="px-6 py-4 whitespace-nowrap">
                <div>
                  <div class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ project?.name || '未知專案' }}
                  </div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ project?.description || '無描述' }}
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                {{ project?.client?.name || '未知業主' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="getCategoryClass(project?.category)"
                >
                  {{ getCategoryLabel(project?.category) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                NT${{ (project?.amount || 0).toLocaleString() }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="getStatusClass(project?.status)"
                >
                  {{ getStatusLabel(project?.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                {{ formatDate(project?.contact_date) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex justify-end space-x-2">
                  <button @click="handleEditProject(project)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
                    <PencilIcon class="w-4 h-4" />
                  </button>
                  <button @click="handleDeleteProject(project?.id)" class="text-red-600 hover:text-red-900 dark:text-red-400" :disabled="!project?.id">
                    <TrashIcon class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Edit Project Modal -->
    <div v-if="showEditModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeEditModal"></div>
        
        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <form @submit.prevent="submitEdit" class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                  編輯專案
                </h3>
                
                <div class="space-y-4">
                  <!-- 專案名稱 -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      專案名稱 <span class="text-red-500">*</span>
                    </label>
                    <input
                      v-model="editForm.name"
                      type="text"
                      required
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                      placeholder="請輸入專案名稱"
                    />
                  </div>

                  <!-- 業主 -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      業主 <span class="text-red-500">*</span>
                    </label>
                    <select
                      v-model="editForm.client_id"
                      required
                      :disabled="loadingClients"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white disabled:opacity-50"
                    >
                      <option value="">請選擇業主</option>
                      <option v-for="client in clients" :key="client?.id || 'empty'" :value="client?.id">
                        {{ client?.name || '未知業主' }}
                      </option>
                    </select>
                  </div>

                  <!-- 專案描述 -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      專案描述
                    </label>
                    <textarea
                      v-model="editForm.description"
                      rows="3"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                      placeholder="請描述專案內容..."
                    />
                  </div>

                  <!-- 類別和金額 -->
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        專案類別 <span class="text-red-500">*</span>
                      </label>
                      <select
                        v-model="editForm.category"
                        required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                      >
                        <option value="">請選擇類別</option>
                        <option value="website">網站</option>
                        <option value="script">腳本</option>
                        <option value="server">伺服器</option>
                        <option value="custom">自訂</option>
                      </select>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        專案金額 <span class="text-red-500">*</span>
                      </label>
                      <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">
                          NT$
                        </span>
                        <input
                          v-model.number="editForm.amount"
                          type="number"
                          min="0"
                          step="1"
                          required
                          class="w-full pl-12 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                          placeholder="0"
                        />
                      </div>
                    </div>
                  </div>

                  <!-- 日期 -->
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        接洽日期 <span class="text-red-500">*</span>
                      </label>
                      <input
                        v-model="editForm.contact_date"
                        type="date"
                        required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                      />
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        開始執行日期
                      </label>
                      <input
                        v-model="editForm.start_date"
                        type="date"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                      />
                    </div>
                  </div>

                  <div class="grid grid-cols-3 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        預計完成日期
                      </label>
                      <input
                        v-model="editForm.expected_completion_date"
                        type="date"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                      />
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        實際完成日期
                      </label>
                      <input
                        v-model="editForm.completion_date"
                        type="date"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                      />
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        收款日期
                      </label>
                      <input
                        v-model="editForm.payment_date"
                        type="date"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                      />
                    </div>
                  </div>

                  <!-- 專案狀態 -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      專案狀態
                    </label>
                    <select
                      v-model="editForm.status"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                    >
                      <option value="contacted">已接洽</option>
                      <option value="in_progress">進行中</option>
                      <option value="completed">已完成</option>
                      <option value="paid">已收款</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Modal Actions -->
            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                type="submit"
                :disabled="isUpdating"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <span v-if="isUpdating">更新中...</span>
                <span v-else>更新專案</span>
              </button>
              <button
                type="button"
                @click="closeEditModal"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              >
                取消
              </button>
            </div>
          </form>
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

const { getProjects, deleteProject, updateProject } = useProjects()
const { getClients } = useClients()

// Reactive data
const projects = ref([])
const searchQuery = ref('')
const filterCategory = ref('')
const filterStatus = ref('')
const filterClientId = ref('')
const loading = ref(false)
const error = ref(null)

// Edit modal data
const showEditModal = ref(false)
const isUpdating = ref(false)
const editingProject = ref(null)
const clients = ref([])
const loadingClients = ref(false)
const editForm = ref({
  name: '',
  client_id: '',
  description: '',
  category: '',
  amount: null,
  contact_date: '',
  start_date: '',
  expected_completion_date: '',
  completion_date: '',
  payment_date: '',
  status: 'contacted'
})

// Computed properties
const filteredProjects = computed(() => {
  return projects.value.filter(project => {
    const matchesSearch = project.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         project.description.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesCategory = !filterCategory.value || project.category === filterCategory.value
    const matchesStatus = !filterStatus.value || project.status === filterStatus.value
    const matchesClient = !filterClientId.value || (project.client_id && project.client_id.toString() === filterClientId.value.toString())
    
    return matchesSearch && matchesCategory && matchesStatus && matchesClient
  })
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
  if (!dateString) return '未設定'
  return new Date(dateString).toLocaleDateString('zh-TW')
}

// Edit modal methods
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

const handleEditProject = async (project) => {
  if (!project?.id) return
  
  editingProject.value = project
  
  // Load clients if not already loaded
  if (clients.value.length === 0) {
    await loadClients()
  }
  
  // Populate edit form with project data
  editForm.value = {
    name: project.name || '',
    client_id: project.client_id || '',
    description: project.description || '',
    category: project.category || '',
    amount: project.amount || null,
    contact_date: project.contact_date ? new Date(project.contact_date).toISOString().split('T')[0] : '',
    start_date: project.start_date ? new Date(project.start_date).toISOString().split('T')[0] : '',
    expected_completion_date: project.expected_completion_date ? new Date(project.expected_completion_date).toISOString().split('T')[0] : '',
    completion_date: project.completion_date ? new Date(project.completion_date).toISOString().split('T')[0] : '',
    payment_date: project.payment_date ? new Date(project.payment_date).toISOString().split('T')[0] : '',
    status: project.status || 'contacted'
  }
  
  showEditModal.value = true
}

const closeEditModal = () => {
  showEditModal.value = false
  editingProject.value = null
  editForm.value = {
    name: '',
    client_id: '',
    description: '',
    category: '',
    amount: null,
    contact_date: '',
    start_date: '',
    expected_completion_date: '',
    completion_date: '',
    payment_date: '',
    status: 'contacted'
  }
}

const submitEdit = async () => {
  if (!editingProject.value?.id) return
  
  isUpdating.value = true
  
  try {
    const response = await updateProject(editingProject.value.id, editForm.value)
    
    if (response.success && response.data) {
      // Update the project in the local array
      const index = projects.value.findIndex(p => p.id === editingProject.value.id)
      if (index !== -1) {
        // Extract the updated project data
        const updatedProject = response.data.data || response.data
        projects.value[index] = updatedProject
      }
      
      closeEditModal()
      
      // Optionally reload projects to ensure data consistency
      await loadProjects()
    } else {
      alert(response.error?.message || '更新失敗')
    }
  } catch (error) {
    console.error('更新專案失敗:', error)
    alert('更新專案失敗，請稍後再試')
  } finally {
    isUpdating.value = false
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

// Watch filters for real-time filtering
watch([searchQuery, filterCategory, filterStatus, filterClientId], () => {
  // Debounce API calls if needed
  // For now, just filter locally
})
</script>