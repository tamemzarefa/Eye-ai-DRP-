import { useState, useEffect } from 'react'
import { useLocation, useParams } from 'react-router-dom'
import Navbar from '../components/Navbar'
import Sidebar from '../components/Sidebar'
import { authFetch } from '../lib/auth'

export default function Report() {
  const [mobileOpen, setMobileOpen] = useState(false)
  const [report, setReport] = useState(null)
  const [loading, setLoading] = useState(true)
  const { id } = useParams()
  const location = useLocation()

  useEffect(() => {
    let mounted = true
    const fetchReport = async () => {
      if (!id || id === 'preview') {
        const incoming = location.state?.report
        if (incoming) {
          setReport(incoming)
        }
        setLoading(false)
        return
      }

      try {
        const res = await authFetch(`/api/v1/reports/${id}`)
        if (!mounted) return
        if (res.ok) {
          const data = await res.json()
          setReport(data)
        } else {
          setReport(null)
        }
      } catch (err) {
        console.error(err)
        setReport(null)
      } finally {
        setLoading(false)
      }
    }

    fetchReport()
    return () => { mounted = false }
  }, [id])

  return (
    <div className="min-h-screen bg-white flex flex-col font-sans" style={{ height: '100vh' }}>
      <Navbar onMenuClick={() => setMobileOpen(true)} />

      <div className="flex-1 flex">
        <Sidebar mobileOpen={mobileOpen} onClose={() => setMobileOpen(false)} />

        <div className="flex-1 p-6">
          <h1 className="text-2xl font-bold mb-4">عرض التقرير</h1>

          {loading && <div>جارٍ تحميل التقرير...</div>}

          {!loading && !report && id === 'preview' && (
            <div className="p-4 bg-yellow-50 border border-yellow-100 rounded">هذا تقرير معاينة — لم يتم حفظه في الخادم.</div>
          )}

          {!loading && !report && id !== 'preview' && (
            <div className="p-4 bg-red-50 border border-red-100 rounded">لم يتم العثور على التقرير.</div>
          )}

          {report && (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <div className="border rounded p-4">
                  <div className="text-sm text-gray-500 mb-2">المريض</div>
                  <div className="font-semibold text-lg">{report.patient_name}</div>
                  <div className="text-xs text-gray-400">الرقم الطبي: {report.patient_id} • العمر: {report.patient_age}</div>
                </div>

                <div className="border rounded p-4 mt-4">
                  <div className="text-sm text-gray-500 mb-2">نتيجة النموذج</div>
                  <div className="text-xl font-bold">{report.classification} — {report.confidence}%</div>
                  <div className="text-xs text-gray-400 mt-2">تاريخ: {report.date}</div>
                  <div className="mt-3">ملاحظات الطبيب: {report.doctor_notes}</div>
                </div>
              </div>

              <div>
                {report.image_preview ? (
                  <img src={report.image_preview} alt="preview" className="w-full rounded border" />
                ) : (
                  <div className="h-48 bg-gray-50 rounded border flex items-center justify-center text-gray-400">لا توجد معاينة للصورة</div>
                )}
              </div>
            </div>
          )}

        </div>
      </div>
    </div>
  )
}
