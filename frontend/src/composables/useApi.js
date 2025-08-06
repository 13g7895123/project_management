/**
 * Base API composable for handling HTTP requests
 */
export const useApi = () => {
  const config = useRuntimeConfig()
  
  // Get base URL from environment - using /api as specified in requirements
  const baseURL = process.client 
    ? '/api'
    : '/api'

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
      const response = await $fetch(endpoint, defaultOptions)
      return {
        success: true,
        data: response,
        error: null
      }
    } catch (error) {
      console.error('API Request Error:', error)
      return {
        success: false,
        data: null,
        error: {
          message: error.message || '請求失敗',
          status: error.status || 500,
          statusText: error.statusText || 'Internal Server Error'
        }
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