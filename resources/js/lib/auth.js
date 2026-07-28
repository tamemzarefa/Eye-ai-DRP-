const TOKEN_KEY = 'eyeai_token'

export function getToken() {
  return localStorage.getItem(TOKEN_KEY) || null
}

export function setToken(token) {
  if (token) {
    localStorage.setItem(TOKEN_KEY, token)
  }
}

export function clearToken() {
  localStorage.removeItem(TOKEN_KEY)
}

export async function authFetch(input, init = {}) {
  const token = getToken()
  const headers = new Headers(init.headers || {})
  if (token) {
    headers.set('Authorization', `Bearer ${token}`)
  }
  headers.set('Accept', 'application/json')

  const res = await fetch(input, { ...init, headers })
  return res
}
