export default defineNuxtRouteMiddleware(async (to) => {
  const authStore = useAuthStore()
  
  // 初始化認證狀態
  await authStore.initializeAuth()
  
  // 檢查是否已登入
  if (!authStore.isLoggedIn) {
    console.log('[Admin Middleware] User not authenticated, redirecting to login')
    return navigateTo('/auth/login')
  }
  
  // 嘗試驗證token是否仍然有效
  try {
    await authStore.fetchUser()
  } catch (error) {
    console.warn('[Admin Middleware] Token validation failed:', error.message)
    return navigateTo('/auth/login')
  }
  
  // 檢查是否有管理員權限
  if (!authStore.isAdmin) {
    console.log('[Admin Middleware] User is not admin, access denied')
    throw createError({
      statusCode: 403,
      statusMessage: 'Access denied. Administrator privileges required.'
    })
  }
})