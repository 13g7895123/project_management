<template>
  <div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6">
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
        網站設定
      </h2>
      
      <!-- Website Identity Section -->
      <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          網站識別
        </h3>
        
        <div class="space-y-4">
          <!-- Website Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              主要網站名稱
            </label>
            <input
              v-model="websiteName"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
              placeholder="專案管理系統"
            />
          </div>
          
          <!-- Secondary Website Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              次要網站名稱
            </label>
            <input
              v-model="websiteSecondaryName"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
              placeholder="Project Management"
            />
          </div>
          
          <!-- Website Title -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              網站標題（瀏覽器標籤頁顯示）
            </label>
            <input
              v-model="websiteTitle"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
              placeholder="專案管理系統"
            />
          </div>
        </div>
      </div>
      
      <!-- Logo Settings Section -->
      <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          標誌設定
        </h3>
        
        <div class="space-y-4">
          <!-- Logo Display Toggle -->
          <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
            <div>
              <h4 class="font-medium text-gray-900 dark:text-white">使用標誌</h4>
              <p class="text-sm text-gray-600 dark:text-gray-400">使用標誌取代文字名稱顯示</p>
            </div>
            <button
              @click="toggleShowLogo"
              class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
              :class="[
                showLogo 
                  ? 'bg-primary-500' 
                  : 'bg-gray-200 dark:bg-gray-700'
              ]"
            >
              <span
                class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                :class="[
                  showLogo ? 'translate-x-6' : 'translate-x-1'
                ]"
              />
            </button>
          </div>
          
          <!-- Logo Upload -->
          <div v-if="showLogo || logoUrl">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              標誌圖片
            </label>
            <div class="flex items-center space-x-4">
              <div v-if="logoUrl" class="flex-shrink-0">
                <img :src="logoUrl" alt="Logo Preview" class="w-16 h-16 object-contain border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700" />
              </div>
              <div class="flex-1">
                <input
                  ref="logoFileInput"
                  type="file"
                  accept="image/*"
                  @change="handleLogoUpload"
                  class="hidden"
                />
                <button
                  @click="$refs.logoFileInput.click()"
                  class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors duration-200"
                >
                  {{ logoUrl ? '更換標誌' : '上傳標誌' }}
                </button>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                  支援 JPG、PNG、SVG 格式，建議尺寸 200x50px
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Favicon Settings -->
      <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          網站圖示設定
        </h3>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Favicon（網站圖示）
            </label>
            <div class="flex items-center space-x-4">
              <div class="flex-shrink-0">
                <img :src="faviconUrl" alt="Favicon Preview" class="w-8 h-8 object-contain border border-gray-300 dark:border-gray-600 rounded bg-gray-50 dark:bg-gray-700" />
              </div>
              <div class="flex-1">
                <input
                  ref="faviconFileInput"
                  type="file"
                  accept="image/x-icon,image/png,image/svg+xml"
                  @change="handleFaviconUpload"
                  class="hidden"
                />
                <button
                  @click="$refs.faviconFileInput.click()"
                  class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                >
                  更換 Favicon
                </button>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                  支援 ICO、PNG、SVG 格式，建議尺寸 32x32px
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Feature Settings Section -->
      <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          功能設定
        </h3>
        
        <div class="space-y-4">
          <!-- Multilingual Support -->
          <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
            <div>
              <h4 class="font-medium text-gray-900 dark:text-white">多語言支援</h4>
              <p class="text-sm text-gray-600 dark:text-gray-400">啟用多語言切換功能</p>
            </div>
            <button
              @click="toggleMultilingual"
              class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
              :class="[
                enableMultilingual 
                  ? 'bg-primary-500' 
                  : 'bg-gray-200 dark:bg-gray-700'
              ]"
            >
              <span
                class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                :class="[
                  enableMultilingual ? 'translate-x-6' : 'translate-x-1'
                ]"
              />
            </button>
          </div>
          
          <!-- Dark Mode -->
          <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
            <div>
              <h4 class="font-medium text-gray-900 dark:text-white">深色模式</h4>
              <p class="text-sm text-gray-600 dark:text-gray-400">允許用戶切換深色/淺色主題</p>
            </div>
            <button
              @click="toggleDarkMode"
              class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
              :class="[
                enableDarkMode 
                  ? 'bg-primary-500' 
                  : 'bg-gray-200 dark:bg-gray-700'
              ]"
            >
              <span
                class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                :class="[
                  enableDarkMode ? 'translate-x-6' : 'translate-x-1'
                ]"
              />
            </button>
          </div>
          
          <!-- Search Functionality -->
          <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
            <div>
              <h4 class="font-medium text-gray-900 dark:text-white">搜尋功能</h4>
              <p class="text-sm text-gray-600 dark:text-gray-400">在頂部導航欄顯示搜尋按鈕</p>
            </div>
            <button
              @click="toggleSearch"
              class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
              :class="[
                enableSearch 
                  ? 'bg-primary-500' 
                  : 'bg-gray-200 dark:bg-gray-700'
              ]"
            >
              <span
                class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                :class="[
                  enableSearch ? 'translate-x-6' : 'translate-x-1'
                ]"
              />
            </button>
          </div>
          
          <!-- Notifications -->
          <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
            <div>
              <h4 class="font-medium text-gray-900 dark:text-white">通知功能</h4>
              <p class="text-sm text-gray-600 dark:text-gray-400">在頂部導航欄顯示通知鈴鐺</p>
            </div>
            <button
              @click="toggleNotifications"
              class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
              :class="[
                enableNotifications 
                  ? 'bg-primary-500' 
                  : 'bg-gray-200 dark:bg-gray-700'
              ]"
            >
              <span
                class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                :class="[
                  enableNotifications ? 'translate-x-6' : 'translate-x-1'
                ]"
              />
            </button>
          </div>
          
          <!-- Footer Display -->
          <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
            <div>
              <h4 class="font-medium text-gray-900 dark:text-white">頁尾顯示</h4>
              <p class="text-sm text-gray-600 dark:text-gray-400">在頁面底部顯示頁尾資訊</p>
            </div>
            <button
              @click="toggleFooter"
              class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
              :class="[
                showFooter 
                  ? 'bg-primary-500' 
                  : 'bg-gray-200 dark:bg-gray-700'
              ]"
            >
              <span
                class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                :class="[
                  showFooter ? 'translate-x-6' : 'translate-x-1'
                ]"
              />
            </button>
          </div>
          
        </div>
      </div>
      
      <!-- Preview Section -->
      <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          預覽
        </h3>
        <div class="p-6 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-700">
          <div class="flex items-center space-x-3 mb-4">
            <div v-if="showLogo && logoUrl" class="flex-shrink-0">
              <img :src="logoUrl" alt="Logo" class="h-8 object-contain" />
            </div>
            <div v-else class="flex flex-col">
              <span class="font-bold text-lg text-gray-900 dark:text-white">{{ websiteName }}</span>
              <span v-if="websiteSecondaryName" class="text-sm text-gray-600 dark:text-gray-400">{{ websiteSecondaryName }}</span>
            </div>
          </div>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            頁面標題：{{ websiteTitle }}
          </p>
        </div>
      </div>
      
      <!-- Action Buttons -->
      <div class="flex justify-end space-x-3">
        <button
          @click="resetToDefaults"
          class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
        >
          重置為預設值
        </button>
        <button
          @click="saveSettings"
          class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors duration-200"
        >
          儲存設定
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
// Page metadata
definePageMeta({
  middleware: 'auth'
})

const websiteSettingsStore = useWebsiteSettingsStore()
const settingsStore = useSettingsStore()
const { showSuccess, showError, showInfo } = useSweetAlert()

// Reactive refs for form inputs
const {
  websiteName,
  websiteSecondaryName,
  websiteTitle,
  showLogo,
  logoUrl,
  faviconUrl,
  enableMultilingual,
  enableSearch,
  enableNotifications,
  showFooter,
  enableDarkMode
} = storeToRefs(websiteSettingsStore)

// Methods
const toggleShowLogo = () => {
  showLogo.value = !showLogo.value
  websiteSettingsStore.saveSettings()
}

const toggleMultilingual = async () => {
  enableMultilingual.value = !enableMultilingual.value
  await websiteSettingsStore.saveSettings()
  showSuccess('多語系設定已更新', enableMultilingual.value ? '已啟用多語系功能' : '已停用多語系功能')
}

const toggleDarkMode = async () => {
  enableDarkMode.value = !enableDarkMode.value
  await websiteSettingsStore.saveSettings()
  
  // If dark mode is disabled, force light mode
  if (!enableDarkMode.value) {
    const { setTheme } = useTheme()
    setTheme('light')
  }
  
  showSuccess('深色模式設定已更新', enableDarkMode.value ? '已啟用深色模式' : '已停用深色模式')
}

const toggleSearch = async () => {
  enableSearch.value = !enableSearch.value
  await websiteSettingsStore.saveSettings()
  showSuccess('搜尋功能設定已更新', enableSearch.value ? '已啟用搜尋功能' : '已停用搜尋功能')
}

const toggleNotifications = async () => {
  enableNotifications.value = !enableNotifications.value
  await websiteSettingsStore.saveSettings()
  showSuccess('通知功能設定已更新', enableNotifications.value ? '已啟用通知功能' : '已停用通知功能')
}

const toggleFooter = async () => {
  const newValue = !showFooter.value
  showFooter.value = newValue
  
  try {
    await websiteSettingsStore.saveSettings()
    
    // Also update the settings store for backward compatibility
    settingsStore.showFootbar = newValue
    
    // Force a small delay to ensure state synchronization
    await nextTick()
    
    showSuccess('頁尾顯示設定已更新', newValue ? '已顯示頁尾' : '已隱藏頁尾')
  } catch (error) {
    // If save fails, revert the state
    showFooter.value = !newValue
    showError('設定保存失敗', '無法更新頁尾顯示設定，請稍後再試')
  }
}


const handleLogoUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return
  
  try {
    await websiteSettingsStore.uploadLogo(file)
    // Show success message (you could add a notification system here)
    console.log('標誌上傳成功')
  } catch (error) {
    console.error('標誌上傳失敗:', error)
    // Show error message
  }
}

const handleFaviconUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return
  
  try {
    await websiteSettingsStore.uploadFavicon(file)
    console.log('Favicon 更新成功')
  } catch (error) {
    console.error('Favicon 更新失敗:', error)
  }
}

const saveSettings = async () => {
  try {
    await websiteSettingsStore.saveSettings()
    showSuccess('網站設定已儲存', '所有設定變更已成功儲存')
  } catch (error) {
    showError('儲存失敗', '無法儲存網站設定，請稍後再試')
  }
}

const resetToDefaults = async () => {
  try {
    await websiteSettingsStore.resetToDefaults()
    showSuccess('設定已重置', '所有設定已重置為預設值')
  } catch (error) {
    showError('重置失敗', '無法重置設定，請稍後再試')
  }
}

// Watch for toggle state changes for debugging purposes (can be removed in production)
onMounted(() => {
  console.log('Initial toggle states:', {
    enableMultilingual: enableMultilingual.value,
    enableSearch: enableSearch.value,
    enableNotifications: enableNotifications.value,
    showFooter: showFooter.value,
    enableDarkMode: enableDarkMode.value
  })
})

// Watch for changes in input fields and auto-save
watch([websiteName, websiteSecondaryName, websiteTitle], () => {
  websiteSettingsStore.saveSettings()
}, { deep: true })
</script>