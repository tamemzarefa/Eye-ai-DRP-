import { BrowserRouter, Routes, Route } from 'react-router-dom'
import Dashboard from './pages/Dashboard'
import NewAnalysis from './pages/NewAnalysis'
import Reports from './pages/Reports'
import Patients from './pages/Patients'
import Report from './pages/Report'
import Login from './pages/Login'
import Register from './pages/Register'


function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Dashboard />} />
        <Route path="/new-analysis" element={<NewAnalysis />} />
        <Route path="/reports" element={<Reports />} />
        <Route path="/patients" element={<Patients />} />
        <Route path="/report" element={<Report />} />
        <Route path="/login" element={<Login />} />
        <Route path="/register" element={<Register />} />
      </Routes>
    </BrowserRouter>
  )
}

export default App