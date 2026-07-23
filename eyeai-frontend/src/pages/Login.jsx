import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import logo from '../assets/logo.png'

export default function Login() {
  const navigate = useNavigate()
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [loading, setLoading] = useState(false)

  const handleLogin = async (e) => {
    e.preventDefault()
    if (!email || !password) return
    setLoading(true)

    try {
      // const response = await fetch('http://localhost:8000/api/login', {
      //   method: 'POST',
      //   headers: { 'Content-Type': 'application/json' },
      //   body: JSON.stringify({ email, password }),
      // })
      // const data = await response.json()
      // localStorage.setItem('token', data.token)

      await new Promise(r => setTimeout(r, 1000))
      navigate('/')
    } catch (err) {
      console.error('خطأ بتسجيل الدخول:', err)
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="min-h-screen flex font-sans">
      {/* Left */}
      <div className="hidden md:flex w-5/12 flex-col justify-between p-10 relative overflow-hidden"
        style={{ background: 'linear-gradient(135deg, #f97316 0%, #fb923c 40%, #fde68a 100%)' }}>
        <div className="flex items-center gap-2">
          <img 
  src={logo} 
  className="h-14 w-auto" 
  alt="eye Ai"
  style={{ filter: 'brightness(0) invert(1)' }} 
/>
        </div>
        <div>
          <p className="text-gray-900 text-xl font-bold leading-relaxed">
            <span className="font-black">Empower your clinical diagnosis</span>
            {' '}with precision AI and make sight possible for all
          </p>
        </div>
      </div>

      {/* Right */}
      <div className="flex-1 flex items-center justify-center px-8 py-12 bg-white">
        <div className="w-full max-w-sm">
          <div className="text-amber-500 mb-4">✦</div>
          <h1 className="text-2xl font-bold text-gray-900 mb-6">Sign In</h1>

          <form onSubmit={handleLogin} className="flex flex-col gap-4">
            <div>
              <label className="block text-sm text-gray-600 mb-1">your email</label>
              <input
                type="email"
                value={email}
                onChange={e => setEmail(e.target.value)}
                className="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-amber-400 transition-colors"
              />
            </div>
            <div>
              <label className="block text-sm text-gray-600 mb-1">your password</label>
              <input
                type="password"
                value={password}
                onChange={e => setPassword(e.target.value)}
                className="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-amber-400 transition-colors"
              />
            </div>

            <button
              type="submit"
              disabled={loading}
              className="w-full bg-gray-900 hover:bg-gray-700 disabled:opacity-60 text-white font-semibold py-2.5 rounded-lg border-0 cursor-pointer transition-colors font-sans mt-2"
            >
              {loading ? 'جاري الدخول...' : 'Sign in'}
            </button>
          </form>

          <div className="flex items-center gap-3 my-4">
            <div className="flex-1 h-px bg-gray-200" />
            <span className="text-sm text-gray-400">or</span>
            <div className="flex-1 h-px bg-gray-200" />
          </div>

          <button className="w-full flex items-center justify-center gap-2 border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium py-2.5 rounded-lg cursor-pointer transition-colors bg-white font-sans">
            <svg width="18" height="18" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
              <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
              <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
              <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Continue with google
          </button>

          <p className="text-center text-xs text-gray-400 mt-6">
            You don't have an account?{' '}
            <button onClick={() => navigate('/register')} className="text-gray-900 font-semibold underline bg-transparent border-0 cursor-pointer font-sans">
              Create new one
            </button>
          </p>
        </div>
      </div>
    </div>
  )
}