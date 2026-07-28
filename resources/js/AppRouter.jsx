import { BrowserRouter, Routes, Route } from 'react-router-dom'
import Dashboard from './pages/Dashboard'
import NewAnalysis from './pages/NewAnalysis'
import Reports from './pages/Reports'
import Patients from './pages/Patients'
import Report from './pages/Report'
import Login from './pages/Login'
import { useEffect, useState } from 'react'
import { getToken, authFetch } from './lib/auth'

export default function AppRouter() {
  const [checking, setChecking] = useState(true)

  useEffect(() => {
    const token = getToken()
    if (!token) {
      setChecking(false)
      return
    }

    // validate token by calling /api/v1/auth/me
    authFetch('/api/v1/auth/me').then(async (res) => {
      if (res.ok) {
        // token valid; do nothing
      } else {
        // invalid token -> clear and go to login
        localStorage.removeItem('token')
        window.location.replace('/login')
      }
      setChecking(false)
    }).catch(() => {
      localStorage.removeItem('token')
      setChecking(false)
    })
  }, [])

  if (checking) return null
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Dashboard />} />
        <Route path="/new-analysis" element={<NewAnalysis />} />
          <Route path="/reports" element={<Reports />} />
          <Route path="/patients" element={<Patients />} />
          <Route path="/report/:id" element={<Report />} />
        <Route path="/login" element={<Login />} />
      </Routes>
    </BrowserRouter>
  )
}
