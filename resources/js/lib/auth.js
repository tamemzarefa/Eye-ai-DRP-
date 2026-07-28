export function getToken() {
  return localStorage.getItem('token') || null
}

export function setToken(token) {
  if (token) localStorage.setItem('token', token)
  else localStorage.removeItem('token')
}

export async function authFetch(input, init = {}) {
  const token = getToken()
  const headers = new Headers(init.headers || {})
  if (token) headers.set('Authorization', `Bearer ${token}`)
  headers.set('Accept', 'application/json')

  const res = await fetch(input, { ...init, headers })
  return res
}
