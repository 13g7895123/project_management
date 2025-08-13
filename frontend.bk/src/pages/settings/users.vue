<template>
  <div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
          {{ t('nav.user_management') }}
        </h2>
        <div class="flex items-center space-x-4">
          <!-- Search -->
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="搜尋用戶..."
              class="w-64 px-4 py-2 pl-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
            />
            <MagnifyingGlassIcon class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" />
          </div>
          
          <!-- Add User Button -->
          <button
            @click="showCreateModal = true"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
          >
            <PlusIcon class="w-4 h-4 mr-2" />
            新增用戶
          </button>
        </div>
      </div>

      <!-- Access Denied for Non-Admin -->
      <div v-if="!authStore.isAdmin" class="text-center py-12">
        <ShieldExclamationIcon class="w-12 h-12 text-red-500 mx-auto mb-4" />
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ t('auth.access_denied') }}</h3>
        <p class="text-gray-600 dark:text-gray-400">{{ t('auth.admin_only_feature') }}</p>
      </div>

      <!-- Users Table -->
      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-200 tracking-wide">
                用戶資訊
              </th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-200 tracking-wide">
                角色權限
              </th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-200 tracking-wide">
                帳號狀態
              </th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-200 tracking-wide">
                最後登入時間
              </th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-200 tracking-wide">
                操作功能
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
              <!-- User Info -->
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="w-10 h-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                    <UserCircleIcon class="w-8 h-8 text-gray-500 dark:text-gray-400" />
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ user?.name || '未知用戶' }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ user?.email || '未設定Email' }}</div>
                    <div v-if="user?.username" class="text-xs text-gray-400 dark:text-gray-500">@{{ user.username }}</div>
                  </div>
                </div>
              </td>

              <!-- Role -->
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  class="inline-flex px-3 py-1 text-sm font-medium rounded-full"
                  :class="{
                    'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400': user?.role === 'admin',
                    'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400': user?.role === 'user'
                  }"
                >
                  {{ user?.role === 'admin' ? '管理員' : '一般用戶' }}
                </span>
              </td>

              <!-- Status -->
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  class="inline-flex px-3 py-1 text-sm font-medium rounded-full"
                  :class="{
                    'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': user?.status === 'active',
                    'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400': user?.status === 'inactive',
                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': user?.status === 'suspended'
                  }"
                >
                  {{ 
                    user?.status === 'active' ? '正常使用' : 
                    user?.status === 'suspended' ? '已暫停' :
                    '已停用'
                  }}
                </span>
              </td>

              <!-- Last Login -->
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                {{ formatDate(user?.last_login_at) }}
              </td>

              <!-- Actions -->
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex items-center space-x-2">
                  <!-- Toggle Status -->
                  <button
                    v-if="user?.id !== authStore.user?.id"
                    @click="toggleStatus(user)"
                    class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition-colors duration-200"
                    :disabled="!user?.id"
                  >
                    {{ user?.status === 'active' ? t('auth.deactivate') : t('auth.activate') }}
                  </button>
                  
                  <!-- Edit -->
                  <button
                    @click="editUser(user)"
                    class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 transition-colors duration-200"
                  >
                    {{ t('common.edit') }}
                  </button>
                  
                  <!-- Delete -->
                  <button
                    v-if="user?.id !== authStore.user?.id"
                    @click="handleDeleteUser(user)"
                    class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors duration-200"
                    :disabled="!user?.id"
                  >
                    刪除
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- No Users Found -->
        <div v-if="!loading && filteredUsers.length === 0" class="text-center py-12">
          <UsersIcon class="w-12 h-12 text-gray-400 mx-auto mb-4" />
          <p class="text-gray-500 dark:text-gray-400">{{ t('auth.no_users_found') }}</p>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-500 mx-auto mb-4"></div>
          <p class="text-gray-500 dark:text-gray-400">載入中...</p>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="!loading && filteredUsers.length > 0 && pagination.last_page > 1" class="mt-6 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-4">
        <div class="text-sm text-gray-700 dark:text-gray-300">
          顯示 {{ pagination.from }} 到 {{ pagination.to }} 筆，共 {{ pagination.total }} 筆用戶
        </div>
        <div class="flex items-center space-x-2">
          <button
            @click="goToPrevPage()"
            :disabled="pagination.current_page <= 1 || loading"
            class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            上一頁
          </button>
          
          <span class="text-sm text-gray-700 dark:text-gray-300">
            第 {{ pagination.current_page }} 頁，共 {{ pagination.last_page }} 頁
          </span>
          
          <button
            @click="goToNextPage()"
            :disabled="pagination.current_page >= pagination.last_page || loading"
            class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            下一頁
          </button>
        </div>
      </div>
    </div>

    <!-- Edit User Modal -->
    <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
      <div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
          {{ t('auth.edit_user') }}
        </h3>
        
        <div class="space-y-4">
          <!-- Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ t('auth.full_name') }}
            </label>
            <input
              v-model="editForm.name"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
            />
          </div>
          
          <!-- Email -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ t('auth.email') }}
            </label>
            <input
              v-model="editForm.email"
              type="email"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
            />
          </div>

          <!-- Role -->
          <div v-if="editForm.id !== authStore.user?.id">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ t('auth.role') }}
            </label>
            <select
              v-model="editForm.role"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
            >
              <option value="user">{{ t('auth.role_user') }}</option>
              <option value="admin">{{ t('auth.role_admin') }}</option>
            </select>
          </div>
        </div>

        <!-- Modal Actions -->
        <div class="flex justify-end space-x-3 mt-6">
          <button
            @click="showEditModal = false"
            class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
          >
            {{ t('common.cancel') }}
          </button>
          <button
            @click="saveUser"
            class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors duration-200"
          >
            {{ t('common.save') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Create User Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
      <div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-xl max-w-md w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
          新增用戶
        </h3>
        
        <div class="space-y-4">
          <!-- Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              姓名 *
            </label>
            <input
              v-model="createForm.name"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
            />
          </div>
          
          <!-- Username -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              用戶名 *
            </label>
            <input
              v-model="createForm.username"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
            />
          </div>
          
          <!-- Email -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Email *
            </label>
            <input
              v-model="createForm.email"
              type="email"
              required
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
            />
          </div>

          <!-- Password -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              密碼 *
            </label>
            <input
              v-model="createForm.password"
              type="password"
              required
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
            />
          </div>

          <!-- Confirm Password -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              確認密碼 *
            </label>
            <input
              v-model="createForm.password_confirmation"
              type="password"
              required
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
            />
          </div>

          <!-- Role -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              角色
            </label>
            <select
              v-model="createForm.role"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
            >
              <option value="user">一般用戶</option>
              <option value="admin">管理員</option>
            </select>
          </div>
        </div>

        <!-- Modal Actions -->
        <div class="flex justify-end space-x-3 mt-6">
          <button
            @click="showCreateModal = false"
            class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
          >
            取消
          </button>
          <button
            @click="saveNewUser"
            class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors duration-200"
          >
            建立
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Page metadata
definePageMeta({
  middleware: 'admin'
})
import {
  MagnifyingGlassIcon,
  ShieldExclamationIcon,
  UsersIcon,
  PlusIcon,
  UserCircleIcon
} from '@heroicons/vue/24/outline'

const { t } = useI18n()
const authStore = useAuthStore()
const { getUsers, updateUser, deleteUser, toggleUserStatus, createUser } = useUsers()

// Reactive data
const searchQuery = ref('')
const showEditModal = ref(false)
const showCreateModal = ref(false)
const editForm = ref({})
const createForm = ref({
  name: '',
  username: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: 'user',
  status: 'active'
})
const users = ref([])
const loading = ref(false)
const error = ref(null)
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
  from: 0,
  to: 0
})
const currentPage = ref(1)

// Load users data
const loadUsers = async (page = 1) => {
  try {
    loading.value = true
    error.value = null
    const response = await getUsers({
      search: searchQuery.value,
      page: page,
      per_page: 15,
      sort: '-created_at'
    })
    
    if (response.success) {
      users.value = response.data.data || []
      pagination.value = response.data.pagination || {
        current_page: 1,
        last_page: 1,
        per_page: 15,
        total: 0,
        from: 0,
        to: 0
      }
      currentPage.value = page
    } else {
      throw new Error(response.message || 'Failed to load users')
    }
  } catch (err) {
    error.value = err.message
    console.error('Failed to load users:', err)
    users.value = []
    pagination.value = {
      current_page: 1,
      last_page: 1,
      per_page: 15,
      total: 0,
      from: 0,
      to: 0
    }
  } finally {
    loading.value = false
  }
}

// Since backend handles search, we don't need client-side filtering
const filteredUsers = computed(() => {
  return users.value
})

// Pagination helpers
const goToPage = async (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    await loadUsers(page)
  }
}

const goToPrevPage = async () => {
  if (pagination.value.current_page > 1) {
    await goToPage(pagination.value.current_page - 1)
  }
}

const goToNextPage = async () => {
  if (pagination.value.current_page < pagination.value.last_page) {
    await goToPage(pagination.value.current_page + 1)
  }
}

// Watch search query and reload users from first page
watch(searchQuery, async () => {
  currentPage.value = 1
  await loadUsers(1)
}, { debounce: 500 })

// Format date for display
const formatDate = (date) => {
  if (!date) return '未曾登入'
  try {
    return new Date(date).toLocaleDateString('zh-TW', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit',
      hour12: false
    })
  } catch (error) {
    console.error('Invalid date:', date)
    return '日期格式錯誤'
  }
}

// Toggle user status
const toggleStatus = async (user) => {
  if (!user?.id) return
  try {
    const newStatus = user.status === 'active' ? 'inactive' : 'active'
    const response = await toggleUserStatus(user.id, newStatus)
    
    if (response.success) {
      await loadUsers() // Reload users to reflect changes
    } else {
      throw new Error(response.message || 'Failed to toggle user status')
    }
  } catch (error) {
    console.error('Failed to toggle user status:', error)
    alert('Failed to toggle user status: ' + error.message)
  }
}

// Edit user
const editUser = (user) => {
  if (!user) return
  editForm.value = { ...user }
  showEditModal.value = true
}

// Save user changes
const saveUser = async () => {
  if (!editForm.value?.id) return
  try {
    const response = await updateUser(editForm.value.id, {
      name: editForm.value.name,
      username: editForm.value.username,
      email: editForm.value.email,
      role: editForm.value.role,
      phone: editForm.value.phone,
      bio: editForm.value.bio,
      company: editForm.value.company,
      position: editForm.value.position,
      location: editForm.value.location,
      website: editForm.value.website
    })
    
    if (response.success) {
      showEditModal.value = false
      await loadUsers() // Reload users to reflect changes
    } else {
      throw new Error(response.message || 'Failed to update user')
    }
  } catch (error) {
    console.error('Failed to update user:', error)
    alert('Failed to update user: ' + error.message)
  }
}

// Create new user
const saveNewUser = async () => {
  try {
    const response = await createUser(createForm.value)
    
    if (response.success) {
      showCreateModal.value = false
      // Reset form
      createForm.value = {
        name: '',
        username: '',
        email: '',
        password: '',
        password_confirmation: '',
        role: 'user',
        status: 'active'
      }
      await loadUsers() // Reload users to reflect changes
    } else {
      throw new Error(response.message || 'Failed to create user')
    }
  } catch (error) {
    console.error('Failed to create user:', error)
    alert('Failed to create user: ' + error.message)
  }
}

// Delete user
const handleDeleteUser = async (user) => {
  if (!user?.id) {
    console.error('No user ID provided for deletion')
    alert('錯誤：無法刪除用戶，缺少用戶 ID')
    return
  }
  
  // Check if user is trying to delete themselves (frontend validation)
  if (user.id === authStore.user?.id) {
    alert('錯誤：無法刪除自己的帳號')
    return
  }
  
  // Check if this is the last admin user (frontend validation)
  const adminUsers = users.value.filter(u => u.role === 'admin')
  if (user.role === 'admin' && adminUsers.length <= 1) {
    alert('錯誤：無法刪除最後一個管理員帳號')
    return
  }
  
  if (confirm(`確定要刪除用戶「${user.name}」嗎？此操作無法復原。`)) {
    try {
      console.log('Attempting to delete user:', user.id, user.name)
      const response = await deleteUser(user.id)
      console.log('Delete user response:', response)
      
      if (response.success) {
        console.log('User deleted successfully')
        alert('用戶刪除成功')
        await loadUsers() // Reload users to reflect changes
      } else {
        console.error('Delete user failed:', response)
        const errorMessage = response.error?.message || response.message || response.error || 'Failed to delete user'
        throw new Error(errorMessage)
      }
    } catch (error) {
      console.error('Failed to delete user:', error)
      
      // Provide more specific error messages
      let userMessage = '刪除用戶失敗'
      if (error.message) {
        if (error.message.includes('cannot delete your own account') || error.message.includes('自己')) {
          userMessage = '錯誤：無法刪除自己的帳號'
        } else if (error.message.includes('last admin') || error.message.includes('最後一個管理員')) {
          userMessage = '錯誤：無法刪除最後一個管理員帳號'
        } else if (error.message.includes('權限不足') || error.message.includes('403')) {
          userMessage = '錯誤：權限不足'
        } else if (error.message.includes('登入已過期') || error.message.includes('401')) {
          userMessage = '錯誤：登入已過期，請重新登入'
        } else {
          userMessage = `刪除用戶失敗：${error.message}`
        }
      }
      
      alert(userMessage)
    }
  }
}

// Load users on mount
onMounted(async () => {
  await loadUsers()
})
</script>