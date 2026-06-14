import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import Navbar from '../components/Navbar'
import Sidebar from '../components/Sidebar'

const EyeIcon = ({ size = 20, color = "currentColor" }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
    <circle cx="12" cy="12" r="3"/>
  </svg>
)

const UsersIcon = ({ size = 22, color = "currentColor" }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
    <circle cx="9" cy="7" r="4"/>
    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
  </svg>
)

const UploadIcon = ({ size = 16 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
    <polyline points="17 8 12 3 7 8"/>
    <line x1="12" y1="3" x2="12" y2="15"/>
  </svg>
)

const barData = [
  { label: "طبيعي", height: 55 },
  { label: "خفيف", height: 80 },
  { label: "متوسط", height: 65 },
  { label: "شديد", height: 75 },
  { label: "تكاثري", height: 50 },
]

const patients = [
  { initials: "سا", name: "سارة أحمد", id: "#45920", date: "اليوم، 10:45 ص", classification: "طبيعي (Negative)", classType: "negative", confidence: "99.2%", confidenceColor: "text-green-500" },
  { initials: "م.ح", name: "محمد حسن", id: "#45921", date: "اليوم، 09:12 ص", classification: "متوسط (Moderate)", classType: "moderate", confidence: "87.5%", confidenceColor: "text-amber-500" },
  { initials: "ل.ع", name: "ليلى عمر", id: "#45918", date: "أمس، 04:30 م", classification: "تكاثري (Proliferative)", classType: "proliferative", confidence: "94.8%", confidenceColor: "text-amber-500" },
]

const classificationClasses = {
  negative: "bg-green-100 text-green-700",
  moderate: "bg-amber-100 text-amber-700",
  proliferative: "bg-red-100 text-red-700",
}

export default function Dashboard() {
  const [mobileOpen, setMobileOpen] = useState(false)
  const navigate = useNavigate()

  return (
    <div className="min-h-screen bg-white flex flex-col font-sans" dir="rtl">

      <Navbar onMenuClick={() => setMobileOpen(true)} />

      <div className="flex flex-1">
        <Sidebar mobileOpen={mobileOpen} onClose={() => setMobileOpen(false)} />

        {/* MAIN */}
        <div className="flex-1 p-5 md:p-6 min-w-0">

          {/* Header */}
          <div className="flex flex-col-reverse sm:flex-row justify-between items-start gap-3 mb-5">
            <button onClick={() => navigate('/new-analysis')} className="flex items-center gap-2 bg-amber-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl cursor-pointer border-0 font-sans whitespace-nowrap">
              <UploadIcon />
              تحليل صورة جديدة
            </button>
            <div className="text-right">
              <h1 className="text-xl md:text-2xl font-bold text-gray-900">مرحباً د. أحمد</h1>
              <p className="text-sm text-gray-400 mt-1">إليك نظرة عامة على نشاط العيادة اليوم والنتائج التحليلية.</p>
            </div>
          </div>

          {/* Cards Row */}
          <div className="flex flex-col sm:flex-row gap-4 mb-5">

            {/* Bar Chart Card */}
            <div className="flex-1 border border-gray-100 rounded-2xl p-4 min-w-0">
              <div className="flex justify-between items-center mb-4 flex-wrap gap-2">
                <span className="text-xs text-gray-400 flex items-center gap-1.5">
                  <span className="w-2 h-2 bg-amber-500 rounded-full inline-block" />
                  آخر 30 يوم
                </span>
                <span className="text-sm font-semibold text-gray-900">توزيع درجات اعتلال الشبكية</span>
              </div>
              <div className="flex items-end gap-2 sm:gap-3 h-28 justify-center">
                {barData.map((bar) => (
                  <div key={bar.label} className="flex flex-col items-center gap-1.5 flex-1 max-w-12">
                    <div
                      className="w-full bg-amber-400 rounded-t-md min-w-4"
                      style={{ height: `${bar.height}px` }}
                    />
                    <span className="text-[10px] text-gray-400 text-center">{bar.label}</span>
                  </div>
                ))}
              </div>
            </div>

            {/* Stats Card */}
            <div className="sm:w-44 border border-gray-100 rounded-2xl p-4 flex flex-row sm:flex-col justify-between gap-4">
              <div className="flex justify-between items-start">
                <span className="bg-amber-50 text-amber-500 text-[11px] font-semibold rounded-full px-2 py-0.5">
                  +12% هذا الشهر
                </span>
                <UsersIcon size={22} color="#f59e0b" />
              </div>
              <div className="text-right">
                <div className="text-xs text-gray-400 mb-1">إجمالي عدد المرضى</div>
                <div className="text-3xl font-bold text-gray-900">1,284</div>
              </div>
            </div>
          </div>

          {/* Table */}
          <div className="border border-gray-100 rounded-2xl overflow-hidden">
            <div className="flex justify-between items-center px-5 py-3.5 border-b border-gray-100">
              <span className="text-amber-500 text-sm cursor-pointer">عرض الكل</span>
              <span className="text-sm font-semibold text-gray-900">آخر الفحوصات المنجزة</span>
            </div>

            {/* Table head */}
            <div className="grid grid-cols-3 md:grid-cols-5 px-5 py-2.5 bg-gray-50 border-b border-gray-100">
              <div className="hidden md:block text-xs text-gray-400 text-center">الإجراء</div>
              <div className="hidden md:block text-xs text-gray-400 text-center">نسبة الثقة</div>
              <div className="text-xs text-gray-400 text-center">التصنيف الآلي</div>
              <div className="text-xs text-gray-400 text-center">تاريخ الفحص</div>
              <div className="text-xs text-gray-400 text-center">المريض</div>
            </div>

            {/* Table rows */}
            {patients.map((p, idx) => (
              <div
                key={p.id}
                className={`grid grid-cols-3 md:grid-cols-5 px-5 py-3.5 items-center ${
                  idx < patients.length - 1 ? "border-b border-gray-50" : ""
                }`}
              >
                <div className="hidden md:flex justify-center">
                  <EyeIcon size={18} color="#ccc" />
                </div>
                <div className={`hidden md:block text-center text-sm font-semibold ${p.confidenceColor}`}>
                  {p.confidence}
                </div>
                <div className="text-center">
                  <span className={`text-xs font-medium px-3 py-1 rounded-full ${classificationClasses[p.classType]}`}>
                    {p.classification}
                  </span>
                </div>
                <div className="text-center text-sm text-gray-500">{p.date}</div>
                <div className="flex items-center gap-2 justify-end">
                  <div className="text-right">
                    <div className="text-sm font-semibold text-gray-900">{p.name}</div>
                    <div className="text-xs text-gray-400">ID: {p.id}</div>
                  </div>
                  <div className="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center text-white text-xs font-semibold shrink-0">
                    {p.initials}
                  </div>
                </div>
              </div>
            ))}
          </div>

        </div>
      </div>
    </div>
  )
}