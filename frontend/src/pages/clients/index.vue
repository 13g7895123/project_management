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

    <!-- Clients Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="client in filteredClients"
        :key="client.id"
        class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6 hover:shadow-md transition-shadow duration-200"
      >
        <!-- Client Header -->
        <div class="flex justify-between items-start mb-4">
          <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
              {{ client.name }}
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
              認識於 {{ client.how_we_met }}
            </p>
          </div>
          <div class="flex space-x-2">
            <button class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 p-1">
              <PencilIcon class="w-4 h-4" />
            </button>
            <button @click="handleDeleteClient(client.id)" class="text-red-600 hover:text-red-900 dark:text-red-400 p-1">
              <TrashIcon class="w-4 h-4" />
            </button>
          </div>
        </div>

        <!-- Contact Methods -->
        <div class="space-y-2 mb-4">
          <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">聯繫方式</h4>
          <div class="space-y-1">
            <div
              v-for="contact in client.contacts"
              :key="contact.id"
              class="flex items-center space-x-2 text-sm"
            >
              <component :is="getContactIcon(contact.type)" class="w-4 h-4 text-gray-400" />
              <span class="text-gray-600 dark:text-gray-300">{{ contact.type }}:</span>
              <span class="text-gray-900 dark:text-white">{{ contact.value }}</span>
              <span v-if="contact.is_primary" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200">
                主要
              </span>
            </div>
          </div>
        </div>

        <!-- Projects Count -->
        <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
          <span class="text-sm text-gray-500 dark:text-gray-400">
            相關專案: {{ client.projects_count }} 個
          </span>
          <button class="text-sm text-primary-600 hover:text-primary-900 dark:text-primary-400">
            查看專案
          </button>
        </div>

        <!-- Notes -->
        <div v-if="client.notes" class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
          <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">備註</h4>
          <p class="text-sm text-gray-600 dark:text-gray-300">{{ client.notes }}</p>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="filteredClients.length === 0" class="text-center py-12">
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
  
  const response = await getClients({
    search: searchQuery.value
  })
  
  if (response.success) {
    clients.value = response.data.data || response.data || []
  } else {
    error.value = response.error?.message || '載入業主資料失敗'
    // Fallback to mock data for development
    clients.value = [
      {
        id: 1,
        name: 'ABC公司',
        how_we_met: '朋友介紹',
        notes: '重要客戶，主要業務為網站開發',
        projects_count: 3,
        contacts: [
          { id: 1, type: 'phone', value: '02-1234-5678', is_primary: true },
          { id: 2, type: 'email', value: 'contact@abc.com', is_primary: false },
          { id: 3, type: 'line', value: 'abc_company', is_primary: false }
        ]
      },
      {
        id: 2,
        name: 'XYZ企業',
        how_we_met: '網路接洽',
        notes: '需要定期維護服務',
        projects_count: 2,
        contacts: [
          { id: 4, type: 'email', value: 'info@xyz.com', is_primary: true },
          { id: 5, type: 'phone', value: '0912-345-678', is_primary: false }
        ]
      },
      {
        id: 3,
        name: '123公司',
        how_we_met: '展覽會',
        notes: '中小企業，預算有限',
        projects_count: 1,
        contacts: [
          { id: 6, type: 'phone', value: '04-5678-9012', is_primary: true },
          { id: 7, type: 'wechat', value: 'company123', is_primary: false }
        ]
      }
    ]
  }
  
  loading.value = false
}

const clearSearch = () => {
  searchQuery.value = ''
  loadClients()
}

const handleDeleteClient = async (clientId) => {
  if (!confirm('確定要刪除此業主嗎？')) return
  
  const response = await deleteClient(clientId)
  
  if (response.success) {
    // Remove from local array
    clients.value = clients.value.filter(client => client.id !== clientId)
  } else {
    alert(response.error?.message || '刪除失敗')
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