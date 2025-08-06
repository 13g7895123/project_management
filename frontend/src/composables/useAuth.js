/**
 * Authentication API composable
 */
export const useAuth = () => {
  const { post, get } = useApi()

  /**
   * Login user
   */
  const login = async (credentials) => {
    return await post('/auth/login', credentials)
  }

  /**
   * Register user
   */
  const register = async (userData) => {
    return await post('/auth/register', userData)
  }

  /**
   * Logout user
   */
  const logout = async () => {
    return await post('/auth/logout')
  }

  /**
   * Get current user profile
   */
  const getProfile = async () => {
    return await get('/auth/profile')
  }

  /**
   * Update user profile
   */
  const updateProfile = async (profileData) => {
    return await post('/auth/profile', profileData)
  }

  /**
   * Change password
   */
  const changePassword = async (passwordData) => {
    return await post('/auth/change-password', passwordData)
  }

  /**
   * Request password reset
   */
  const requestPasswordReset = async (email) => {
    return await post('/auth/forgot-password', { email })
  }

  /**
   * Reset password
   */
  const resetPassword = async (resetData) => {
    return await post('/auth/reset-password', resetData)
  }

  /**
   * Refresh token
   */
  const refreshToken = async () => {
    return await post('/auth/refresh')
  }

  /**
   * Verify email
   */
  const verifyEmail = async (token) => {
    return await post('/auth/verify-email', { token })
  }

  return {
    login,
    register,
    logout,
    getProfile,
    updateProfile,
    changePassword,
    requestPasswordReset,
    resetPassword,
    refreshToken,
    verifyEmail
  }
}