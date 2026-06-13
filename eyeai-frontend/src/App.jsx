import { BrowserRouter, Routes, Route } from 'react-router-dom'
import Dashboard from './pages/Dashboard'
import NewAnalysis from './pages/NewAnalysis'
import Reports from './pages/Reports'
import Patients from './pages/Patients'

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Dashboard />} />
        <Route path="/new-analysis" element={<NewAnalysis />} />
        <Route path="/reports" element={<Reports />} />
        <Route path="/patients" element={<Patients />} />
      </Routes>
    </BrowserRouter>
  )
}

export default App