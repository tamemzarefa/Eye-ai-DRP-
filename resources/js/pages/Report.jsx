import { useState, useEffect } from 'react'
import { useLocation, useParams } from 'react-router-dom'
import Navbar from '../components/Navbar'
import Sidebar from '../components/Sidebar'
import { authFetch } from '../lib/auth'

// خريطة ألوان افتراضية للتعامل مع الحالات بآمان ومنع أخطاء الـ .bg
const STATUS_STYLES = {
  'No DR': { bg: 'bg-green-100', text: 'text-green-800', border: 'border-green-200' },
  'Mild': { bg: 'bg-yellow-100', text: 'text-yellow-800', border: 'border-yellow-200' },
  'Moderate': { bg: 'bg-orange-100', text: 'text-orange-800', border: 'border-orange-200' },
  'Severe': { bg: 'bg-red-100', text: 'text-red-800', border: 'border-red-200' },
  'Proliferative DR': { bg: 'bg-purple-100', text: 'text-purple-800', border: 'border-purple-200' },
  'Default': { bg: 'bg-gray-100', text: 'text-gray-800', border: 'border-gray-200' },
}

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
          // دعم استخراج data إذا كانت النتيجة ملفوفة بداخل كائن { data: { ... } }
          setReport(data?.data || data)
        } else {
          setReport(null)
        }
      } catch (err) {
        console.error('Error fetching report:', err)
        setReport(null)
      } finally {
        if (mounted) setLoading(false)
      }
    }

    fetchReport()
    return () => { mounted = false }
  }, [id, location.state])

  // 🛠️ دالة معالجة واستخراج نص التصنيف بآمان ومنع خطأ Objects as React child
  const getClassificationText = (classification) => {
    if (!classification) return 'غير محدد'
    if (typeof classification === 'string') return classification
    if (typeof classification === 'object') {
      return classification.stage || classification.grade || classification.label || classification.prediction || JSON.stringify(classification)
    }
    return String(classification)
  }

  // 🛠️ دالة حساب نسبة الثقة بشكل منسق
  const getConfidenceValue = (confidence) => {
    if (confidence === undefined || confidence === null) return null
    if (typeof confidence === 'number') {
      // إذا كانت النسبة بين 0 و 1 يتم ضربها بـ 100
      const val = confidence <= 1 ? confidence * 100 : confidence
      return val.toFixed(1)
    }
    return String(confidence)
  }

  const classificationText = getClassificationText(report?.classification || report?.prediction)
  const confidenceText = getConfidenceValue(report?.confidence)
  const badgeStyle = STATUS_STYLES[classificationText] || STATUS_STYLES['Default']

  return (
    <div className="min-h-screen bg-white flex flex-col font-sans" style={{ height: '100vh' }}>
      <Navbar onMenuClick={() => setMobileOpen(true)} />

      <div className="flex-1 flex overflow-hidden">
        <Sidebar mobileOpen={mobileOpen} onClose={() => setMobileOpen(false)} />

        <div className="flex-1 p-6 overflow-y-auto">
          <h1 className="text-2xl font-bold mb-6 text-gray-800">عرض التقرير الطبي</h1>

          {loading && (
            <div className="flex items-center justify-center p-12 text-gray-500">
              <span className="ml-2">جارٍ تحميل التقرير...</span>
            </div>
          )}

          {!loading && !report && id === 'preview' && (
            <div className="p-4 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg">
              هذا تقرير معاينة — لم يتم حفظه في الخادم بعد.
            </div>
          )}

          {!loading && !report && id !== 'preview' && (
            <div className="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
              لم يتم العثور على التقرير المطلوب.
            </div>
          )}

          {!loading && report && (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              {/* قسم تفاصيل المريض والنتيجة */}
              <div className="space-y-4">
                <div className="border border-gray-200 rounded-lg p-5 bg-white shadow-sm">
                  <div className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">معلومات المريض</div>
                  <div className="font-bold text-xl text-gray-800">{report.patient_name || 'غير محدد'}</div>
                  <div className="text-sm text-gray-500 mt-1">
                    الرقم الطبي: <span className="font-medium text-gray-700">{report.patient_id || '-'}</span> • العمر: <span className="font-medium text-gray-700">{report.patient_age || '-'}</span>
                  </div>
                </div>

                <div className="border border-gray-200 rounded-lg p-5 bg-white shadow-sm">
                  <div className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">نتيجة الفحص الآلي (AI Analysis)</div>
                  
                  <div className="flex items-center space-x-3 space-x-reverse mb-3">
                    <span className={`px-3 py-1 rounded-full text-base font-bold border ${badgeStyle.bg} ${badgeStyle.text} ${badgeStyle.border}`}>
                      {classificationText}
                    </span>
                    {confidenceText && (
                      <span className="text-lg font-semibold text-gray-700">
                        {confidenceText}%
                      </span>
                    )}
                  </div>

                  <div className="text-xs text-gray-400">
                    تاريخ الفحص: {report.date || new Date().toLocaleDateString('ar-EG')}
                  </div>

                  {/* التقرير الطبي المفصل إذا توفر من FastAPI */}
                  {report.Epicrisis && (
                    <div className="mt-4 pt-4 border-t border-gray-100">
                      <div className="text-xs font-bold text-gray-600 mb-1">التقييم الطبي (Epicrisis):</div>
                      <p className="text-sm text-gray-700 leading-relaxed bg-gray-50 p-3 rounded-md border border-gray-100">
                        {report.Epicrisis}
                      </p>
                    </div>
                  )}

                  {report.Procedere && (
                    <div className="mt-3">
                      <div className="text-xs font-bold text-gray-600 mb-1">الإجراءات الموصى بها (Procedere):</div>
                      <p className="text-sm text-gray-700 leading-relaxed bg-gray-50 p-3 rounded-md border border-gray-100">
                        {report.Procedere}
                      </p>
                    </div>
                  )}

                  <div className="mt-4 pt-3 border-t border-gray-100">
                    <span className="text-xs font-semibold text-gray-500">ملاحظات الطبيب:</span>
                    <p className="text-sm text-gray-700 mt-1">{report.doctor_notes || 'لا توجد ملاحظات مدونة.'}</p>
                  </div>
                </div>
              </div>

              {/* قسم معاينة الصورة */}
              <div>
                <div className="border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                  <div className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">صورة شبكية العين المرفوعة</div>
                  {report.image_preview || report.image_url ? (
                    <img
                      src={report.image_preview || report.image_url}
                      alt="Retina Scan Preview"
                      className="w-full h-auto rounded-lg border border-gray-100 object-cover max-h-[450px]"
                    />
                  ) : (
                    <div className="h-64 bg-gray-50 rounded-lg border border-dashed border-gray-200 flex items-center justify-center text-gray-400 text-sm">
                      لا توجد معاينة متاحة للصورة
                    </div>
                  )}
                </div>
              </div>
            </div>
          )}

        </div>
      </div>
    </div>
  )
}