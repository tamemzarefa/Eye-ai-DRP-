import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { setToken } from '../lib/auth'

export default function Login() {
  const navigate = useNavigate()
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState('')

  const handleSubmit = async (e) => {
    e.preventDefault()
    if (!email || !password) return
    setLoading(true)
    setError('')

    try {
      const res = await fetch('/api/v1/auth/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password }),
      })

      if (!res.ok) throw new Error('Login failed')
      const data = await res.json()
      // store token using helper
      if (data.token) setToken(data.token)
      navigate('/')
    } catch (err) {
      console.error(err)
      setError(err.message || 'فشل تسجيل الدخول')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="min-h-screen flex items-center justify-center">
      <div className="w-full max-w-md p-6 bg-white rounded shadow">
        <h2 className="text-xl font-semibold mb-4">تسجيل الدخول</h2>
        {error && <div className="mb-3 text-red-600">{error}</div>}
        <form onSubmit={handleSubmit} className="flex flex-col gap-3">
          <input value={email} onChange={e => setEmail(e.target.value)} placeholder="البريد الإلكتروني" className="px-3 py-2 border rounded" />
          <input value={password} onChange={e => setPassword(e.target.value)} type="password" placeholder="كلمة المرور" className="px-3 py-2 border rounded" />
          <button type="submit" disabled={loading} className="mt-2 bg-amber-500 text-white px-4 py-2 rounded">{loading ? 'جاري...' : 'دخول'}</button>
        </form>
      </div>
    </div>
  )
}
