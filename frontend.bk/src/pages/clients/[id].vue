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
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">編輯業主</h1>
        <p class="text-gray-600 dark:text-gray-300">修改業主資料</p>
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
          <button @click="loadClient" class="mt-2 text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200 underline">
            重新載入
          </button>
        </div>
      </div>
    </div>

    <!-- Form -->
    <div v-else class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm">
      <form @submit.prevent="submitForm" class="p-6 space-y-6">
        <!-- 基本資訊 -->
        <div>
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">基本資訊</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                業主稱呼 <span class="text-red-500">*</span>
              </label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                placeholder="請輸入業主稱呼"
              />
            </div>
            <div>
              <label for="how_we_met" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                認識方式
              </label>
              <input
                id="how_we_met"
                v-model="form.how_we_met"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                placeholder="例如：朋友介紹、網路接洽..."
              />
            </div>
          </div>
        </div>

        <!-- 備註 -->
        <div>
          <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            備註
          </label>
          <textarea
            id="notes"
            v-model="form.notes"
            rows="4"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
            placeholder="請輸入備註資訊..."
          />
        </div>

        <!-- 聯繫方式 -->
        <div>
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">聯繫方式</h3>
            <button
              type="button"
              @click="addContact"
              class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-primary-900 dark:text-primary-300 dark:hover:bg-primary-800"
            >
              <PlusIcon class="w-4 h-4 mr-1" />
              新增聯繫方式
            </button>
          </div>

          <div class="space-y-4">
            <div
              v-for="(contact, index) in form.contacts"
              :key="index"
              class="flex items-start space-x-4 p-4 border border-gray-200 dark:border-gray-700 rounded-md"
            >
              <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    類型
                  </label>
                  <select
                    v-model="contact.type"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white text-sm"
                  >
                    <option value="">請選擇</option>
                    <option value="phone">電話</option>
                    <option value="mobile">手機</option>
                    <option value="email">電子郵件</option>
                    <option value="line">LINE</option>
                    <option value="wechat">WeChat</option>
                    <option value="telegram">Telegram</option>
                    <option value="other">其他</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    聯繫資訊
                  </label>
                  <input
                    v-model="contact.value"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white text-sm"
                    placeholder="請輸入聯繫資訊"
                  />
                </div>
                <div class="flex items-center space-x-4">
                  <label class="flex items-center">
                    <input
                      v-model="contact.is_primary"
                      type="checkbox"
                      class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                      @change="handlePrimaryChange(index)"
                    />
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">主要聯繫方式</span>
                  </label>
                </div>
              </div>
              <button
                type="button"
                @click="removeContact(index)"
                class="p-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200"
                :disabled="form.contacts.length <= 1"
              >
                <TrashIcon class="w-4 h-4" />
              </button>
            </div>
          </div>
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
            <span v-else>更新業主</span>
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

import { 
  ArrowLeftIcon, 
  PlusIcon, 
  TrashIcon 
} from '@heroicons/vue/24/outline'

const { getClient, updateClient } = useClients()
const route = useRoute()

// Reactive data
const loading = ref(true)
const error = ref(null)
const isSubmitting = ref(false)

// 表單資料
const form = ref({
  name: '',
  how_we_met: '',
  notes: '',
  contacts: [
    {
      type: '',
      value: '',
      is_primary: true
    }
  ]
})

// 載入業主資料
const loadClient = async () => {
  loading.value = true
  error.value = null
  
  try {
    const response = await getClient(route.params.id)
    
    if (response.success && response.data) {
      const clientData = response.data.data || response.data
      
      form.value = {
        name: clientData.name || '',
        how_we_met: clientData.how_we_met || '',
        notes: clientData.notes || '',
        contacts: clientData.contact_methods && clientData.contact_methods.length > 0 
          ? clientData.contact_methods.map(contact => ({
              type: contact.type,
              value: contact.value,
              is_primary: contact.is_primary
            }))
          : [{ type: '', value: '', is_primary: true }]
      }
    } else {
      error.value = response.error?.message || '載入業主資料失敗'
    }
  } catch (err) {
    console.error('Load client error:', err)
    error.value = '載入業主資料時發生錯誤，請稍後再試'
  } finally {
    loading.value = false
  }
}

// 提交表單
const submitForm = async () => {
  try {
    isSubmitting.value = true
    
    const response = await updateClient(route.params.id, form.value)
    
    if (response.success) {
      // 成功後返回列表頁
      await navigateTo('/clients')
    } else {
      alert(response.error?.message || '更新業主資料失敗')
    }
  } catch (error) {
    console.error('Update client error:', error)
    alert('更新業主資料時發生錯誤，請稍後再試')
  } finally {
    isSubmitting.value = false
  }
}

// 新增聯繫方式
const addContact = () => {
  form.value.contacts.push({
    type: '',
    value: '',
    is_primary: false
  })
}

// 移除聯繫方式
const removeContact = (index) => {
  if (form.value.contacts.length > 1) {
    const removedContact = form.value.contacts[index]
    form.value.contacts.splice(index, 1)
    
    // 如果移除的是主要聯繫方式，將第一個設為主要
    if (removedContact.is_primary && form.value.contacts.length > 0) {
      form.value.contacts[0].is_primary = true
    }
  }
}

// 處理主要聯繫方式變更
const handlePrimaryChange = (index) => {
  if (form.value.contacts[index].is_primary) {
    // 將其他聯繫方式設為非主要
    form.value.contacts.forEach((contact, i) => {
      if (i !== index) {
        contact.is_primary = false
      }
    })
  }
}

// 載入業主資料
onMounted(() => {
  loadClient()
})
</script>