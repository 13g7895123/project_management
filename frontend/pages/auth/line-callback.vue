<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <!-- Loading state -->
    <div v-if="isProcessing" class="text-center">
      <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 dark:border-slate-700/50 p-12">
        <div class="mx-auto w-16 h-16 bg-gradient-to-tr from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
          <svg class="w-8 h-8 text-white animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
          處理 LINE 登入中...
        </h2>
        <p class="text-gray-600 dark:text-gray-400">
          請稍候，正在驗證您的 LINE 帳號
        </p>
      </div>
    </div>

    <!-- Error state -->
    <div v-else-if="error" class="text-center max-w-md">
      <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 dark:border-slate-700/50 p-12">
        <div class="mx-auto w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-2xl flex items-center justify-center mb-6">
          <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
          LINE 登入失敗
        </h2>
        <p class="text-gray-600 dark:text-gray-400 mb-6">
          {{ error }}
        </p>
        <button
          @click="navigateTo('/auth/login')"
          class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transform hover:scale-[1.02] transition-all duration-200"
        >
          返回登入頁面
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: false,
  middleware: 'guest'
})

const route = useRoute()
const authStore = useAuthStore()
const { showSuccess, showError } = useSweetAlert()

const isProcessing = ref(true)
const error = ref('')

// 處理 LINE 回調
onMounted(async () => {
  try {
    // 從 URL 獲取回調參數
    const urlParams = new URLSearchParams(window.location.search)
    const code = urlParams.get('code')
    const state = urlParams.get('state')
    const errorParam = urlParams.get('error')
    const errorDescription = urlParams.get('error_description')

    // 檢查是否有錯誤
    if (errorParam) {
      throw new Error(errorDescription || 'LINE 授權失敗')
    }

    if (!code) {
      throw new Error('缺少授權碼')
    }

    // 調用後端 API 處理回調
    const config = useRuntimeConfig()
    const apiBaseUrl = config.public.apiBaseUrl

    // 構建完整的回調 URL 並發送請求
    const callbackUrl = `${apiBaseUrl}/auth/line/callback${window.location.search}`

    const response = await fetch(callbackUrl, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
    })

    const data = await response.json()

    if (!response.ok || !data.success) {
      throw new Error(data.message || 'LINE 登入失敗')
    }

    // 使用 authStore 處理回調數據
    await authStore.handleLineCallback(data)

    // 顯示成功訊息
    showSuccess('LINE 登入成功', `歡迎回來，${authStore.user?.name || '用戶'}！`)

    // 重定向到首頁
    setTimeout(async () => {
      await navigateTo('/')
    }, 1000)

  } catch (err) {
    console.error('LINE callback error:', err)
    error.value = err.message || 'LINE 登入失敗，請重試'
    showError('LINE 登入失敗', error.value)
  } finally {
    isProcessing.value = false
  }
})
</script>
