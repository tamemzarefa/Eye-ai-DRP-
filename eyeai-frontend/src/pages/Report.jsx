import { useState } from 'react'
import Navbar from '../components/Navbar'
import Sidebar from '../components/Sidebar'

const SendIcon = ({ size = 16 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <line x1="22" y1="2" x2="11" y2="13"/>
    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
  </svg>
)

const SaveIcon = ({ size = 15 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
    <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
  </svg>
)

const BotIcon = ({ size = 22 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
    <line x1="12" y1="3" x2="12" y2="7"/>
    <circle cx="9" cy="16" r="1" fill="white"/>
    <circle cx="15" cy="16" r="1" fill="white"/>
  </svg>
)

const XIcon = ({ size = 14 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <line x1="18" y1="6" x2="6" y2="18"/>
    <line x1="6" y1="6" x2="18" y2="18"/>
  </svg>
)

const SparkleIcon = ({ size = 40 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="#f97316">
    <path d="M12 2L13.5 9L20 10.5L13.5 12L12 19L10.5 12L4 10.5L10.5 9L12 2Z"/>
    <path d="M19 2L19.75 5.25L23 6L19.75 6.75L19 10L18.25 6.75L15 6L18.25 5.25L19 2Z" opacity="0.6"/>
    <path d="M5 14L5.5 16.5L8 17L5.5 17.5L5 20L4.5 17.5L2 17L4.5 16.5L5 14Z" opacity="0.6"/>
  </svg>
)

const ZoomInIcon = ({ size = 13 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
    <line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/>
  </svg>
)

const ZoomOutIcon = ({ size = 13 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
    <line x1="8" y1="11" x2="14" y2="11"/>
  </svg>
)

const MinusIcon = ({ size = 13 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2.5" strokeLinecap="round">
    <line x1="5" y1="12" x2="19" y2="12"/>
  </svg>
)

const PlusIcon = ({ size = 13 }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2.5" strokeLinecap="round">
    <line x1="12" y1="5" x2="12" y2="19"/>
    <line x1="5" y1="12" x2="19" y2="12"/>
  </svg>
)

const chatSuggestions = [
  'What is the last report we exported?',
  'How many employees are using our software?',
  "How can't change the colours of my report?",
]

export default function Report() {
  const [mobileOpen, setMobileOpen] = useState(false)
  const [chatOpen, setChatOpen] = useState(false)
  const [messages, setMessages] = useState([])
  const [input, setInput] = useState('')
  const [loading, setLoading] = useState(false)
  const [chatStarted, setChatStarted] = useState(false)
  const [zoom, setZoom] = useState(100)

  const handleZoomIn  = () => setZoom(z => Math.min(z + 10, 200))
  const handleZoomOut = () => setZoom(z => Math.max(z - 10, 50))

  const handleSend = async (text) => {
    const msg = text || input
    if (!msg.trim()) return
    setInput('')
    setChatStarted(true)

    const newMessages = [...messages, { role: 'user', content: msg }]
    setMessages(newMessages)
    setLoading(true)

    try {
      // const response = await fetch('http://localhost:8000/api/chat', {
      //   method: 'POST',
      //   headers: { 'Content-Type': 'application/json' },
      //   body: JSON.stringify({ message: msg }),
      // })
      // const data = await response.json()

      await new Promise(r => setTimeout(r, 1000))
      const reply = `Great question! You can ask for my help with the following:
1. Anything to do with your reports in our software e.g. What is the last report we exported?
2. Anything to do with your organisation e.g. how many employees are using our software?
3. Anything to do with the features we have in our software e.g how can't change the colours of my report?`

      setMessages([...newMessages, { role: 'ai', content: reply }])
    } catch (err) {
      console.error('خطأ بالشات:', err)
    } finally {
      setLoading(false)
    }
  }

  const handleOpenChat = () => {
    setChatOpen(true)
  }

  const handleCloseChat = () => {
    setChatOpen(false)
  }

  return (
    <div className="min-h-screen bg-white flex flex-col font-sans" style={{ height: '100vh', overflow: 'hidden' }}>
      <Navbar onMenuClick={() => setMobileOpen(true)} />

      <div className="flex-1 flex overflow-hidden flex-row-reverse">
        <Sidebar mobileOpen={mobileOpen} onClose={() => setMobileOpen(false)} />

        <div className="flex-1 flex overflow-hidden" >

          {/* منطقة التقرير */}
          <div className={`flex flex-col transition-all duration-300 overflow-hidden ${chatOpen ? 'w-1/2' : 'flex-1'}`}>

            {/* شريط التقرير */}
            <div className="flex items-center gap-2 px-4 py-2 bg-amber-500 mx-4 mt-4 rounded-xl shrink-0">
              <span className="text-white text-sm font-semibold">File name</span>
         <div className="flex items-center gap-1 mx-auto">
         <button onClick={handleZoomOut} className="w-7 h-7 flex items-center justify-center bg-white/20 hover:bg-white/30 rounded-lg border-0 cursor-pointer transition-colors">
         <MinusIcon />
          </button>
            <span className="text-white text-xs px-2">{zoom}%</span>
           <button onClick={handleZoomIn} className="w-7 h-7 flex items-center justify-center bg-white/20 hover:bg-white/30 rounded-lg border-0 cursor-pointer transition-colors">
                <PlusIcon />
            </button>
            </div>
             <span className="text-white text-xs">2 / 1</span>
            </div>

            {/* محتوى التقرير */}
            <div className="flex-1 p-4 overflow-auto">
              <div
                className="bg-white border border-gray-100 rounded-xl shadow-sm mx-auto transition-all"
                style={{ width: `${zoom}%`, minHeight: '500px' }}
              >
                {/* TODO: هون رح يتحمل الـ PDF من Laravel */}
              </div>
            </div>
          </div>

          {/* شات بوت */}
          {chatOpen && (
            <div className="w-1/2 flex flex-col overflow-hidden border-l border-gray-100" 
              style={{ background: 'linear-gradient(180deg, #fff7ed 0%, #ffffff 40%)' }}>

              {/* هيدر الشات */}
              <div className="flex items-center justify-between px-5 py-3 border-b border-orange-100 shrink-0">
                <button
                  onClick={() => {}}
                  className="flex items-center gap-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg border-0 cursor-pointer transition-colors font-sans"
                >
                  <SaveIcon size={13} />
                  حفظ النتائج
                </button>
                <div className="flex items-center gap-2">
                  <h2 className="text-base font-bold text-gray-900">تعديل التقرير</h2>
                  <button
                    onClick={handleCloseChat}
                    className="w-7 h-7 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg border-0 cursor-pointer transition-colors"
                  >
                    <XIcon />
                  </button>
                </div>
              </div>

              {/* محتوى الشات */}
              <div className="flex-1 overflow-auto px-5 py-4">
                {!chatStarted ? (
                  <div className="flex flex-col items-center justify-center h-full gap-5">
                    <SparkleIcon size={40} />
                    <div className="w-full">
                      <p className="text-xs text-gray-400 mb-1">Hi,</p>
                      <p className="text-sm text-gray-600 mb-4">What can I ask you to do?</p>
                      <div className="flex flex-col gap-2">
                        {chatSuggestions.map((s, i) => (
                          <button
                            key={i}
                            onClick={() => handleSend(s)}
                            className="text-left text-xs text-gray-500 bg-white hover:bg-orange-50 border border-gray-200 rounded-xl px-3 py-2 cursor-pointer transition-colors font-sans"
                          >
                            {s}
                          </button>
                        ))}
                      </div>
                    </div>
                  </div>
                ) : (
                  <div className="flex flex-col gap-4 pb-2">
                    {messages.map((msg, i) => (
                      <div key={i} className={`flex ${msg.role === 'user' ? 'justify-end' : 'justify-start'}`}>
                        {msg.role === 'ai' && (
                          <div className="w-7 h-7 bg-amber-500 rounded-full flex items-center justify-center shrink-0 ml-2 mt-1">
                            <BotIcon size={14} />
                          </div>
                        )}
                        <div className={`max-w-xs rounded-2xl px-4 py-3 text-sm whitespace-pre-line ${
                          msg.role === 'user'
                            ? 'bg-amber-500 text-white rounded-tr-sm'
                            : 'bg-white border border-gray-100 text-gray-700 rounded-tl-sm shadow-sm'
                        }`}>
                          {msg.content}
                        </div>
                      </div>
                    ))}
                    {loading && (
                      <div className="flex justify-start">
                        <div className="w-7 h-7 bg-amber-500 rounded-full flex items-center justify-center shrink-0 ml-2">
                          <BotIcon size={14} />
                        </div>
                        <div className="bg-white border border-gray-100 rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm">
                          <div className="flex gap-1">
                            <div className="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style={{ animationDelay: '0ms' }} />
                            <div className="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style={{ animationDelay: '150ms' }} />
                            <div className="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style={{ animationDelay: '300ms' }} />
                          </div>
                        </div>
                      </div>
                    )}
                  </div>
                )}
              </div>

              {/* input الشات */}
              <div className="px-5 py-4 border-t border-orange-100 shrink-0">
                <div className="flex items-center gap-2 border border-gray-200 rounded-xl px-3 py-2 bg-white">
                  <button
                    onClick={() => handleSend()}
                    className="w-7 h-7 bg-amber-500 hover:bg-amber-600 rounded-lg flex items-center justify-center border-0 cursor-pointer transition-colors shrink-0"
                  >
                    <SendIcon size={13} color="white" />
                  </button>
                  <input
                    dir="ltr"
                    type="text"
                    value={input}
                    onChange={e => setInput(e.target.value)}
                    onKeyDown={e => e.key === 'Enter' && handleSend()}
                    placeholder="Type your message..."
                    className="flex-1 text-sm outline-none bg-transparent"
                  />
                </div>
              </div>
            </div>
          )}
        </div>

        {/* زر الشات العائم */}
        {!chatOpen && (
          <button
            onClick={handleOpenChat}
            className="fixed bottom-6 left-6 w-14 h-14 bg-amber-500 hover:bg-amber-600 rounded-full flex items-center justify-center border-0 cursor-pointer shadow-lg transition-colors z-50"
          >
            <BotIcon size={24} />
          </button>
        )}
      </div>
    </div>
  )
}