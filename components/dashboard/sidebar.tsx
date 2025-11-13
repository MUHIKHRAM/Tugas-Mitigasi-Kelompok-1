"use client"

import { LayoutDashboard, MapPin, AlertTriangle, BarChart3, FileText, Zap, Settings, ChevronLeft } from "lucide-react"

interface SidebarProps {
  open: boolean
  onToggle: () => void
}

const menuItems = [
  { icon: LayoutDashboard, label: "Dashboard", href: "#" },
  { icon: MapPin, label: "Peta Gempa", href: "#" },
  { icon: AlertTriangle, label: "Peringatan", href: "#" },
  { icon: BarChart3, label: "Analitik", href: "#" },
  { icon: FileText, label: "Laporan", href: "#" },
  { icon: Zap, label: "Pengaruh", href: "#" },
  { icon: Settings, label: "Kaluar", href: "#" },
]

export default function Sidebar({ open, onToggle }: SidebarProps) {
  return (
    <>
      <div
        className={`${open ? "w-64" : "w-20"} bg-gradient-to-b from-gray-900 to-gray-800 text-white transition-all duration-300 flex flex-col`}
      >
        {/* Logo */}
        <div className="p-4 border-b border-gray-700 flex items-center justify-between">
          {open && <span className="font-bold text-lg">Peta.Gem</span>}
          <button onClick={onToggle} className="p-1 hover:bg-gray-700 rounded transition-smooth">
            <ChevronLeft size={20} className={`transform transition-transform ${!open ? "rotate-180" : ""}`} />
          </button>
        </div>

        {/* Menu Items */}
        <nav className="flex-1 p-4 space-y-2">
          {menuItems.map((item, index) => (
            <a
              key={index}
              href={item.href}
              className="flex items-center gap-4 px-4 py-3 rounded-lg hover:bg-gray-700 transition-smooth group"
            >
              <item.icon size={20} className="flex-shrink-0" />
              {open && <span className="text-sm">{item.label}</span>}
            </a>
          ))}
        </nav>

        {/* Footer */}
        {open && (
          <div className="p-4 border-t border-gray-700 text-xs text-gray-400">
            <p>© 2025 Peta.Gem</p>
            <p>Real-time Monitoring</p>
          </div>
        )}
      </div>
    </>
  )
}
