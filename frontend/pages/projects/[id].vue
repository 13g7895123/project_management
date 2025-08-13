<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div class="flex items-center space-x-4">
        <button
          @click="$router.back()"
          class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
        >
          <ArrowLeftIcon class="w-5 h-5" />
        </button>
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">編輯專案</h1>
          <p class="text-gray-600 dark:text-gray-300">修改專案記錄</p>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loadingProject" class="bg-blue-50 dark:bg-blue-900/50 border border-blue-200 dark:border-blue-700 rounded-md p-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <div class="h-5 w-5 animate-spin rounded-full border-2 border-solid border-current border-r-transparent"></div>
        </div>
        <div class="ml-3">
          <p class="text-sm font-medium text-blue-800 dark:text-blue-200">正在載入專案資料...</p>
        </div>
      </div>
    </div>

    <!-- Loading Clients Message -->
    <div v-if="loadingClients" class="bg-blue-50 dark:bg-blue-900/50 border border-blue-200 dark:border-blue-700 rounded-md p-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <div class="h-5 w-5 animate-spin rounded-full border-2 border-solid border-current border-r-transparent"></div>
        </div>
        <div class="ml-3">
          <p class="text-sm font-medium text-blue-800 dark:text-blue-200">正在載入業主列表...</p>
        </div>
      </div>
    </div>

    <!-- Success/Error Messages -->
    <div v-if="successMessage" class="bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-700 rounded-md p-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ successMessage }}</p>
        </div>
      </div>
    </div>

    <div v-if="errorMessage" class="bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700 rounded-md p-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ errorMessage }}</p>
        </div>
      </div>
    </div>

    <!-- Form -->
    <div v-if="!loadingProject" class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm">
      <form @submit.prevent="submitForm" class="p-6 space-y-6">
        <!-- 基本資訊 -->
        <div>
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">基本資訊</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                專案名稱 <span class="text-red-500">*</span>
              </label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                placeholder="請輸入專案名稱"
              />
            </div>
            <div>
              <label for="client" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                業主 <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <select
                  id="client"
                  v-model="form.client_id"
                  required
                  :disabled="loadingClients"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <option value="" disabled>
                    {{ loadingClients ? '載入中...' : '請選擇業主' }}
                  </option>
                  <option v-for="client in clients" :key="client?.id || 'empty'" :value="client?.id">
                    {{ client?.name || '未知業主' }}
                  </option>
                </select>
                <div v-if="loadingClients" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                  <div class="h-4 w-4 animate-spin rounded-full border-2 border-solid border-current border-r-transparent" role="status">
                    <span class="sr-only">載入中...</span>
                  </div>
                </div>
              </div>
              <p v-if="clientsError" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ clientsError }}
                <button @click="loadClients" class="ml-2 underline hover:no-underline">
                  重新載入
                </button>
              </p>
              <p v-else-if="!loadingClients && clients.length === 0" class="mt-1 text-sm text-yellow-600 dark:text-yellow-400">
                尚無業主資料，請先
                <NuxtLink to="/clients/create" class="underline hover:no-underline">
                  新增業主
                </NuxtLink>
              </p>
            </div>
          </div>
        </div>

        <!-- 專案詳情 -->
        <div>
          <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            專案描述
          </label>
          <textarea
            id="description"
            v-model="form.description"
            rows="4"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
            placeholder="請描述專案內容..."
          />
        </div>

        <!-- 專案分類與金額 -->
        <div>
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">專案資訊</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                專案類別 <span class="text-red-500">*</span>
              </label>
              <select
                id="category"
                v-model="form.category"
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
              <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                專案金額 <span v-if="form.status !== 'pending_evaluation'" class="text-red-500">*</span>
                <span v-if="form.status === 'pending_evaluation'" class="text-gray-500 text-xs">(待評估狀態可選填)</span>
              </label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">
                  NT$
                </span>
                <input
                  id="amount"
                  v-model.number="form.amount"
                  type="number"
                  min="0"
                  step="1"
                  :required="form.status !== 'pending_evaluation'"
                  class="w-full pl-12 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                  :placeholder="form.status === 'pending_evaluation' ? '待評估時可選填' : '0'"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- 訂金設定 -->
        <div>
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">訂金設定</h3>
          <div class="space-y-4">
            <!-- 是否收取訂金 -->
            <div class="flex items-center">
              <input
                id="requires_deposit"
                v-model="form.requires_deposit"
                type="checkbox"
                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700"
              />
              <label for="requires_deposit" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                收取訂金
              </label>
            </div>
            
            <!-- 訂金金額與收款日期 -->
            <div v-if="form.requires_deposit" class="grid grid-cols-1 md:grid-cols-2 gap-6 pl-6">
              <div>
                <label for="deposit_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  訂金金額 <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">
                    NT$
                  </span>
                  <input
                    id="deposit_amount"
                    v-model.number="form.deposit_amount"
                    type="number"
                    min="0"
                    step="1"
                    :required="form.requires_deposit"
                    class="w-full pl-12 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                    placeholder="請輸入訂金金額"
                  />
                </div>
              </div>
              <div>
                <label for="deposit_received_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  訂金收款日期
                </label>
                <input
                  id="deposit_received_date"
                  v-model="form.deposit_received_date"
                  type="date"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- 日期資訊 -->
        <div>
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">日期資訊</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
              <label for="contact_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                接洽日期 <span class="text-red-500">*</span>
              </label>
              <input
                id="contact_date"
                v-model="form.contact_date"
                type="date"
                required
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
              />
            </div>
            <div>
              <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                開始執行日期
              </label>
              <input
                id="start_date"
                v-model="form.start_date"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
              />
            </div>
            <div>
              <label for="expected_completion_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                預計完成日期
              </label>
              <input
                id="expected_completion_date"
                v-model="form.expected_completion_date"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
              />
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div>
              <label for="completion_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                實際完成日期
              </label>
              <input
                id="completion_date"
                v-model="form.completion_date"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
              />
            </div>
            <div>
              <label for="payment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                收款日期
              </label>
              <input
                id="payment_date"
                v-model="form.payment_date"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
              />
            </div>
          </div>
        </div>

        <!-- 專案狀態 -->
        <div>
          <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            專案狀態
          </label>
          <select
            id="status"
            v-model="form.status"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
          >
            <option value="pending_evaluation">待評估</option>
            <option value="contacted">已接洽</option>
            <option value="in_progress">進行中</option>
            <option value="completed">已完成</option>
            <option value="paid">已收款</option>
          </select>
        </div>

        <!-- 提交按鈕 -->
        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
          <button
            type="button"
            @click="$router.back()"
            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
          >
            取消
          </button>
          <button
            type="submit"
            :disabled="isSubmitting"
            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="isSubmitting">更新中...</span>
            <span v-else>更新專案</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
// Page metadata
definePageMeta({
  middleware: 'auth'
})
import { ArrowLeftIcon } from '@heroicons/vue/24/outline'

const { getProject, updateProject } = useProjects()
const { getClients } = useClients()
const route = useRoute()

// 取得專案 ID
const projectId = computed(() => route.params.id)

// 業主列表
const clients = ref([])
const loadingClients = ref(false)
const clientsError = ref('')

// 專案資料
const loadingProject = ref(false)
const projectError = ref('')

// 表單資料
const form = ref({
  name: '',
  client_id: '',
  description: '',
  category: '',
  amount: null,
  requires_deposit: false,
  deposit_amount: null,
  deposit_received_date: '',
  contact_date: '',
  start_date: '',
  expected_completion_date: '',
  completion_date: '',
  payment_date: '',
  status: 'contacted'
})

const isSubmitting = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

// 載入專案資料
const loadProject = async () => {
  if (!projectId.value) return
  
  loadingProject.value = true
  projectError.value = ''
  
  try {
    const response = await getProject(projectId.value)
    
    if (response.success && response.data) {
      const projectData = response.data.data || response.data
      
      // 填入表單資料
      form.value = {
        name: projectData.name || '',
        client_id: projectData.client_id || '',
        description: projectData.description || '',
        category: projectData.category || '',
        amount: projectData.amount || null,
        requires_deposit: projectData.requires_deposit || false,
        deposit_amount: projectData.deposit_amount || null,
        deposit_received_date: projectData.deposit_received_date ? new Date(projectData.deposit_received_date).toISOString().split('T')[0] : '',
        contact_date: projectData.contact_date ? new Date(projectData.contact_date).toISOString().split('T')[0] : '',
        start_date: projectData.start_date ? new Date(projectData.start_date).toISOString().split('T')[0] : '',
        expected_completion_date: projectData.expected_completion_date ? new Date(projectData.expected_completion_date).toISOString().split('T')[0] : '',
        completion_date: projectData.completion_date ? new Date(projectData.completion_date).toISOString().split('T')[0] : '',
        payment_date: projectData.payment_date ? new Date(projectData.payment_date).toISOString().split('T')[0] : '',
        status: projectData.status || 'contacted'
      }
    } else {
      projectError.value = response.error?.message || '載入專案資料失敗'
      errorMessage.value = projectError.value
    }
  } catch (error) {
    console.error('載入專案失敗:', error)
    projectError.value = '載入專案資料時發生錯誤，請稍後再試'
    errorMessage.value = projectError.value
  } finally {
    loadingProject.value = false
  }
}

// 載入業主列表
const loadClients = async () => {
  loadingClients.value = true
  clientsError.value = ''
  
  try {
    const response = await getClients({ per_page: 100 })
    
    if (response.success && response.data) {
      const backendResponse = response.data
      if (backendResponse.success && backendResponse.data && backendResponse.data.data) {
        clients.value = backendResponse.data.data || []
      } else {
        clients.value = []
        clientsError.value = backendResponse.message || '載入業主資料失敗：格式錯誤'
      }
    } else {
      clients.value = []
      clientsError.value = response.error?.message || '載入業主資料失敗'
    }
  } catch (error) {
    console.error('載入業主失敗:', error)
    clients.value = []
    clientsError.value = '載入業主資料時發生錯誤，請稍後再試'
  } finally {
    loadingClients.value = false
  }
}

const submitForm = async () => {
  try {
    isSubmitting.value = true
    errorMessage.value = ''
    successMessage.value = ''
    
    // 呼叫 API 來更新專案
    const response = await updateProject(projectId.value, form.value)
    
    if (response && response.data) {
      successMessage.value = response.message || '專案更新成功'
      
      // 成功後延遲導向專案列表
      setTimeout(async () => {
        await navigateTo('/projects')
      }, 1500)
    } else {
      throw new Error('專案更新失敗')
    }
  } catch (error) {
    console.error('更新專案失敗:', error)
    errorMessage.value = error.message || '專案更新失敗，請稍後再試'
  } finally {
    isSubmitting.value = false
  }
}

// 載入資料
onMounted(async () => {
  await Promise.all([
    loadClients(),
    loadProject()
  ])
})
</script>