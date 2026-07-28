import { useEffect, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { get } from '../lib/api'
import Navbar from '../components/Navbar'
import Sidebar from '../components/Sidebar'

const EyeIcon = ({ size = 20, color = 'currentColor' }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
    <circle cx="12" cy="12" r="3" />
  </svg>
)

const UsersIcon = ({ size = 22, color = 'currentColor' }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
    <circle cx="9" cy="7" r="4" />
    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
  </svg>
)

const UploadIcon = ({ size = 16 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
    <polyline points="17 8 12 3 7 8" />
    <line x1="12" y1="3" x2="12" y2="15" />
  </svg>
)

const statusClasses = {
  active: 'bg-green-100 text-green-700',
  inactive: 'bg-amber-100 text-amber-700',
  archived: 'bg-gray-100 text-gray-600',
}

const statusLabels = {
  active: 'نشط',
  inactive: 'غير نشط',
  archived: 'مؤرشف',
}

const statusChartData = [
  { label: 'نشط', key: 'active' },
  { label: 'غير نشط', key: 'inactive' },
  { label: 'مؤرشف', key: 'archived' },
]

function getInitials(name) {
  return name
    .split(' ')
    .slice(0, 2)
    .map((part) => part.charAt(0).toUpperCase())
    .join('')
}

function formatDate(value) {
  if (!value) return '-'
  return new Date(value).toLocaleDateString('ar-EG', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

export default function Dashboard() {
  const [mobileOpen, setMobileOpen] = useState(false)
  const [patients, setPatients] = useState([])
  const [user, setUser] = useState(null)
  const [statusCounts, setStatusCounts] = useState({ active: 0, inactive: 0, archived: 0 })
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')
  const navigate = useNavigate()

  useEffect(() => {
    const fetchData = async () => {
      setLoading(true)
      setError('')

      try {
        const [patientsResponse, userResponse] = await Promise.all([
          get('/patients'),
          get('/auth/me'),
        ])

        const patientList = patientsResponse.data ?? patientsResponse
        setPatients(patientList)
        setUser(userResponse)

        const counts = { active: 0, inactive: 0, archived: 0 }
        patientList.forEach((patient) => {
          counts[patient.status] = (counts[patient.status] || 0) + 1
        })
        setStatusCounts(counts)
      } catch (err) {
        console.error(err)
        setError(err.message || 'فشل تحميل بيانات المرضى.')
        if (err.response?.status === 401) {
          navigate('/login')
        }
      } finally {
        setLoading(false)
      }
    }

    fetchData()
  }, [navigate])

  return (
    <div className="min-h-screen bg-white flex flex-col font-sans" dir="rtl">
      <Navbar onMenuClick={() => setMobileOpen(true)} />

      <div className="flex flex-1">
        <Sidebar mobileOpen={mobileOpen} onClose={() => setMobileOpen(false)} />

        <div className="flex-1 p-5 md:p-6 min-w-0">
          <div className="flex flex-col-reverse sm:flex-row justify-between items-start gap-3 mb-5">
            <button onClick={() => navigate('/new-analysis')} className="flex items-center gap-2 bg-amber-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl cursor-pointer border-0 font-sans whitespace-nowrap">
              <UploadIcon />
              تحليل صورة جديدة
            </button>
            <div className="text-right">
              <h1 className="text-xl md:text-2xl font-bold text-gray-900">مرحباً {user?.name ?? 'دكتور'}</h1>
              <p className="text-sm text-gray-400 mt-1">إليك نظرة عامة على سجلات المرضى الحالية والحالة العامة.</p>
            </div>
          </div>

          <div className="flex flex-col sm:flex-row gap-4 mb-5">
            <div className="flex-1 border border-gray-100 rounded-2xl p-4 min-w-0">
              <div className="flex justify-between items-center mb-4 flex-wrap gap-2">
                <span className="text-xs text-gray-400 flex items-center gap-1.5">
                  <span className="w-2 h-2 bg-amber-500 rounded-full inline-block" />
                  حالة المرضى حسب الحالة
                </span>
                <span className="text-sm font-semibold text-gray-900">توزيع حالات المرضى</span>
              </div>
              <div className="flex items-end gap-2 sm:gap-3 h-28 justify-center">
                {statusChartData.map((bar) => (
                  <div key={bar.key} className="flex flex-col items-center gap-1.5 flex-1 max-w-12">
                    <div
                      className="w-full rounded-t-md min-w-4"
                      style={{ height: `${Math.max((statusCounts[bar.key] || 0) * 12, 40)}px`, backgroundColor: '#f59e0b' }}
                    />
                    <span className="text-[10px] text-gray-400 text-center">{bar.label}</span>
                  </div>
                ))}
              </div>
            </div>

            <div className="sm:w-44 border border-gray-100 rounded-2xl p-4 flex flex-row sm:flex-col justify-between gap-4">
              <div className="flex justify-between items-start">
                <span className="bg-amber-50 text-amber-500 text-[11px] font-semibold rounded-full px-2 py-0.5">
                  إجمالي المرضى
                </span>
                <UsersIcon size={22} color="#f59e0b" />
              </div>
              <div className="text-right">
                <div className="text-xs text-gray-400 mb-1">السجل الحالي</div>
                <div className="text-3xl font-bold text-gray-900">{patients.length}</div>
              </div>
            </div>
          </div>

          {error ? (
            <div className="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700 mb-5">
              {error}
            </div>
          ) : null}

          <div className="border border-gray-100 rounded-2xl overflow-hidden">
            <div className="flex justify-between items-center px-5 py-3.5 border-b border-gray-100">
              <span className="text-amber-500 text-sm cursor-pointer">عرض الكل</span>
              <span className="text-sm font-semibold text-gray-900">آخر سجلات المرضى</span>
            </div>

            <div className="grid grid-cols-3 md:grid-cols-5 px-5 py-2.5 bg-gray-50 border-b border-gray-100">
              <div className="hidden md:block text-xs text-gray-400 text-center">الإجراء</div>
              <div className="hidden md:block text-xs text-gray-400 text-center">الحالة</div>
              <div className="text-xs text-gray-400 text-center">الحالة العامة</div>
              <div className="text-xs text-gray-400 text-center">تاريخ التسجيل</div>
              <div className="text-xs text-gray-400 text-center">المريض</div>
            </div>

            {loading ? (
              <div className="px-5 py-8 text-center text-gray-500">جارٍ تحميل البيانات...</div>
            ) : patients.length === 0 ? (
              <div className="px-5 py-8 text-center text-gray-500">لا توجد سجلات مرضى حتى الآن.</div>
            ) : (
              patients.map((p, idx) => (
                <div
                  key={p.id}
                  className={`grid grid-cols-3 md:grid-cols-5 px-5 py-3.5 items-center ${idx < patients.length - 1 ? 'border-b border-gray-50' : ''}`}
                >
                  <div className="hidden md:flex justify-center">
                    <EyeIcon size={18} color="#ccc" />
                  </div>
                  <div className="hidden md:block text-center text-sm font-semibold">
                    {statusLabels[p.status] ?? p.status}
                  </div>
                  <div className="text-center">
                    <span className={`text-xs font-medium px-3 py-1 rounded-full ${statusClasses[p.status] ?? 'bg-gray-100 text-gray-600'}`}>
                      {statusLabels[p.status] ?? p.status}
                    </span>
                  </div>
                  <div className="text-center text-sm text-gray-500">{formatDate(p.created_at)}</div>
                  <div className="flex items-center gap-2 justify-end">
                    <div className="text-right">
                      <div className="text-sm font-semibold text-gray-900">{p.name}</div>
                      <div className="text-xs text-gray-400">ID: #{p.id}</div>
                    </div>
                    <div className="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center text-white text-xs font-semibold shrink-0">
                      {getInitials(p.name)}
                    </div>
                  </div>
                </div>
              ))
            )}
          </div>
        </div>
      </div>
    </div>
  )
}
