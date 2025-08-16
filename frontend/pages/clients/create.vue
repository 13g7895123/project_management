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
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">新增業主</h1>
        <p class="text-gray-600 dark:text-gray-300">建立新的業主資料</p>
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
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
            placeholder="關於這個業主的其他資訊..."
          />
        </div>

        <!-- 聯繫方式 -->
        <div>
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">聯繫方式</h3>
            <button
              type="button"
              @click="addContact"
              class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-primary-900 dark:text-primary-200"
            >
              <PlusIcon class="w-4 h-4 mr-1" />
              新增聯繫方式
            </button>
          </div>

          <div class="space-y-4">
            <div
              v-for="(contact, index) in form.contacts"
              :key="contact?.id || `contact-${index}`"
              class="flex items-end space-x-4 p-4 border border-gray-200 dark:border-gray-600 rounded-lg"
            >
              <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    聯繫方式類型
                  </label>
                  <select
                    v-model="contact.type"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                  >
                    <option value="phone">電話</option>
                    <option value="mobile">手機</option>
                    <option value="email">Email</option>
                    <option value="line">LINE</option>
                    <option value="wechat">WeChat</option>
                    <option value="telegram">Telegram</option>
                    <option value="other">其他</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    聯繫資訊
                  </label>
                  <input
                    v-model="contact.value"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                    :placeholder="getContactPlaceholder(contact.type)"
                  />
                </div>
                <div class="flex items-center">
                  <label class="flex items-center">
                    <input
                      v-model="contact.is_primary"
                      type="checkbox"
                      @change="updatePrimaryContact(index)"
                      class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                    />
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">設為主要</span>
                  </label>
                </div>
              </div>
              <button
                type="button"
                @click="removeContact(index)"
                class="p-2 text-red-600 hover:text-red-900 dark:text-red-400"
              >
                <TrashIcon class="w-4 h-4" />
              </button>
            </div>
            
            <div v-if="form.contacts.length === 0" class="text-center py-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
              <p class="text-gray-500 dark:text-gray-400">尚未新增任何聯繫方式</p>
              <button
                type="button"
                @click="addContact"
                class="mt-2 text-primary-600 hover:text-primary-500 dark:text-primary-400"
              >
                點擊新增第一個聯繫方式
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
            <span v-if="isSubmitting">儲存中...</span>
            <span v-else>儲存業主</span>
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
import { ArrowLeftIcon, PlusIcon, TrashIcon } from '@heroicons/vue/24/outline'

const form = reactive({
  name: '',
  how_we_met: '',
  notes: '',
  contacts: []
})

const isSubmitting = ref(false)
let contactIdCounter = 1

const addContact = () => {
  const newContact = {
    id: contactIdCounter++,
    type: 'phone',
    value: '',
    is_primary: form.contacts.length === 0
  }
  form.contacts.push(newContact)
  console.log('Added contact:', newContact, 'Total contacts:', form.contacts.length)
}

const removeContact = (index) => {
  const wasPrimary = form.contacts[index].is_primary
  form.contacts.splice(index, 1)
  
  // 如果刪除的是主要聯繫方式，將第一個設為主要
  if (wasPrimary && form.contacts.length > 0) {
    form.contacts[0].is_primary = true
  }
}

const updatePrimaryContact = (index) => {
  if (form.contacts[index].is_primary) {
    // 將其他聯繫方式設為非主要
    form.contacts.forEach((contact, i) => {
      if (i !== index) {
        contact.is_primary = false
      }
    })
  }
}

const getContactPlaceholder = (type) => {
  const placeholders = {
    phone: '02-1234-5678',
    mobile: '0912-345-678',
    email: 'example@email.com',
    line: 'LINE ID',
    wechat: 'WeChat ID',
    telegram: 'Telegram ID',
    other: '其他聯繫資訊'
  }
  return placeholders[type] || '請輸入聯繫資訊'
}

const { createClient } = useClients()
const { showSuccess, showError, showLoading, close } = useSweetAlert()

const submitForm = async () => {
  try {
    isSubmitting.value = true
    
    // 驗證表單
    if (!form.name.trim()) {
      showError('表單驗證失敗', '請輸入業主稱呼')
      return
    }
    
    showLoading('建立業主中...', '正在儲存業主資料')
    
    // 呼叫 API 來儲存業主資料
    const response = await createClient(form)
    
    close()
    if (response.success) {
      await showSuccess('業主建立成功', `業主「${form.name}」已成功建立`)
      // 成功後導向業主列表
      await navigateTo('/clients')
    } else {
      throw new Error(response.error?.message || '儲存業主失敗')
    }
  } catch (error) {
    close()
    console.error('儲存業主失敗:', error)
    showError('業主建立失敗', error.message || '無法建立業主，請稍後再試')
  } finally {
    isSubmitting.value = false
  }
}

// 初始化時添加一個預設聯繫方式
onMounted(() => {
  addContact()
})
</script>