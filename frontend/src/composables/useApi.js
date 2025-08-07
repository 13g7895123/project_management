/**
 * Base API composable for handling HTTP requests
 */
export const useApi = () => {
  const config = useRuntimeConfig()
  
  // Get base URL from environment - handle development vs production
  const getBaseURL = () => {
    // In development, use proxy
    if (process.dev) {
      return '/api'
    }
    
    // In production, use the configured API URL
    const apiBaseUrl = config.public.apiBaseUrl || config.public.backendUrl
    if (apiBaseUrl) {
      return apiBaseUrl
    }
    
    // Default fallback
    return 'https://project.mercylife.cc/api'
  }
  
  const baseURL = getBaseURL()
  
  // Log the resolved base URL for debugging
  console.log('[API] Base URL resolved to:', baseURL)

  /**
   * Generic API request handler
   */
  const apiRequest = async (endpoint, options = {}) => {
    const defaultOptions = {
      baseURL,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        ...options.headers
      },
      ...options
    }

    try {
      console.log(`[API] ${defaultOptions.method || 'GET'} ${baseURL}${endpoint}`)
      const response = await $fetch(endpoint, defaultOptions)
      console.log(`[API] Success: ${baseURL}${endpoint}`)
      return {
        success: true,
        data: response,
        error: null
      }
    } catch (error) {
      console.error(`[API] Error ${defaultOptions.method || 'GET'} ${baseURL}${endpoint}:`, error)
      console.error('[API] Full URL:', `${baseURL}${endpoint}`)
      console.error('[API] Options:', defaultOptions)
      
      // Provide more detailed error information
      const errorDetails = {
        message: error.message || '請求失敗',
        status: error.status || error.statusCode || 500,
        statusText: error.statusText || error.statusMessage || 'Internal Server Error',
        url: `${baseURL}${endpoint}`,
        method: defaultOptions.method || 'GET'
      }
      
      // Special handling for 404 errors
      if (errorDetails.status === 404) {
        errorDetails.message = `API endpoint not found: ${errorDetails.url}`
      }
      
      return {
        success: false,
        data: null,
        error: errorDetails
      }
    }
  }

  /**
   * GET request
   */
  const get = async (endpoint, params = {}) => {
    return await apiRequest(endpoint, {
      method: 'GET',
      params
    })
  }

  /**
   * POST request
   */
  const post = async (endpoint, body = {}) => {
    return await apiRequest(endpoint, {
      method: 'POST',
      body
    })
  }

  /**
   * PUT request
   */
  const put = async (endpoint, body = {}) => {
    return await apiRequest(endpoint, {
      method: 'PUT',
      body
    })
  }

  /**
   * DELETE request
   */
  const del = async (endpoint) => {
    return await apiRequest(endpoint, {
      method: 'DELETE'
    })
  }

  return {
    get,
    post,
    put,
    delete: del,
    apiRequest
  }
}