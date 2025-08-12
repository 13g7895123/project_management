<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">業主管理</h1>
        <p class="text-gray-600 dark:text-gray-300">管理所有業主資訊和聯繫方式</p>
      </div>
      <NuxtLink
        to="/clients/create"
        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
      >
        <PlusIcon class="w-4 h-4 mr-2" />
        新增業主
      </NuxtLink>
    </div>

    <!-- Search -->
    <div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">搜尋業主</label>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="搜尋業主名稱..."
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
          />
        </div>
        <div class="flex items-end">
          <button
            @click="clearSearch"
            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
          >
            清除搜尋
          </button>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]" role="status">
        <span class="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]">載入中...</span>
      </div>
      <p class="mt-2 text-gray-500 dark:text-gray-400">正在載入業主資料...</p>
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
          <button @click="loadClients" class="mt-2 text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200 underline">
            重新載入
          </button>
        </div>
      </div>
    </div>

    <!-- Clients Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="client in filteredClients"
        :key="client?.id || 'empty'"
        class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6 hover:shadow-md transition-shadow duration-200"
        v-if="client"
      >
        <!-- Client Header -->
        <div class="flex justify-between items-start mb-4">
          <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
              {{ client?.name || '未知業主' }}
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
              認識於 {{ client?.how_we_met || '未記錄' }}
            </p>
          </div>
          <div class="flex space-x-2">
            <button class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 p-1">
              <PencilIcon class="w-4 h-4" />
            </button>
            <button @click="handleDeleteClient(client?.id)" class="text-red-600 hover:text-red-900 dark:text-red-400 p-1" :disabled="!client?.id">
              <TrashIcon class="w-4 h-4" />
            </button>
          </div>
        </div>

        <!-- Contact Methods -->
        <div class="space-y-2 mb-4">
          <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">聯繫方式</h4>
          <div class="space-y-1">
            <div
              v-for="contact in (client?.contacts || [])"
              :key="contact?.id || 'empty'"
              class="flex items-center space-x-2 text-sm"
              v-if="contact"
            >
              <component :is="getContactIcon(contact?.type)" class="w-4 h-4 text-gray-400" />
              <span class="text-gray-600 dark:text-gray-300">{{ contact?.type || '未知' }}:</span>
              <span class="text-gray-900 dark:text-white">{{ contact?.value || '未填寫' }}</span>
              <span v-if="contact?.is_primary" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200">
                主要
              </span>
            </div>
          </div>
        </div>

        <!-- Projects Count -->
        <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
          <span class="text-sm text-gray-500 dark:text-gray-400">
            相關專案: {{ client?.projects_count || 0 }} 個
          </span>
          <button class="text-sm text-primary-600 hover:text-primary-900 dark:text-primary-400">
            查看專案
          </button>
        </div>

        <!-- Notes -->
        <div v-if="client?.notes" class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
          <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">備註</h4>
          <p class="text-sm text-gray-600 dark:text-gray-300">{{ client.notes }}</p>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!loading && !error && filteredClients.length === 0" class="text-center py-12">
      <UsersIcon class="mx-auto h-12 w-12 text-gray-400 mb-4" />
      <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
        {{ searchQuery ? '找不到符合條件的業主' : '尚未新增任何業主' }}
      </h3>
      <p class="text-gray-500 dark:text-gray-400">
        {{ searchQuery ? '請嘗試其他搜尋條件' : '開始建立您的第一個業主資料' }}
      </p>
    </div>
  </div>
</template>

<script setup>
// Page metadata
definePageMeta({
  middleware: 'auth'
})
import { 
  PlusIcon, 
  PencilIcon, 
  TrashIcon, 
  UsersIcon,
  PhoneIcon,
  EnvelopeIcon,
  ChatBubbleLeftIcon,
  DevicePhoneMobileIcon
} from '@heroicons/vue/24/outline'

const { getClients, deleteClient } = useClients()

// Reactive data
const clients = ref([])
const searchQuery = ref('')
const loading = ref(false)
const error = ref(null)

// Computed properties
const filteredClients = computed(() => {
  if (!searchQuery.value) return clients.value
  
  return clients.value.filter(client =>
    client.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    client.how_we_met.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    (client.notes && client.notes.toLowerCase().includes(searchQuery.value.toLowerCase()))
  )
})

// Methods
const loadClients = async () => {
  loading.value = true
  error.value = null
  
  try {
    const response = await getClients({
      search: searchQuery.value
    })
    
    if (response.success && response.data) {
      // Handle paginated response: response.data.data.data contains the actual client array
      // Backend structure: {success: true, data: {data: [...], ...pagination}, message}
      // After useApi wrapper: {success: true, data: {success: true, data: {data: [...]}}, error: null}
      const backendResponse = response.data
      if (backendResponse.success && backendResponse.data && backendResponse.data.data) {
        clients.value = backendResponse.data.data || []
      } else {
        // Handle case where backend response doesn't have expected structure
        clients.value = []
        error.value = backendResponse.message || '載入業主資料失敗：格式錯誤'
      }
    } else {
      clients.value = []
      error.value = response.error?.message || '載入業主資料失敗'
    }
  } catch (err) {
    console.error('Load clients error:', err)
    clients.value = []
    error.value = '載入業主資料時發生錯誤，請稍後再試'
  } finally {
    loading.value = false
  }
}

const clearSearch = () => {
  searchQuery.value = ''
  loadClients()
}

const handleDeleteClient = async (clientId) => {
  if (!clientId) {
    alert('無效的業主ID')
    return
  }
  
  if (!confirm('確定要刪除此業主嗎？此操作無法復原。')) return
  
  try {
    const response = await deleteClient(clientId)
    
    if (response.success) {
      // Remove from local array
      clients.value = clients.value.filter(client => client.id !== clientId)
      
      // Show success message
      const successMessage = response.data?.message || '業主刪除成功'
      alert(successMessage)
    } else {
      const errorMessage = response.error?.message || response.data?.message || '刪除失敗'
      alert(errorMessage)
    }
  } catch (err) {
    console.error('Delete client error:', err)
    alert('刪除業主時發生錯誤，請稍後再試')
  }
}

const getContactIcon = (type) => {
  const icons = {
    phone: PhoneIcon,
    email: EnvelopeIcon,
    line: ChatBubbleLeftIcon,
    wechat: ChatBubbleLeftIcon,
    telegram: ChatBubbleLeftIcon,
    mobile: DevicePhoneMobileIcon
  }
  return icons[type] || PhoneIcon
}

// Load data on mount
onMounted(() => {
  loadClients()
})

// Watch search query for real-time filtering
watch(searchQuery, () => {
  // Debounce API calls if needed
  // For now, just filter locally
})
</script>