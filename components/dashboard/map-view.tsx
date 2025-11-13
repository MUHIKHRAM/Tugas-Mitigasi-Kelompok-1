"use client"

import { useState } from "react"

export default function MapView() {
  const [earthquakes] = useState([
    { id: 1, lat: 0.5, lng: 120.0, magnitude: 5.8, name: "Palu", region: "Sulawesi Tengah" },
    { id: 2, lat: -1.2, lng: 121.5, magnitude: 5.2, name: "Donggala", region: "Sulawesi Tengah" },
    { id: 3, lat: 1.5, lng: 124.8, magnitude: 4.0, name: "Manado", region: "Sulawesi Utara" },
    { id: 4, lat: -0.8, lng: 119.7, magnitude: 4.5, name: "Toli-toli", region: "Sulawesi Tengah" },
    { id: 5, lat: 0.2, lng: 123.5, magnitude: 4.2, name: "Morowali", region: "Sulawesi Tengah" },
  ])

  const [hoveredEq, setHoveredEq] = useState<number | null>(null)

  const getMagnitudeColor = (magnitude: number) => {
    if (magnitude >= 5.5) return "#ef4444"
    if (magnitude >= 5.0) return "#f97316"
    if (magnitude >= 4.5) return "#eab308"
    return "#84cc16"
  }

  return (
    <div className="bg-slate-900 rounded-xl shadow-lg border border-slate-700 overflow-hidden">
      <div className="p-6 border-b border-slate-700 bg-gradient-to-r from-slate-900 to-slate-800">
        <h2 className="text-xl font-bold text-white mb-4">Peta Seismik Sulawesi</h2>
        <div className="flex flex-wrap gap-4">
          <div className="flex items-center gap-2">
            <div className="w-4 h-4 rounded-full" style={{ backgroundColor: "#ef4444" }}></div>
            <span className="text-sm text-slate-300">&ge; 5.5 Parah</span>
          </div>
          <div className="flex items-center gap-2">
            <div className="w-4 h-4 rounded-full" style={{ backgroundColor: "#f97316" }}></div>
            <span className="text-sm text-slate-300">5.0 - 5.4 Sedang</span>
          </div>
          <div className="flex items-center gap-2">
            <div className="w-4 h-4 rounded-full" style={{ backgroundColor: "#eab308" }}></div>
            <span className="text-sm text-slate-300">4.5 - 4.9 Menengah</span>
          </div>
          <div className="flex items-center gap-2">
            <div className="w-4 h-4 rounded-full" style={{ backgroundColor: "#84cc16" }}></div>
            <span className="text-sm text-slate-300">&lt; 4.5 Ringan</span>
          </div>
        </div>
      </div>

      {/* Interactive Sulawesi Map */}
      <div className="relative h-96 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800 overflow-hidden">
        <svg className="w-full h-full" viewBox="0 0 500 600" preserveAspectRatio="xMidYMid meet">
          {/* Ocean background */}
          <defs>
            <linearGradient id="oceanGradient" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" style={{ stopColor: "#1e3a5f", stopOpacity: 1 }} />
              <stop offset="100%" style={{ stopColor: "#0f2744", stopOpacity: 1 }} />
            </linearGradient>
            <filter id="glow">
              <feGaussianBlur stdDeviation="3" result="coloredBlur" />
              <feMerge>
                <feMergeNode in="coloredBlur" />
                <feMergeNode in="SourceGraphic" />
              </feMerge>
            </filter>
          </defs>
          <rect width="500" height="600" fill="url(#oceanGradient)" />

          {/* Simplified Sulawesi Island Shapes */}
          {/* Northern Arm */}
          <path
            d="M 250 80 Q 270 100 280 150 Q 285 180 280 220 Q 270 250 250 260"
            fill="#2d5016"
            stroke="#4a7c59"
            strokeWidth="2"
            opacity="0.8"
          />

          {/* Central Body */}
          <ellipse cx="250" cy="300" rx="35" ry="80" fill="#3d6b1f" stroke="#5a8c4c" strokeWidth="2" opacity="0.85" />

          {/* Southern Arm */}
          <path
            d="M 250 380 Q 260 410 270 450 Q 275 490 270 540"
            fill="#2d5016"
            stroke="#4a7c59"
            strokeWidth="2"
            opacity="0.8"
          />

          {/* Eastern Peninsula */}
          <path
            d="M 285 200 Q 320 210 340 240 Q 350 260 345 300"
            fill="#2d5016"
            stroke="#4a7c59"
            strokeWidth="2"
            opacity="0.75"
          />

          {/* Western Peninsula */}
          <path
            d="M 215 250 Q 190 260 180 290 Q 175 320 185 340"
            fill="#2d5016"
            stroke="#4a7c59"
            strokeWidth="2"
            opacity="0.75"
          />

          {/* Sulawesi Tengah highlight */}
          <circle
            cx="250"
            cy="280"
            r="50"
            fill="none"
            stroke="#7cc576"
            strokeWidth="1.5"
            strokeDasharray="5,5"
            opacity="0.5"
          />
          <text x="250" y="360" fill="#7cc576" fontSize="12" fontWeight="bold" textAnchor="middle" opacity="0.7">
            SULAWESI TENGAH
          </text>

          {/* Earthquake markers */}
          {earthquakes.map((eq) => {
            // Map coordinates to SVG space
            // Longitude: 119-125 maps to 150-350
            // Latitude: -2 to 2 maps to 450-100
            const x = ((eq.lng - 119) / 6) * 200 + 150
            const y = ((2 - eq.lat) / 4) * 350 + 100

            const size = 6 + (eq.magnitude - 4) * 2
            const color = getMagnitudeColor(eq.magnitude)
            const isHovered = hoveredEq === eq.id

            return (
              <g key={eq.id} className="cursor-pointer">
                {/* Pulse ring */}
                <circle cx={x} cy={y} r={size + 12} fill={color} opacity="0.15" className="animate-pulse" />

                {/* Glow ring */}
                <circle
                  cx={x}
                  cy={y}
                  r={size + 6}
                  fill={color}
                  opacity={isHovered ? 0.4 : 0.2}
                  className="transition-opacity"
                />

                {/* Main marker */}
                <circle
                  cx={x}
                  cy={y}
                  r={size}
                  fill={color}
                  stroke="white"
                  strokeWidth="1.5"
                  opacity={isHovered ? 1 : 0.85}
                  className="hover:opacity-100 transition-opacity"
                  onMouseEnter={() => setHoveredEq(eq.id)}
                  onMouseLeave={() => setHoveredEq(null)}
                  filter="url(#glow)"
                />

                {/* Magnitude label */}
                {isHovered && (
                  <>
                    <text x={x} y={y + 1} fill="white" fontSize="11" fontWeight="bold" textAnchor="middle">
                      {eq.magnitude}
                    </text>
                    {/* Tooltip */}
                    <rect x={x - 50} y={y - 35} width="100" height="30" rx="4" fill="#000" opacity="0.8" />
                    <text x={x} y={y - 22} fill="#fff" fontSize="11" fontWeight="bold" textAnchor="middle">
                      {eq.name}
                    </text>
                    <text x={x} y={y - 12} fill="#cbd5e1" fontSize="9" textAnchor="middle">
                      M {eq.magnitude}
                    </text>
                  </>
                )}
              </g>
            )
          })}

          {/* Grid lines for reference */}
          <g stroke="#4a7c59" strokeWidth="0.5" opacity="0.2">
            <line x1="150" y1="100" x2="150" y2="550" />
            <line x1="250" y1="100" x2="250" y2="550" />
            <line x1="350" y1="100" x2="350" y2="550" />
            <line x1="100" y1="300" x2="400" y2="300" />
          </g>
        </svg>

        {/* Map Controls */}
        <div className="absolute bottom-4 right-4 flex flex-col gap-2">
          <button className="bg-green-600 hover:bg-green-700 text-white p-2 rounded-lg transition-colors shadow-lg">
            <span className="text-lg">+</span>
          </button>
          <button className="bg-green-600 hover:bg-green-700 text-white p-2 rounded-lg transition-colors shadow-lg">
            <span className="text-lg">−</span>
          </button>
        </div>

        {/* Legend */}
        <div className="absolute top-4 left-4 bg-black/60 backdrop-blur-sm p-3 rounded-lg border border-green-600/30">
          <p className="text-xs text-green-400 font-semibold">Keterangan:</p>
          <p className="text-xs text-slate-300 mt-1">Titik = Lokasi Gempa</p>
          <p className="text-xs text-slate-300">Warna = Magnitude</p>
        </div>
      </div>

      {/* Quick Info */}
      <div className="p-4 bg-slate-800/50 border-t border-slate-700">
        <div className="text-sm text-slate-300">
          <p>
            Total Gempa Terdeteksi: <span className="font-bold text-green-400">{earthquakes.length}</span>
          </p>
          <p>
            Status: <span className="font-bold text-yellow-400">Aktif Monitoring</span>
          </p>
        </div>
      </div>
    </div>
  )
}
