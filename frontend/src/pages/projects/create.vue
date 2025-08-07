<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
      <button
        @click="$router.back()"
        class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
      >
        <ArrowLeftIcon class="w-5 h-5" />
      </button>
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">新增專案</h1>
        <p class="text-gray-600 dark:text-gray-300">建立新的專案記錄</p>
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
    <div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm">
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
              <select
                id="client"
                v-model="form.client_id"
                required
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
              >
                <option value="">請選擇業主</option>
                <option v-for="client in clients" :key="client.id" :value="client.id">
                  {{ client.name }}
                </option>
              </select>
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
              專案金額 <span class="text-red-500">*</span>
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
                required
                class="w-full pl-12 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                placeholder="0"
              />
            </div>
          </div>
        </div>

        <!-- 日期資訊 -->
        <div>
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">日期資訊</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
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
              <label for="completion_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                完成日期
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
            <span v-if="isSubmitting">儲存中...</span>
            <span v-else>儲存專案</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ArrowLeftIcon } from '@heroicons/vue/24/outline'

const { createProject } = useProjects()
const { getClients } = useClients()

// 業主列表
const clients = ref([])

// 表單資料
const form = ref({
  name: '',
  client_id: '',
  description: '',
  category: '',
  amount: null,
  contact_date: '',
  start_date: '',
  completion_date: '',
  payment_date: '',
  status: 'contacted'
})

const isSubmitting = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

// 載入業主列表
const loadClients = async () => {
  try {
    const response = await getClients()
    if (response.success) {
      clients.value = response.data.data
    } else {
      clients.value = []
    }
  } catch (error) {
    console.error('載入業主失敗:', error)
    clients.value = []
  }
}

const submitForm = async () => {
  try {
    isSubmitting.value = true
    errorMessage.value = ''
    successMessage.value = ''
    
    // 呼叫 API 來儲存專案
    const response = await createProject(form.value)
    
    if (response && response.data) {
      successMessage.value = response.message || '專案建立成功'
      
      // 成功後延遲導向專案列表
      setTimeout(async () => {
        await navigateTo('/projects')
      }, 1500)
    } else {
      throw new Error('專案建立失敗')
    }
  } catch (error) {
    console.error('儲存專案失敗:', error)
    errorMessage.value = error.message || '專案建立失敗，請稍後再試'
  } finally {
    isSubmitting.value = false
  }
}

// 設定今天的日期為預設接洽日期並載入業主列表
onMounted(async () => {
  const today = new Date().toISOString().split('T')[0]
  form.value.contact_date = today
  await loadClients()
})
</script>