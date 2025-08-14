<template>
  <div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6">
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
        設定
      </h2>
      <p class="text-gray-600 dark:text-gray-300 mb-6">
        管理您的應用程式設定和偏好。
      </p>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <NuxtLink
          to="/settings/theme"
          class="block p-6 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-primary-500 transition-colors duration-200"
        >
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
            主題設定
          </h3>
          <p class="text-gray-600 dark:text-gray-300 text-sm">
            自定義顏色主題和顯示模式
          </p>
        </NuxtLink>
        
        <NuxtLink
          to="/settings/website"
          class="block p-6 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-primary-500 transition-colors duration-200"
        >
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
            網站設定
          </h3>
          <p class="text-gray-600 dark:text-gray-300 text-sm">
            網站名稱、標誌、多語言及功能設定
          </p>
        </NuxtLink>
        
        <NuxtLink
          to="/settings/ui"
          class="block p-6 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-primary-500 transition-colors duration-200"
        >
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
            介面設定
          </h3>
          <p class="text-gray-600 dark:text-gray-300 text-sm">
            頁尾顯示和側邊選單設定
          </p>
        </NuxtLink>
        
        <NuxtLink
          to="/settings/users"
          class="block p-6 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-primary-500 transition-colors duration-200"
        >
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
            用戶管理
          </h3>
          <p class="text-gray-600 dark:text-gray-300 text-sm">
            管理用戶帳戶和權限
          </p>
        </NuxtLink>
      </div>
      
      <!-- Export/Import Section -->
      <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-8">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
          資料管理
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Settings Export/Import -->
          <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6">
            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3">
              設定匯出/匯入
            </h4>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
              匯出或匯入系統設定，包含主題、網站設定等
            </p>
            <div class="space-y-3">
              <button
                @click="exportSettings"
                :disabled="loading.exportSettings"
                class="w-full px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
              >
                {{ loading.exportSettings ? '匯出中...' : '匯出設定' }}
              </button>
              <div class="relative">
                <input
                  ref="settingsFileInput"
                  type="file"
                  accept=".json"
                  @change="importSettings"
                  class="hidden"
                />
                <button
                  @click="$refs.settingsFileInput?.click()"
                  :disabled="loading.importSettings"
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                >
                  {{ loading.importSettings ? '匯入中...' : '匯入設定' }}
                </button>
              </div>
            </div>
          </div>
          
          <!-- Data Export/Import -->
          <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6">
            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3">
              資料匯出/匯入
            </h4>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
              匯出或匯入專案、業主、用戶等資料
            </p>
            <div class="space-y-3">
              <button
                @click="exportData"
                :disabled="loading.exportData"
                class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
              >
                {{ loading.exportData ? '匯出中...' : '匯出資料' }}
              </button>
              <div class="relative">
                <input
                  ref="dataFileInput"
                  type="file"
                  accept=".json"
                  @change="importData"
                  class="hidden"
                />
                <button
                  @click="$refs.dataFileInput?.click()"
                  :disabled="loading.importData"
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                >
                  {{ loading.importData ? '匯入中...' : '匯入資料' }}
                </button>
              </div>
            </div>
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

// Composables
const { getSettings, updateSettings } = useWebsiteSettingsApi()
const { get: apiGet, post: apiPost } = useApi()

// Loading states
const loading = ref({
  exportSettings: false,
  importSettings: false,
  exportData: false,
  importData: false
})

// Notifications
const { showNotification } = useNotifications()

// Export Settings
const exportSettings = async () => {
  loading.value.exportSettings = true
  try {
    const response = await getSettings()
    if (response.success) {
      const settingsData = {
        version: '1.0',
        exportedAt: new Date().toISOString(),
        type: 'settings',
        data: response.data
      }
      
      downloadJSON(settingsData, `settings-export-${formatDateForFilename(new Date())}.json`)
      showNotification('設定匯出成功', 'success')
    } else {
      throw new Error(response.message || '匯出失敗')
    }
  } catch (error) {
    console.error('Export settings error:', error)
    showNotification('設定匯出失敗: ' + error.message, 'error')
  } finally {
    loading.value.exportSettings = false
  }
}

// Import Settings
const importSettings = async (event) => {
  const file = event.target.files[0]
  if (!file) return
  
  loading.value.importSettings = true
  try {
    const fileContent = await readFileAsText(file)
    const importData = JSON.parse(fileContent)
    
    // Validate import data
    if (!importData.type || importData.type !== 'settings') {
      throw new Error('無效的設定檔案格式')
    }
    
    if (!importData.data) {
      throw new Error('設定檔案中沒有找到設定資料')
    }
    
    // Import settings via API
    const response = await updateSettings(importData.data)
    if (response.success) {
      showNotification('設定匯入成功，請重新整理頁面以查看變更', 'success')
      // Refresh the page after a short delay
      setTimeout(() => {
        window.location.reload()
      }, 2000)
    } else {
      throw new Error(response.message || '匯入失敗')
    }
  } catch (error) {
    console.error('Import settings error:', error)
    showNotification('設定匯入失敗: ' + error.message, 'error')
  } finally {
    loading.value.importSettings = false
    // Reset file input
    event.target.value = ''
  }
}

// Export Data
const exportData = async () => {
  loading.value.exportData = true
  try {
    // Get all data from different endpoints
    const [usersResponse, clientsResponse, projectsResponse] = await Promise.all([
      apiGet('/users?per_page=1000').catch(() => ({ success: false, data: [] })),
      apiGet('/clients?per_page=1000').catch(() => ({ success: false, data: [] })),
      apiGet('/projects?per_page=1000').catch(() => ({ success: false, data: [] }))
    ])
    
    const exportData = {
      version: '1.0',
      exportedAt: new Date().toISOString(),
      type: 'data',
      data: {
        users: usersResponse.success ? usersResponse.data : [],
        clients: clientsResponse.success ? clientsResponse.data : [],
        projects: projectsResponse.success ? projectsResponse.data : []
      }
    }
    
    downloadJSON(exportData, `data-export-${formatDateForFilename(new Date())}.json`)
    showNotification('資料匯出成功', 'success')
  } catch (error) {
    console.error('Export data error:', error)
    showNotification('資料匯出失敗: ' + error.message, 'error')
  } finally {
    loading.value.exportData = false
  }
}

// Import Data
const importData = async (event) => {
  const file = event.target.files[0]
  if (!file) return
  
  loading.value.importData = true
  try {
    const fileContent = await readFileAsText(file)
    const importData = JSON.parse(fileContent)
    
    // Validate import data
    if (!importData.type || importData.type !== 'data') {
      throw new Error('無效的資料檔案格式')
    }
    
    if (!importData.data) {
      throw new Error('資料檔案中沒有找到資料')
    }
    
    const { users, clients, projects } = importData.data
    const importResults = []
    
    // Import users (if any)
    if (users && users.length > 0) {
      try {
        const response = await apiPost('/users/import', { users })
        importResults.push(`用戶: ${response.success ? '成功' : '失敗'}`)
      } catch (error) {
        importResults.push(`用戶: 失敗 (${error.message})`)
      }
    }
    
    // Import clients (if any)
    if (clients && clients.length > 0) {
      try {
        const response = await apiPost('/clients/import', { clients })
        importResults.push(`業主: ${response.success ? '成功' : '失敗'}`)
      } catch (error) {
        importResults.push(`業主: 失敗 (${error.message})`)
      }
    }
    
    // Import projects (if any)
    if (projects && projects.length > 0) {
      try {
        const response = await apiPost('/projects/import', { projects })
        importResults.push(`專案: ${response.success ? '成功' : '失敗'}`)
      } catch (error) {
        importResults.push(`專案: 失敗 (${error.message})`)
      }
    }
    
    const message = `資料匯入完成。結果: ${importResults.join(', ')}`
    showNotification(message, 'info')
    
  } catch (error) {
    console.error('Import data error:', error)
    showNotification('資料匯入失敗: ' + error.message, 'error')
  } finally {
    loading.value.importData = false
    // Reset file input
    event.target.value = ''
  }
}

// Utility Functions
const downloadJSON = (data, filename) => {
  const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = filename
  document.body.appendChild(a)
  a.click()
  document.body.removeChild(a)
  URL.revokeObjectURL(url)
}

const readFileAsText = (file) => {
  return new Promise((resolve, reject) => {
    const reader = new FileReader()
    reader.onload = (e) => resolve(e.target.result)
    reader.onerror = (e) => reject(new Error('檔案讀取失敗'))
    reader.readAsText(file)
  })
}

const formatDateForFilename = (date) => {
  return date.toISOString().slice(0, 19).replace(/:/g, '-')
}
</script>