const API_BASE_URL = import.meta.env.VITE_API_BASE_URL?.replace(/\/+$/, '') ?? ''
const API_PREFIX = `${API_BASE_URL}/api/v1`
const TOKEN_KEY = 'eyeai_token'

export function getToken() {
  return localStorage.getItem(TOKEN_KEY)
}

export function setToken(token) {
  localStorage.setItem(TOKEN_KEY, token)
}

export function clearToken() {
  localStorage.removeItem(TOKEN_KEY)
}

function authHeaders() {
  const token = getToken()
  return token ? { Authorization: `Bearer ${token}` } : {}
}

async function request(path, options = {}) {
  const url = `${API_PREFIX}${path}`
  const headers = {
    ...authHeaders(),
    ...options.headers,
  }

  const init = {
    method: options.method || 'GET',
    headers,
    ...options,
  }

  if (options.body && !(options.body instanceof FormData)) {
    init.body = JSON.stringify(options.body)
    init.headers = {
      'Content-Type': 'application/json',
      ...headers,
    }
  }

  const response = await fetch(url, init)

  if (!response.ok) {
    if (response.status === 401) {
      clearToken()
    }

    let errorData = null
    try {
      errorData = await response.json()
    } catch (e) {
      // ignore parse failure
    }

    const message = errorData?.message || response.statusText || 'Request failed'
    const error = new Error(message)
    error.response = response
    error.data = errorData
    throw error
  }

  if (response.status === 204) {
    return null
  }

  return response.json()
}

export function get(path, options = {}) {
  return request(path, { method: 'GET', ...options })
}

export function post(path, body, options = {}) {
  return request(path, { method: 'POST', body, ...options })
}

export function put(path, body, options = {}) {
  return request(path, { method: 'PUT', body, ...options })
}

export function del(path, options = {}) {
  return request(path, { method: 'DELETE', ...options })
}
