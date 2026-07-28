import { BrowserRouter, Routes, Route } from 'react-router-dom'
import Dashboard from './pages/Dashboard'
import NewAnalysis from './pages/NewAnalysis'
import Reports from './pages/Reports'
import Patients from './pages/Patients'
import Report from './pages/Report'

export default function AppRouter() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Dashboard />} />
        <Route path="/new-analysis" element={<NewAnalysis />} />
        <Route path="/reports" element={<Reports />} />
        <Route path="/patients" element={<Patients />} />
        <Route path="/report/:id" element={<Report />} />
      </Routes>
    </BrowserRouter>
  )
}
