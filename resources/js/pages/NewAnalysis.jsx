import { useState, useRef, useCallback } from 'react'
import Navbar from '../components/Navbar'
import Sidebar from '../components/Sidebar'

// ========== ICONS ==========
const UploadCloudIcon = ({ size = 36 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="#f59e0b" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round">
    <polyline points="16 16 12 12 8 16"/>
    <line x1="12" y1="12" x2="12" y2="21"/>
    <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
  </svg>
)

const EyeIcon = ({ size = 16, color = "currentColor" }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
    <circle cx="12" cy="12" r="3"/>
  </svg>
)

const BrainIcon = ({ size = 16 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <path d="M9.5 2A2.5 2.5 0 0 1 12 4.5v15a2.5 2.5 0 0 1-4.96-.46 2.5 2.5 0 0 1-2.96-3.08 3 3 0 0 1-.34-5.58 2.5 2.5 0 0 1 1.32-4.24 2.5 2.5 0 0 1 4.44-1.14"/>
    <path d="M14.5 2A2.5 2.5 0 0 0 12 4.5v15a2.5 2.5 0 0 0 4.96-.46 2.5 2.5 0 0 0 2.96-3.08 3 3 0 0 0 .34-5.58 2.5 2.5 0 0 0-1.32-4.24 2.5 2.5 0 0 0-4.44-1.14"/>
  </svg>
)

const ZoomInIcon = ({ size = 15 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
    <line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/>
  </svg>
)

const ZoomOutIcon = ({ size = 15 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
    <line x1="8" y1="11" x2="14" y2="11"/>
  </svg>
)

const RotateIcon = ({ size = 15 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <polyline points="23 4 23 10 17 10"/>
    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
  </svg>
)

const MaximizeIcon = ({ size = 15 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
  </svg>
)

const ArrowRightIcon = ({ size = 15 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
  </svg>
)

const InfoIcon = ({ size = 14 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="#f59e0b" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <circle cx="12" cy="12" r="10"/>
    <line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
  </svg>
)

const PrintIcon = ({ size = 15 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <polyline points="6 9 6 2 18 2 18 9"/>
    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
    <rect x="6" y="14" width="12" height="8"/>
  </svg>
)

const SaveIcon = ({ size = 15 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
    <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
  </svg>
)

const ThumbUpIcon = ({ size = 14 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/>
    <path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/>
  </svg>
)

const ThumbDownIcon = ({ size = 14 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3H10z"/>
    <path d="M17 2h2.67A2.31 2.31 0 0 1 22 4v7a2.31 2.31 0 0 1-2.33 2H17"/>
  </svg>
)

// ========== درجات الخطورة ==========
const gradeStyles = {
  'Negative':      { label: 'طبيعي (Negative)',       bg: 'bg-green-100',  text: 'text-green-700' },
  'Mild':          { label: 'خفيف (Mild)',             bg: 'bg-blue-100',   text: 'text-blue-700' },
  'Moderate':      { label: 'متوسط (Moderate)',        bg: 'bg-amber-100',  text: 'text-amber-700' },
  'Severe':        { label: 'اعتلال شديد (Grade 4)',   bg: 'bg-orange-100', text: 'text-orange-700' },
  'Proliferative': { label: 'تكاثري (Proliferative)', bg: 'bg-red-100',    text: 'text-red-700' },
}

// ========== IMAGE VIEWER ==========
function ImageViewer({ imagePreview, zoom, rotation, result, onZoomIn, onZoomOut, onRotate, onReset }) {
  return (
    <div className="bg-gray-900 rounded-2xl overflow-hidden">
      {/* شريط الأدوات */}
      <div className="flex items-center justify-between px-4 py-2.5">
        <div className="flex items-center gap-2">
          <button onClick={onReset}   className="w-8 h-8 flex items-center justify-center bg-white/10 hover:bg-white/20 rounded-lg border-0 cursor-pointer transition-colors text-white"><MaximizeIcon /></button>
          <button onClick={onRotate}  className="w-8 h-8 flex items-center justify-center bg-white/10 hover:bg-white/20 rounded-lg border-0 cursor-pointer transition-colors text-white"><RotateIcon /></button>
          <button onClick={onZoomOut} className="w-8 h-8 flex items-center justify-center bg-white/10 hover:bg-white/20 rounded-lg border-0 cursor-pointer transition-colors text-white"><ZoomOutIcon /></button>
          <button onClick={onZoomIn}  className="w-8 h-8 flex items-center justify-center bg-white/10 hover:bg-white/20 rounded-lg border-0 cursor-pointer transition-colors text-white"><ZoomInIcon /></button>
        </div>
        <div className="flex items-center gap-2">
          <EyeIcon size={15} color="#f59e0b" />
          <span className="text-white text-sm">معاينة صورة الشبكية (Fundus)</span>
        </div>
      </div>

      {/* الصورة */}
      <div className="flex items-center justify-center bg-black overflow-hidden" style={{ height: '300px' }}>
        <img
          src={imagePreview}
          alt="fundus"
          style={{
            transform: `scale(${zoom}) rotate(${rotation}deg)`,
            transition: 'transform 0.2s ease',
            maxHeight: '280px',
            maxWidth: '100%',
            objectFit: 'contain',
          }}
        />
      </div>

      {/* نسبة الثقة فقط بعد التحليل */}
      {result && (
        <div className="px-5 py-3 border-t border-white/10">
          <div className="text-xs text-gray-400 mb-1 text-right">نسبة الثقة</div>
          <div className="text-lg font-bold text-white text-right">{result.confidence}%</div>
        </div>
      )}
    </div>
  )
}

// ========== MAIN COMPONENT ==========
export default function NewAnalysis() {
  const fileInputRef = useRef(null)

  const [stage, setStage]             = useState('upload')
  const [isDragging, setIsDragging]   = useState(false)
  const [mobileOpen, setMobileOpen]   = useState(false)
  const [doctorNotes, setDoctorNotes] = useState('')
  const [feedback, setFeedback]       = useState(null)
  const [saving, setSaving]           = useState(false)
  const [saved, setSaved]             = useState(false)
  const [analyzing, setAnalyzing]     = useState(false)

  const [patientName, setPatientName] = useState('')
  const [patientAge, setPatientAge]   = useState('')
  const [patientId, setPatientId]     = useState('ID-2045')

  const [imageFile, setImageFile]       = useState(null)
  const [imagePreview, setImagePreview] = useState(null)
  const [result, setResult]             = useState(null)

  const [zoom, setZoom]         = useState(1)
  const [rotation, setRotation] = useState(0)

  const handleFile = (file) => {
    if (!file || !file.type.startsWith('image/')) return
    setImageFile(file)
    setImagePreview(URL.createObjectURL(file))
    setStage('preview')
  }

  const handleDrop = useCallback((e) => {
    e.preventDefault()
    setIsDragging(false)
    handleFile(e.dataTransfer.files[0])
  }, [])

  const handleDragOver  = (e) => { e.preventDefault(); setIsDragging(true) }
  const handleDragLeave = () => setIsDragging(false)
  const handleFileInput = (e) => handleFile(e.target.files[0])

  const handleZoomIn  = () => setZoom(z => Math.min(z + 0.2, 3))
  const handleZoomOut = () => setZoom(z => Math.max(z - 0.2, 0.5))
  const handleRotate  = () => setRotation(r => r + 90)
  const handleReset   = () => { setZoom(1); setRotation(0) }

  const handleBack = () => {
    setStage('upload')
    setImageFile(null)
    setImagePreview(null)
    setZoom(1); setRotation(0)
    setResult(null); setSaved(false)
    setFeedback(null); setDoctorNotes('')
  }

  const handleAnalyze = async () => {
    if (!imageFile || !patientName) return
    setAnalyzing(true)

    const formData = new FormData()
    formData.append('image', imageFile)
    formData.append('patient_name', patientName)
    formData.append('patient_age', patientAge)
    formData.append('patient_id', patientId)

    try {
      // TODO: استبدلي الـ URL بعنوان Laravel الحقيقي
      // const response = await fetch('http://localhost:8000/api/analyze', {
      //   method: 'POST',
      //   body: formData,
      // })
      // const data = await response.json()

      await new Promise(r => setTimeout(r, 1500))
      const data = {
        classification: 'Severe',
        confidence: 99,
        date: new Date().toLocaleDateString('ar-EG', { year: 'numeric', month: 'long', day: 'numeric' }),
      }

      setResult(data)
      setStage('result')
    } catch (err) {
      console.error('خطأ بالتحليل:', err)
    } finally {
      setAnalyzing(false)
    }
  }

  const handleSave = async () => {
    if (!result) return
    setSaving(true)

    try {
      // TODO: استبدلي الـ URL بعنوان Laravel الحقيقي
      // await fetch('http://localhost:8000/api/reports/save', {
      //   method: 'POST',
      //   headers: { 'Content-Type': 'application/json' },
      //   body: JSON.stringify({
      //     patient_name: patientName,
      //     patient_age: patientAge,
      //     patient_id: patientId,
      //     classification: result.classification,
      //     confidence: result.confidence,
      //     doctor_notes: doctorNotes,
      //     feedback: feedback,
      //   }),
      // })

      await new Promise(r => setTimeout(r, 1000))
      setSaved(true)
    } catch (err) {
      console.error('خطأ بالحفظ:', err)
    } finally {
      setSaving(false)
    }
  }

  const grade = result ? gradeStyles[result.classification] : null

  const viewerProps = {
    imagePreview, zoom, rotation, result,
    onZoomIn: handleZoomIn,
    onZoomOut: handleZoomOut,
    onRotate: handleRotate,
    onReset: handleReset,
  }

  return (
    <div className="min-h-screen bg-white flex flex-col font-sans" dir="rtl">
      <Navbar onMenuClick={() => setMobileOpen(true)} />

      <div className="flex flex-1">
        <Sidebar mobileOpen={mobileOpen} onClose={() => setMobileOpen(false)} />

        <div className="flex-1 p-5 md:p-6 min-w-0">

          {/* ===== المرحلة 1: رفع الصورة ===== */}
          {stage === 'upload' && (
            <div className="max-w-2xl mx-auto">
              <h2 className="text-lg font-bold text-gray-900 text-right mb-4">تفاصيل المريض</h2>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div className="sm:col-span-2">
                  <label className="block text-xs text-gray-500 mb-1 text-right">اسم المريض</label>
                  <input
                    type="text"
                    placeholder="أدخل اسم المريض..."
                    value={patientName}
                    onChange={e => setPatientName(e.target.value)}
                    className="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-right outline-none focus:border-amber-400 transition-colors"
                  />
                </div>
                <div>
                  <label className="block text-xs text-gray-500 mb-1 text-right">العمر</label>
                  <input
                    type="number"
                    placeholder="45"
                    value={patientAge}
                    onChange={e => setPatientAge(e.target.value)}
                    className="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-right outline-none focus:border-amber-400 transition-colors"
                  />
                </div>
                <div>
                  <label className="block text-xs text-gray-500 mb-1 text-right">الرقم الطبي</label>
                  <input
                    type="text"
                    value={patientId}
                    onChange={e => setPatientId(e.target.value)}
                    className="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-right outline-none focus:border-amber-400 transition-colors"
                  />
                </div>
              </div>

              <div
                onDrop={handleDrop}
                onDragOver={handleDragOver}
                onDragLeave={handleDragLeave}
                onClick={() => fileInputRef.current.click()}
                className={`border-2 border-dashed rounded-2xl p-10 flex flex-col items-center justify-center cursor-pointer transition-colors mb-5 ${
                  isDragging ? 'border-amber-400 bg-amber-50' : 'border-gray-200 bg-gray-50 hover:border-amber-300 hover:bg-amber-50/40'
                }`}
              >
                <div className="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center mb-4">
                  <UploadCloudIcon size={32} />
                </div>
                <p className="text-xl font-bold text-gray-900 mb-2">اسحب وأفلت الصورة هنا</p>
                <p className="text-sm text-gray-400 mb-1">أو انقر لاختيار ملف من جهازك</p>
                <p className="text-xs text-gray-400">JPG, PNG UP TO 10MB</p>
                <input ref={fileInputRef} type="file" accept="image/*" className="hidden" onChange={handleFileInput} />
              </div>

              <div className="border border-amber-200 bg-amber-50/60 rounded-xl p-4">
                <div className="flex items-center gap-2 mb-2">
                  <InfoIcon />
                  <span className="text-sm font-semibold text-amber-600">إرشادات الرفع</span>
                </div>
                <p className="text-xs text-gray-500 leading-relaxed text-right">
                  تأكد من أن الصورة واضحة، مركزة على الشبكية، وخالية من الضوضاء. يتطلب النظام صور عالية الدقة للحصول على أدق نتائج تشخيص اعتلال الشبكية السكرية.
                </p>
              </div>
            </div>
          )}

          {/* ===== المرحلة 2: معاينة الصورة ===== */}
          {stage === 'preview' && (
            <div className="max-w-2xl mx-auto">
              <div className="text-right mb-4">
                <span className="text-base font-bold text-gray-900">المريض : {patientName || 'غير محدد'}</span>
              </div>

              <ImageViewer {...viewerProps} />

              <div className="flex items-center justify-between gap-3 mt-4">
                <button
                  onClick={handleAnalyze}
                  disabled={analyzing}
                  className="flex items-center gap-2 bg-amber-500 hover:bg-amber-600 disabled:opacity-60 text-white text-sm font-semibold px-5 py-2.5 rounded-xl border-0 cursor-pointer transition-colors font-sans"
                >
                  <BrainIcon size={16} />
                  {analyzing ? 'جاري التحليل...' : 'بدء تحليل الصورة بواسطة الذكاء الاصطناعي'}
                </button>
                <button
                  onClick={handleBack}
                  className="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-semibold px-5 py-2.5 rounded-xl border-0 cursor-pointer transition-colors font-sans"
                >
                  رجوع <ArrowRightIcon />
                </button>
              </div>
            </div>
          )}

          {/* ===== المرحلة 3: نتيجة التحليل ===== */}
          {stage === 'result' && result && (
            <div className="max-w-3xl mx-auto">

              {/* Header */}
              <div className="flex items-start justify-between mb-5 flex-wrap gap-3">
                <div className="flex items-center gap-2">
                  {saved ? (
                    <span className="text-green-600 text-sm font-semibold bg-green-50 px-4 py-2 rounded-xl">✓ تم الحفظ</span>
                  ) : (
                    <button
                      onClick={handleSave}
                      disabled={saving}
                      className="flex items-center gap-2 bg-amber-500 hover:bg-amber-600 disabled:opacity-60 text-white text-sm font-semibold px-4 py-2 rounded-xl border-0 cursor-pointer transition-colors font-sans"
                    >
                      <SaveIcon />
                      {saving ? 'جاري الحفظ...' : 'حفظ التقرير'}
                    </button>
                  )}
                  <button
                    onClick={() => window.print()}
                    className="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-semibold px-4 py-2 rounded-xl border-0 cursor-pointer transition-colors font-sans"
                  >
                    <PrintIcon />
                    طباعة التقرير
                  </button>
                </div>
                <div className="text-right">
                  <h1 className="text-xl font-bold text-gray-900">نتائج تحليل شبكية العين</h1>
                  <p className="text-xs text-gray-400 mt-1">تاريخ الفحص: {result.date} | المريض: {patientName}</p>
                </div>
              </div>

              {/* محتوى النتيجة */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-5">

                {/* العمود الأيمن: الصورة */}
                <div>
                  <ImageViewer {...viewerProps} />
                </div>

                {/* العمود الأيسر: التفاصيل */}
                <div className="flex flex-col gap-4">

                  {/* التصنيف */}
                  <div className="border border-gray-100 rounded-2xl p-4">
                    <div className="flex items-center gap-2 mb-3 justify-end">
                      <span className="text-sm font-semibold text-gray-700">التشخيص</span>
                      <EyeIcon size={16} color="#f59e0b" />
                    </div>
                    <div className="flex justify-end">
                      <span className={`text-sm font-semibold px-4 py-1.5 rounded-full ${grade.bg} ${grade.text}`}>
                        ● {grade.label}
                      </span>
                    </div>
                  </div>

                  {/* ملاحظات الطبيب */}
                  <div className="border border-gray-100 rounded-2xl p-4">
                    <h3 className="text-sm font-semibold text-gray-700 text-right mb-3">ملاحظات الطبيب المختص</h3>
                    <textarea
                      placeholder="أدخل توصياتك الطبية هنا..."
                      value={doctorNotes}
                      onChange={e => setDoctorNotes(e.target.value)}
                      rows={3}
                      className="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm text-right outline-none focus:border-amber-400 transition-colors resize-none"
                    />
                  </div>

                  {/* تقييم الذكاء الاصطناعي */}
                  <div className="border border-gray-100 rounded-2xl p-4">
                    <h3 className="text-sm font-semibold text-gray-700 text-right mb-3">تقييم الذكاء الاصطناعي</h3>
                    <div className="flex gap-3 justify-end">
                      <button
                        onClick={() => setFeedback('inaccurate')}
                        className={`flex items-center gap-2 px-4 py-2 rounded-xl text-sm border-0 cursor-pointer transition-colors font-sans ${
                          feedback === 'inaccurate' ? 'bg-red-100 text-red-600 font-semibold' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'
                        }`}
                      >
                        <ThumbDownIcon /> غير دقيق
                      </button>
                      <button
                        onClick={() => setFeedback('accurate')}
                        className={`flex items-center gap-2 px-4 py-2 rounded-xl text-sm border-0 cursor-pointer transition-colors font-sans ${
                          feedback === 'accurate' ? 'bg-green-100 text-green-600 font-semibold' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'
                        }`}
                      >
                        <ThumbUpIcon /> دقيق
                      </button>
                    </div>
                  </div>

                </div>
              </div>

              {/* زر رجوع */}
              <div className="mt-5 flex justify-start">
                <button
                  onClick={handleBack}
                  className="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-semibold px-5 py-2.5 rounded-xl border-0 cursor-pointer transition-colors font-sans"
                >
                  رجوع <ArrowRightIcon />
                </button>
              </div>

            </div>
          )}

        </div>
      </div>
    </div>
  )
}
