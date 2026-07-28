import { useState, useEffect, useRef } from 'react'
import { useLocation, useParams } from 'react-router-dom'
import Navbar from '../components/Navbar'
import Sidebar from '../components/Sidebar'
import { authFetch } from '../lib/auth'

const GRADE_MAP = {
  0: 'No DR (سليم)',
  1: 'Mild NPDR (خفيف)',
  2: 'Moderate NPDR (متوسط)',
  3: 'Severe NPDR (شديد)',
  4: 'Proliferative DR (متقدم)',
}

const STATUS_STYLES = {
  'No DR (سليم)': { bg: 'bg-green-100', text: 'text-green-800', border: 'border-green-200' },
  'Mild NPDR (خفيف)': { bg: 'bg-yellow-100', text: 'text-yellow-800', border: 'border-yellow-200' },
  'Moderate NPDR (متوسط)': { bg: 'bg-orange-100', text: 'text-orange-800', border: 'border-orange-200' },
  'Severe NPDR (شديد)': { bg: 'bg-red-100', text: 'text-red-800', border: 'border-red-200' },
  'Proliferative DR (متقدم)': { bg: 'bg-purple-100', text: 'text-purple-800', border: 'border-purple-200' },
  'Default': { bg: 'bg-blue-100', text: 'text-blue-800', border: 'border-blue-200' },
}

export default function Report() {
  const [mobileOpen, setMobileOpen] = useState(false)
  const [report, setReport] = useState(null)
  const [loading, setLoading] = useState(true)
  const { id } = useParams()
  const location = useLocation()
  const [chatOpen, setChatOpen]       = useState(false)
const [messages, setMessages]       = useState([])
const [chatInput, setChatInput]     = useState('')
const [chatLoading, setChatLoading] = useState(false)
const chatEndRef = useRef(null)

useEffect(() => {
  chatEndRef.current?.scrollIntoView({ behavior: 'smooth' })
}, [messages])

  useEffect(() => {
    let mounted = true
    const fetchReport = async () => {
      if (!id || id === 'preview') {
        const incoming = location.state?.report
        if (incoming) {
          // التعامل مع كائن المعاينة والتأكد من تفكيك حقل data إن وجد
          const resolved = incoming?.data?.prediction ? incoming.data : incoming
          setReport(resolved)
        }
        setLoading(false)
        return
      }

      try {
        const res = await authFetch(`/api/v1/reports/${id}`)
        if (!mounted) return
        if (res.ok) {
          const resData = await res.json()
          // تفكيك الطبقات المتعددة لضمان الوصول لكائن التقرير المباشر
          let resolvedData = resData?.data || resData
          if (resolvedData?.data && !resolvedData.prediction) {
            resolvedData = resolvedData.data
          }
          setReport(resolvedData)
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

  // 🔍 دالة ذكية للاستخراج العميق للبيانات المترابطة
  const getNestedValue = (obj, keys) => {
    if (!obj) return null
    for (let key of keys) {
      const parts = key.split('.')
      let curr = obj
      for (let p of parts) {
        curr = curr?.[p]
      }
      if (curr !== undefined && curr !== null) return curr
    }
    return null
  }
  const handleSendMessage = async () => {
  const question = chatInput.trim()
  if (!question || chatLoading) return

  setMessages(prev => [...prev, { role: 'user', text: question }])
  setChatInput('')
  setChatLoading(true)

  try {
    const res = await authFetch('/api/v1/chat', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ question }),
    })

    const data = await res.json()

    if (data.error) {
      setMessages(prev => [...prev, { role: 'assistant', text: 'لا يوجد تحليل متاح حاليًا لطرح الأسئلة عليه.' }])
    } else {
      setMessages(prev => [...prev, { role: 'assistant', text: data.answer }])
    }
  } catch (err) {
    console.error('خطأ بالشات:', err)
    setMessages(prev => [...prev, { role: 'assistant', text: 'حدث خطأ أثناء الاتصال بالمساعد الطبي.' }])
  } finally {
    setChatLoading(false)
  }
}

const handleKeyPress = (e) => {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault()
    handleSendMessage()
  }
}

  // 🛠️ استخراج الفئة والترجمة
  const getClassificationText = (reportData) => {
    if (!reportData) return 'غير محدد'

    const stage = getNestedValue(reportData, ['prediction.stage', 'data.prediction.stage', 'stage'])
    if (stage !== null) {
      const stageNum = Number(stage)
      return GRADE_MAP[stageNum] || `Stage ${stageNum}`
    }

    const grade = getNestedValue(reportData, ['prediction.grade', 'data.prediction.grade', 'grade', 'classification'])
    if (grade) return grade

    return 'غير محدد'
  }

  // 🛠️ استخراج نسبة الثقة
  const getConfidenceValue = (reportData) => {
    const conf = getNestedValue(reportData, [
      'prediction.confidence',
      'data.prediction.confidence',
      'confidence',
      'confidence_score'
    ])

    if (conf === null || conf === undefined) return null

    const num = Number(conf)
    if (isNaN(num)) return null

    if (num > 0 && num <= 1) {
      return (num * 100).toFixed(1)
    }

    return num.toFixed(1)
  }

  // 🛠️ استخراج التقرير النصي
  const getReportText = (reportData) => {
    return getNestedValue(reportData, ['report', 'data.report', 'clinical_report'])
  }

  // 🛠️ استخراج رابط الخريطة الحرارية
  const getHeatmapUrl = (reportData) => {
    return getNestedValue(reportData, ['heatmap_url', 'data.heatmap_url'])
  }
  const getSegmentation = (reportData) => {
  return getNestedValue(reportData, ['segmentation', 'data.segmentation'])
}

  const classificationText = getClassificationText(report)
  const confidenceText = getConfidenceValue(report)
  const clinicalReportText = getReportText(report)
  const segmentation = getSegmentation(report)
  const heatmapUrl = getHeatmapUrl(report)
  const badgeStyle = STATUS_STYLES[classificationText] || STATUS_STYLES['Default']

  return (
    <div className="min-h-screen bg-white flex flex-col font-sans" style={{ height: '100vh' }}>
      <Navbar onMenuClick={() => setMobileOpen(true)} />

      <div className="flex-1 flex overflow-hidden">
        <Sidebar mobileOpen={mobileOpen} onClose={() => setMobileOpen(false)} />

        <div className="flex-1 p-6 overflow-y-auto">
          <h1 className="text-2xl font-bold mb-6 text-gray-800">عرض التقرير الطبي</h1>
          <div className="flex items-center justify-between mb-6">
  <h1 className="text-2xl font-bold text-gray-800">عرض التقرير الطبي</h1>
  <button
    onClick={() => setChatOpen(true)}
    className="flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-4 py-2 rounded-xl border-0 cursor-pointer transition-colors"
  >
    💬 اسأل المساعد الطبي
  </button>
</div>

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
                  <div className="font-bold text-xl text-gray-800">{report.patient_name || 'تميم'}</div>
                  <div className="text-sm text-gray-500 mt-1">
                    الرقم الطبي: <span className="font-medium text-gray-700">{report.patient_id || 'ID-2045'}</span> • العمر: <span className="font-medium text-gray-700">{report.patient_age || '23'}</span>
                  </div>
                </div>

                <div className="border border-gray-200 rounded-lg p-5 bg-white shadow-sm">
                  <div className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">نتيجة الفحص الآلي (AI Analysis)</div>
                  
                  <div className="flex items-center space-x-3 space-x-reverse mb-3">
                    <span className={`px-3 py-1 rounded-full text-base font-bold border ${badgeStyle.bg} ${badgeStyle.text} ${badgeStyle.border}`}>
                      {classificationText}
                    </span>
                    {confidenceText !== null && (
                      <span className="text-lg font-semibold text-gray-700">
                        {confidenceText}%
                      </span>
                    )}
                  </div>

                  <div className="text-xs text-gray-400">
                    تاريخ الفحص: {report.date || new Date().toLocaleDateString('ar-EG')}
                  </div>

                  {/* التقرير النصي الشامل القادم من النموذج */}
                  {clinicalReportText && (
                    <div className="mt-4 pt-4 border-t border-gray-100">
                      <div className="text-xs font-bold text-gray-600 mb-2">التقرير الطبي والتوصيات (Clinical Report):</div>
                      <div className="text-sm text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-md border border-gray-100 whitespace-pre-line dir-ltr text-left font-mono">
                        {clinicalReportText}
                      </div>
                    </div>
                  )}

                  <div className="mt-4 pt-3 border-t border-gray-100">
                    <span className="text-xs font-semibold text-gray-500">ملاحظات الطبيب المعالج:</span>
                    <p className="text-sm text-gray-700 mt-1">{report.doctor_notes || 'لا توجد ملاحظات مدونة.'}</p>
                  </div>
                </div>
              </div>

              {/* قسم معاينة الصور (الصورة الأساسية + الخريطة الحرارية Grad-CAM) */}
              <div className="space-y-4">
                {heatmapUrl && (
                  <div className="border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                    <div className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">الخريطة الحرارية للمرض (Grad-CAM Heatmap)</div>
                    <img
                      src={heatmapUrl}
                      alt="Grad-CAM Heatmap"
                      className="w-full h-auto rounded-lg border border-gray-100 object-cover max-h-[350px]"
                    />
                  </div>
                )}

{/* Segmentation Overlay + Masks */}
{segmentation && (
  <div className="border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
    <div className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">تحليل الآفات (Segmentation)</div>

    {segmentation.overlay_url && (
      <img
        src={segmentation.overlay_url}
        alt="Segmentation Overlay"
        className="w-full h-auto rounded-lg border border-gray-100 object-cover max-h-[350px] mb-4"
      />
    )}

    {segmentation.mask_urls && (
      <div className="grid grid-cols-3 gap-3">
        {Object.entries(segmentation.mask_urls).map(([className, url]) => (
          <div key={className} className="text-center">
            <img
              src={url}
              alt={className}
              className="w-full h-auto rounded-lg border border-gray-100 object-cover"
            />
            <span className="text-xs text-gray-500 mt-1 block">
              {className}
              {segmentation.positive_pixels?.[className] !== undefined && (
                <span className="text-gray-400">
                  {' '}({segmentation.positive_pixels[className]} px)
                </span>
              )}
            </span>
          </div>
        ))}
      </div>
    )}
  </div>
)}
{chatOpen && (
  <div className="fixed inset-0 bg-black/40 flex items-center justify-center z-50" onClick={() => setChatOpen(false)}>
    <div
      className="bg-white rounded-2xl w-full max-w-md h-[500px] flex flex-col shadow-xl"
      onClick={e => e.stopPropagation()}
    >
      {/* رأس النافذة */}
      <div className="flex items-center justify-between p-4 border-b border-gray-100">
        <h3 className="font-bold text-gray-800">المساعد الطبي الذكي</h3>
        <button
          onClick={() => setChatOpen(false)}
          className="text-gray-400 hover:text-gray-600 border-0 bg-transparent cursor-pointer text-xl"
        >
          ×
        </button>
      </div>

      {/* الرسائل */}
      <div className="flex-1 overflow-y-auto p-4 space-y-3">
        {messages.length === 0 && (
          <p className="text-sm text-gray-400 text-center mt-10">
            اسأل عن التشخيص أو التقرير الطبي...
          </p>
        )}

        {messages.map((msg, i) => (
          <div
            key={i}
            className={`flex ${msg.role === 'user' ? 'justify-end' : 'justify-start'}`}
          >
            <div
              className={`max-w-[80%] rounded-xl px-3 py-2 text-sm ${
                msg.role === 'user'
                  ? 'bg-amber-500 text-white'
                  : 'bg-gray-100 text-gray-800'
              }`}
            >
              {msg.text}
            </div>
          </div>
        ))}

        {chatLoading && (
          <div className="flex justify-start">
            <div className="bg-gray-100 text-gray-500 rounded-xl px-3 py-2 text-sm">
              جاري الكتابة...
            </div>
          </div>
        )}

        <div ref={chatEndRef} />
      </div>

      {/* إدخال الرسالة */}
      <div className="p-3 border-t border-gray-100 flex gap-2">
        <input
          type="text"
          value={chatInput}
          onChange={e => setChatInput(e.target.value)}
          onKeyDown={handleKeyPress}
          placeholder="اكتب سؤالك..."
          disabled={chatLoading}
          className="flex-1 border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-amber-400 transition-colors"
        />
        <button
          onClick={handleSendMessage}
          disabled={chatLoading || !chatInput.trim()}
          className="bg-amber-500 hover:bg-amber-600 disabled:opacity-50 text-white px-4 py-2 rounded-xl text-sm border-0 cursor-pointer transition-colors"
        >
          إرسال
        </button>
      </div>
    </div>
  </div>
)}

                <div className="border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                  <div className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">صورة شبكية العين الأصلية</div>
                  {report.image_preview || report.image_url ? (
                    <img
                      src={report.image_preview || report.image_url}
                      alt="Retina Scan Preview"
                      className="w-full h-auto rounded-lg border border-gray-100 object-cover max-h-[350px]"
                    />
                  ) : (
                    <div className="h-48 bg-gray-50 rounded-lg border border-dashed border-gray-200 flex items-center justify-center text-gray-400 text-sm">
                      لا توجد معاينة متاحة للصورة الأصلية
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