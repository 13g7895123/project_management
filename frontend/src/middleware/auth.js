export default defineNuxtRouteMiddleware(async (to) => {
  const authStore = useAuthStore()
  
  // 初始化認證狀態
  await authStore.initializeAuth()
  
  // 檢查是否已登入且有有效token
  if (!authStore.isLoggedIn) {
    console.log('[Auth Middleware] User not authenticated, redirecting to login')
    return navigateTo('/auth/login')
  }
  
  // 嘗試驗證token是否仍然有效
  try {
    await authStore.fetchUser()
  } catch (error) {
    console.warn('[Auth Middleware] Token validation failed:', error.message)
    return navigateTo('/auth/login')
  }
})