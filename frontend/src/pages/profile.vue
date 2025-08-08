<template>
  <div class="space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ t('profile.title') }}</h1>
      <p class="text-gray-600 dark:text-gray-300">{{ t('profile.subtitle') }}</p>
    </div>

    <!-- Profile Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left Column - Profile Card -->
      <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6">
          <!-- Avatar Section -->
          <div class="text-center mb-6">
            <div class="relative inline-block">
              <img
                :src="profileForm.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(profileForm.name || 'User')}&background=6366f1&color=fff`"
                :alt="profileForm.name || 'User Avatar'"
                class="w-24 h-24 rounded-full mx-auto mb-4 object-cover border-4 border-white dark:border-gray-700 shadow-lg"
              />
              <label
                for="avatar-upload"
                class="absolute bottom-0 right-0 bg-primary-600 hover:bg-primary-700 text-white rounded-full p-2 cursor-pointer shadow-lg transition-colors duration-200"
              >
                <CameraIcon class="w-4 h-4" />
                <input
                  id="avatar-upload"
                  type="file"
                  accept="image/*"
                  @change="handleAvatarUpload"
                  class="hidden"
                />
              </label>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ profileForm.name || 'User' }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">@{{ profileForm.username }}</p>
            <div class="mt-2">
              <span 
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                :class="{
                  'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': user?.status === 'active',
                  'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': user?.status === 'inactive'
                }"
              >
                {{ user?.status === 'active' ? t('profile.active') : t('profile.inactive') }}
              </span>
            </div>
          </div>

          <!-- Quick Avatar Actions -->
          <div class="space-y-2 mb-6">
            <button
              @click="triggerAvatarUpload"
              class="w-full px-4 py-2 text-sm font-medium text-primary-600 bg-primary-50 hover:bg-primary-100 dark:bg-primary-900 dark:text-primary-200 rounded-lg transition-colors duration-200"
            >
              <CameraIcon class="w-4 h-4 inline mr-2" />
              {{ t('profile.change_avatar') }}
            </button>
            <button
              v-if="profileForm.avatar && !isDefaultAvatar(profileForm.avatar)"
              @click="removeAvatar"
              class="w-full px-4 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 dark:bg-red-900 dark:text-red-200 rounded-lg transition-colors duration-200"
            >
              <TrashIcon class="w-4 h-4 inline mr-2" />
              {{ t('profile.remove_avatar') }}
            </button>
          </div>

          <!-- Account Info -->
          <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-4">{{ t('profile.account_info') }}</h4>
            <div class="space-y-3">
              <div class="flex justify-between text-sm">
                <span class="text-gray-500 dark:text-gray-400">{{ t('profile.role') }}</span>
                <span class="text-gray-900 dark:text-white font-medium">
                  {{ user?.role === 'admin' ? t('auth.role_admin') : t('auth.role_user') }}
                </span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-500 dark:text-gray-400">{{ t('profile.member_since') }}</span>
                <span class="text-gray-900 dark:text-white">{{ formatDate(user?.createdAt) }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-500 dark:text-gray-400">{{ t('profile.last_login') }}</span>
                <span class="text-gray-900 dark:text-white">{{ formatDate(user?.lastLogin) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column - Forms -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Personal Information Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ t('profile.personal_info') }}</h3>
          </div>
          <form @submit.prevent="updateProfile" class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Name -->
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ t('profile.name') }} <span class="text-red-500">*</span>
                </label>
                <input
                  id="name"
                  v-model="profileForm.name"
                  type="text"
                  required
                  :class="getInputClasses(profileErrors.name)"
                  :placeholder="t('profile.name_placeholder')"
                />
                <p v-if="profileErrors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ profileErrors.name }}
                </p>
              </div>

              <!-- Username -->
              <div>
                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ t('profile.username') }} <span class="text-red-500">*</span>
                </label>
                <input
                  id="username"
                  v-model="profileForm.username"
                  type="text"
                  required
                  :class="getInputClasses(profileErrors.username)"
                  :placeholder="t('profile.username_placeholder')"
                />
                <p v-if="profileErrors.username" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ profileErrors.username }}
                </p>
              </div>

              <!-- Email -->
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ t('profile.email') }} <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <input
                    id="email"
                    v-model="profileForm.email"
                    type="email"
                    required
                    :class="getInputClasses(profileErrors.email)"
                    :placeholder="t('profile.email_placeholder')"
                  />
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <CheckCircleIcon 
                      v-if="user?.emailVerified" 
                      class="w-5 h-5 text-green-500" 
                      :title="t('profile.email_verified')"
                    />
                    <ExclamationCircleIcon 
                      v-else 
                      class="w-5 h-5 text-yellow-500" 
                      :title="t('profile.email_unverified')"
                    />
                  </div>
                </div>
                <p v-if="profileErrors.email" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ profileErrors.email }}
                </p>
              </div>

              <!-- Phone -->
              <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ t('profile.phone') }}
                </label>
                <input
                  id="phone"
                  v-model="profileForm.phone"
                  type="tel"
                  :class="getInputClasses(profileErrors.phone)"
                  :placeholder="t('profile.phone_placeholder')"
                />
                <p v-if="profileErrors.phone" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ profileErrors.phone }}
                </p>
              </div>

              <!-- Company -->
              <div>
                <label for="company" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ t('profile.company') }}
                </label>
                <input
                  id="company"
                  v-model="profileForm.company"
                  type="text"
                  :class="getInputClasses(profileErrors.company)"
                  :placeholder="t('profile.company_placeholder')"
                />
                <p v-if="profileErrors.company" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ profileErrors.company }}
                </p>
              </div>

              <!-- Position -->
              <div>
                <label for="position" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ t('profile.position') }}
                </label>
                <input
                  id="position"
                  v-model="profileForm.position"
                  type="text"
                  :class="getInputClasses(profileErrors.position)"
                  :placeholder="t('profile.position_placeholder')"
                />
                <p v-if="profileErrors.position" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ profileErrors.position }}
                </p>
              </div>

              <!-- Location -->
              <div>
                <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ t('profile.location') }}
                </label>
                <input
                  id="location"
                  v-model="profileForm.location"
                  type="text"
                  :class="getInputClasses(profileErrors.location)"
                  :placeholder="t('profile.location_placeholder')"
                />
                <p v-if="profileErrors.location" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ profileErrors.location }}
                </p>
              </div>

              <!-- Website -->
              <div>
                <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ t('profile.website') }}
                </label>
                <input
                  id="website"
                  v-model="profileForm.website"
                  type="url"
                  :class="getInputClasses(profileErrors.website)"
                  :placeholder="t('profile.website_placeholder')"
                />
                <p v-if="profileErrors.website" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ profileErrors.website }}
                </p>
              </div>
            </div>

            <!-- Bio -->
            <div>
              <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ t('profile.bio') }}
              </label>
              <textarea
                id="bio"
                v-model="profileForm.bio"
                rows="4"
                :class="getTextareaClasses(profileErrors.bio)"
                :placeholder="t('profile.bio_placeholder')"
              />
              <p v-if="profileErrors.bio" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ profileErrors.bio }}
              </p>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
              <button
                type="submit"
                :disabled="isUpdatingProfile"
                class="px-6 py-2 bg-primary-600 hover:bg-primary-700 disabled:bg-primary-400 text-white font-medium rounded-lg transition-colors duration-200"
              >
                <span v-if="isUpdatingProfile">{{ t('common.loading') }}</span>
                <span v-else>{{ t('profile.update_profile') }}</span>
              </button>
            </div>
          </form>
        </div>

        <!-- Password Change Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ t('profile.security') }}</h3>
          </div>
          <form @submit.prevent="changePassword" class="p-6 space-y-6">
            <!-- Current Password -->
            <div>
              <label for="current-password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ t('profile.current_password') }} <span class="text-red-500">*</span>
              </label>
              <input
                id="current-password"
                v-model="passwordForm.currentPassword"
                type="password"
                required
                :class="getInputClasses(passwordErrors.currentPassword)"
                :placeholder="t('profile.current_password_placeholder')"
              />
              <p v-if="passwordErrors.currentPassword" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ passwordErrors.currentPassword }}
              </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- New Password -->
              <div>
                <label for="new-password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ t('profile.new_password') }} <span class="text-red-500">*</span>
                </label>
                <input
                  id="new-password"
                  v-model="passwordForm.newPassword"
                  type="password"
                  required
                  :class="getInputClasses(passwordErrors.newPassword)"
                  :placeholder="t('profile.new_password_placeholder')"
                />
                <p v-if="passwordErrors.newPassword" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ passwordErrors.newPassword }}
                </p>
              </div>

              <!-- Confirm New Password -->
              <div>
                <label for="confirm-password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ t('profile.confirm_new_password') }} <span class="text-red-500">*</span>
                </label>
                <input
                  id="confirm-password"
                  v-model="passwordForm.confirmPassword"
                  type="password"
                  required
                  :class="getInputClasses(passwordErrors.confirmPassword)"
                  :placeholder="t('profile.confirm_new_password_placeholder')"
                />
                <p v-if="passwordErrors.confirmPassword" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ passwordErrors.confirmPassword }}
                </p>
              </div>
            </div>

            <!-- Password Requirements -->
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
              <p class="text-sm text-blue-800 dark:text-blue-200">
                <InformationCircleIcon class="w-4 h-4 inline mr-1" />
                {{ t('profile.password_requirements') }}
              </p>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
              <button
                type="submit"
                :disabled="isChangingPassword"
                class="px-6 py-2 bg-red-600 hover:bg-red-700 disabled:bg-red-400 text-white font-medium rounded-lg transition-colors duration-200"
              >
                <span v-if="isChangingPassword">{{ t('common.loading') }}</span>
                <span v-else>{{ t('profile.change_password') }}</span>
              </button>
            </div>
          </form>
        </div>

        <!-- Danger Zone -->
        <div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm border-2 border-red-200 dark:border-red-900">
          <div class="px-6 py-4 border-b border-red-200 dark:border-red-900">
            <h3 class="text-lg font-medium text-red-900 dark:text-red-200">{{ t('profile.danger_zone') }}</h3>
          </div>
          <div class="p-6">
            <div class="flex items-start space-x-4">
              <div class="flex-shrink-0">
                <ExclamationTriangleIcon class="w-8 h-8 text-red-500" />
              </div>
              <div class="flex-1">
                <h4 class="text-base font-medium text-gray-900 dark:text-white mb-2">{{ t('profile.delete_account') }}</h4>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                  {{ t('profile.delete_account_warning') }}
                </p>
                <button
                  @click="showDeleteConfirm = true"
                  class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200"
                >
                  {{ t('profile.delete_account') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteConfirm"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click="showDeleteConfirm = false"
    >
      <div
        class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6"
        @click.stop
      >
        <div class="flex items-center mb-4">
          <ExclamationTriangleIcon class="w-6 h-6 text-red-500 mr-3" />
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ t('profile.confirm_delete') }}</h3>
        </div>
        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
          {{ t('profile.delete_confirmation') }}
        </p>
        <input
          v-model="deleteConfirmPassword"
          type="password"
          :placeholder="t('profile.current_password_placeholder')"
          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg mb-4 dark:bg-gray-700 dark:text-white"
        />
        <div class="flex justify-end space-x-3">
          <button
            @click="showDeleteConfirm = false"
            class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600"
          >
            {{ t('common.cancel') }}
          </button>
          <button
            @click="deleteAccount"
            :disabled="!deleteConfirmPassword || isDeletingAccount"
            class="px-4 py-2 bg-red-600 hover:bg-red-700 disabled:bg-red-400 text-white rounded-lg"
          >
            {{ isDeletingAccount ? t('common.loading') : t('common.delete') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Success/Error Messages -->
    <div
      v-if="message"
      class="fixed bottom-4 right-4 z-50 p-4 rounded-lg shadow-lg"
      :class="message.type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'"
    >
      {{ message.text }}
    </div>
  </div>
</template>

<script setup>
// Page metadata
definePageMeta({
  middleware: 'auth'
})
import {
  CameraIcon,
  TrashIcon,
  CheckCircleIcon,
  ExclamationCircleIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon
} from '@heroicons/vue/24/outline'

// Composables
const { t } = useI18n()
const { updateProfile: updateProfileAPI, changePassword: changePasswordAPI, getProfile } = useAuth()
const { del } = useApi()
const authStore = useAuthStore()
const { user } = storeToRefs(authStore)

// Form data
const profileForm = reactive({
  name: '',
  username: '',
  email: '',
  phone: '',
  bio: '',
  website: '',
  location: '',
  company: '',
  position: '',
  avatar: ''
})

const passwordForm = reactive({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
})

// Error handling
const profileErrors = ref({})
const passwordErrors = ref({})

// Loading states
const isUpdatingProfile = ref(false)
const isChangingPassword = ref(false)
const isDeletingAccount = ref(false)

// UI states
const showDeleteConfirm = ref(false)
const deleteConfirmPassword = ref('')
const message = ref(null)

// Initialize profile data
const initializeProfile = () => {
  if (user.value) {
    Object.assign(profileForm, {
      name: user.value.name || '',
      username: user.value.username || '',
      email: user.value.email || '',
      phone: user.value.phone || '',
      bio: user.value.bio || '',
      website: user.value.website || '',
      location: user.value.location || '',
      company: user.value.company || '',
      position: user.value.position || '',
      avatar: user.value.avatar || ''
    })
  }
}

// Validation
const validateProfile = () => {
  const errors = {}
  
  if (!profileForm.name?.trim()) {
    errors.name = t('auth.full_name_placeholder')
  }
  
  if (!profileForm.username?.trim()) {
    errors.username = t('profile.username_placeholder')
  }
  
  if (!profileForm.email?.trim()) {
    errors.email = t('profile.email_placeholder')
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(profileForm.email)) {
    errors.email = 'Please enter a valid email address'
  }
  
  if (profileForm.website && !/^https?:\/\/.+/.test(profileForm.website)) {
    errors.website = 'Please enter a valid URL (starting with http:// or https://)'
  }
  
  profileErrors.value = errors
  return Object.keys(errors).length === 0
}

const validatePassword = () => {
  const errors = {}
  
  if (!passwordForm.currentPassword?.trim()) {
    errors.currentPassword = t('profile.current_password_placeholder')
  }
  
  if (!passwordForm.newPassword?.trim()) {
    errors.newPassword = t('profile.new_password_placeholder')
  } else if (passwordForm.newPassword.length < 8) {
    errors.newPassword = t('profile.password_requirements')
  } else if (!/(?=.*[a-zA-Z])(?=.*\d)/.test(passwordForm.newPassword)) {
    errors.newPassword = t('profile.password_requirements')
  }
  
  if (passwordForm.newPassword !== passwordForm.confirmPassword) {
    errors.confirmPassword = t('profile.passwords_not_match')
  }
  
  passwordErrors.value = errors
  return Object.keys(errors).length === 0
}

// Form submission handlers
const updateProfile = async () => {
  if (!validateProfile()) return
  
  isUpdatingProfile.value = true
  
  try {
    // Use the proper API method
    const response = await updateProfileAPI({
      name: profileForm.name,
      username: profileForm.username,
      email: profileForm.email,
      phone: profileForm.phone,
      bio: profileForm.bio,
      website: profileForm.website,
      location: profileForm.location,
      company: profileForm.company,
      position: profileForm.position
    })
    
    if (response.success) {
      showMessage('個人資料更新成功', 'success')
    } else {
      throw new Error(response.message || '個人資料更新失敗')
    }
  } catch (error) {
    console.error('Profile update failed:', error)
    showMessage(error.message || '個人資料更新失敗', 'error')
  } finally {
    isUpdatingProfile.value = false
  }
}

const changePassword = async () => {
  if (!validatePassword()) return
  
  isChangingPassword.value = true
  
  try {
    // Use the proper API method
    const response = await changePasswordAPI({
      current_password: passwordForm.currentPassword,
      password: passwordForm.newPassword,
      password_confirmation: passwordForm.confirmPassword
    })
    
    if (response.success) {
      // Clear form
      Object.assign(passwordForm, {
        currentPassword: '',
        newPassword: '',
        confirmPassword: ''
      })
      
      showMessage('密碼修改成功', 'success')
    } else {
      throw new Error(response.message || '密碼修改失敗')
    }
  } catch (error) {
    console.error('Password change failed:', error)
    
    // Handle specific validation errors
    if (error.message.includes('Current password is incorrect')) {
      passwordErrors.value.currentPassword = '目前密碼不正確'
    } else {
      showMessage(error.message || '密碼修改失敗', 'error')
    }
  } finally {
    isChangingPassword.value = false
  }
}

// Avatar handling
const triggerAvatarUpload = () => {
  document.getElementById('avatar-upload')?.click()
}

const handleAvatarUpload = (event) => {
  const file = event.target.files?.[0]
  if (!file) return
  
  // Validate file
  if (!file.type.startsWith('image/')) {
    showMessage('Please select a valid image file', 'error')
    return
  }
  
  if (file.size > 5 * 1024 * 1024) { // 5MB limit
    showMessage('File size must be less than 5MB', 'error')
    return
  }
  
  // Create object URL for preview
  const imageUrl = URL.createObjectURL(file)
  profileForm.avatar = imageUrl
  
  showMessage(t('profile.avatar_updated'), 'success')
}

const removeAvatar = () => {
  profileForm.avatar = ''
  showMessage(t('profile.avatar_removed'), 'success')
}

const isDefaultAvatar = (avatarUrl) => {
  return !avatarUrl || avatarUrl.includes('ui-avatars.com')
}

// Account deletion
const deleteAccount = async () => {
  if (!deleteConfirmPassword.value) return
  
  isDeletingAccount.value = true
  
  try {
    // Use the profile API to delete account
    const response = await del('/profile', {
      password: deleteConfirmPassword.value
    })
    
    if (response.success) {
      showMessage('帳號刪除成功', 'success')
      // Logout after successful deletion
      await authStore.logout()
    } else {
      throw new Error(response.message || '帳號刪除失敗')
    }
  } catch (error) {
    console.error('Account deletion failed:', error)
    
    if (error.message.includes('password') || error.message.includes('incorrect')) {
      showMessage('密碼不正確', 'error')
    } else {
      showMessage(error.message || '帳號刪除失敗', 'error')
    }
  } finally {
    isDeletingAccount.value = false
    showDeleteConfirm.value = false
  }
}

// Utility functions
const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString()
}

const showMessage = (text, type = 'success') => {
  message.value = { text, type }
  setTimeout(() => {
    message.value = null
  }, 5000)
}

const getInputClasses = (hasError) => {
  const baseClasses = 'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-0 dark:bg-gray-700 dark:text-white transition-colors duration-200'
  
  if (hasError) {
    return `${baseClasses} border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500`
  }
  
  return `${baseClasses} border-gray-300 dark:border-gray-600 focus:border-primary-500 focus:ring-primary-500`
}

const getTextareaClasses = (hasError) => {
  const baseClasses = 'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-0 dark:bg-gray-700 dark:text-white transition-colors duration-200 resize-vertical'
  
  if (hasError) {
    return `${baseClasses} border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500`
  }
  
  return `${baseClasses} border-gray-300 dark:border-gray-600 focus:border-primary-500 focus:ring-primary-500`
}

// Lifecycle
onMounted(() => {
  initializeProfile()
})

// Watch for user changes
watch(user, () => {
  initializeProfile()
}, { deep: true })

// Head
useHead({
  title: computed(() => t('profile.title'))
})
</script>