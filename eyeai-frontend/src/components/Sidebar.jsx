import { useNavigate, useLocation } from 'react-router-dom'

const EyeIcon = ({ size = 20, color = "currentColor" }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
    <circle cx="12" cy="12" r="3"/>
  </svg>
)

const GridIcon = ({ size = 16 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
    <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
  </svg>
)

const UsersIcon = ({ size = 16 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
    <circle cx="9" cy="7" r="4"/>
    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
  </svg>
)

const SettingsIcon = ({ size = 16 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <circle cx="12" cy="12" r="3"/>
    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
  </svg>
)

const HelpCircleIcon = ({ size = 16 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <circle cx="12" cy="12" r="10"/>
    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
    <line x1="12" y1="17" x2="12.01" y2="17"/>
  </svg>
)

const LogOutIcon = ({ size = 16 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="#ef4444" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
    <polyline points="16 17 21 12 16 7"/>
    <line x1="21" y1="12" x2="9" y2="12"/>
  </svg>
)

const XIcon = ({ size = 20 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <line x1="18" y1="6" x2="6" y2="18"/>
    <line x1="6" y1="6" x2="18" y2="18"/>
  </svg>
)

const sidebarItems = [
  { icon: <GridIcon />, label: 'لوحة التحكم', path: '/' },
  { icon: <EyeIcon size={16} />, label: 'فحص جديد', path: '/new-analysis' },
  { icon: <UsersIcon />, label: 'قائمة المرضى', path: '/patients' },
  { icon: <SettingsIcon />, label: 'الإعدادات', path: '/settings' },
]

function SidebarContent({ onClose }) {
  const navigate = useNavigate()
  const location = useLocation()

  const handleNavigate = (path) => {
    navigate(path)
    if (onClose) onClose()
  }

  return (
    <div className="flex flex-col justify-between h-full">
      <div>
        {/* Doctor profile */}
        <div className="flex items-center gap-3 px-4 pb-5 border-b border-gray-100 mb-4">
          <div className="text-right">
            <div className="text-sm font-bold text-amber-500">د. أحمد علي</div>
            <div className="text-xs text-gray-400">أخصائي طب العيون</div>
          </div>
          <div className="w-9 h-9 bg-amber-500 rounded-xl flex items-center justify-center shrink-0">
            <EyeIcon size={18} color="#fff" />
          </div>
        </div>

        {/* Nav items */}
        {sidebarItems.map((item) => {
          const isActive = location.pathname === item.path
          return (
            <div
              key={item.path}
              onClick={() => handleNavigate(item.path)}
              className={`flex items-center gap-3 px-4 py-2.5 cursor-pointer border-r-4 ${
                isActive
                  ? 'bg-amber-50 border-amber-500'
                  : 'border-transparent hover:bg-gray-50'
              }`}
            >
              <span className={isActive ? 'text-amber-500' : 'text-gray-400'}>{item.icon}</span>
              <span className={`text-sm ${isActive ? 'text-amber-500 font-semibold' : 'text-gray-500'}`}>
                {item.label}
              </span>
            </div>
          )
        })}
      </div>

      {/* Bottom */}
      <div className="border-t border-gray-100 pt-2">
        <div className="flex items-center gap-3 px-4 py-2.5 cursor-pointer hover:bg-gray-50">
          <HelpCircleIcon />
          <span className="text-sm text-gray-500">المساعدة</span>
        </div>
        <div className="flex items-center gap-3 px-4 py-2.5 cursor-pointer hover:bg-red-50">
          <LogOutIcon />
          <span className="text-sm text-red-500">خروج</span>
        </div>
      </div>
    </div>
  )
}

export default function Sidebar({ mobileOpen, onClose }) {
  return (
    <>
      {/* Desktop sidebar */}
      <div className="hidden md:flex w-44 border-r border-gray-100 flex-col py-5 shrink-0">
        <SidebarContent />
      </div>

      {/* Mobile drawer */}
      {mobileOpen && (
        <>
          <div
            className="fixed inset-0 bg-black/40 z-40 md:hidden"
            onClick={onClose}
          />
          <div className="fixed top-0 right-0 h-full w-52 bg-white z-50 shadow-xl py-5 md:hidden">
            <button
              className="absolute top-3 left-3 p-1 bg-transparent border-0 cursor-pointer"
              onClick={onClose}
            >
              <XIcon />
            </button>
            <div className="mt-6 h-full">
              <SidebarContent onClose={onClose} />
            </div>
          </div>
        </>
      )}
    </>
  )
}