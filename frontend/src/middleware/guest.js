export default defineNuxtRouteMiddleware(async (to) => {
  const authStore = useAuthStore()
  
  // 初始化認證狀態
  await authStore.initializeAuth()
  
  // 如果已經登入且token有效，重定向到仟表板
  if (authStore.isLoggedIn) {
    console.log('[Guest Middleware] User is authenticated, redirecting to dashboard')
    return navigateTo('/dashboard/analytics')
  }
})