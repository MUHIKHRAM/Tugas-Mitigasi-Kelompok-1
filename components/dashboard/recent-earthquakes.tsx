"use client"

import { Clock } from "lucide-react"

export default function RecentEarthquakes() {
  const earthquakes = [
    { id: 1, location: "Palu", time: "06:32 WIB", distance: "Kedalaman: 32 km", magnitude: 5.8 },
    { id: 2, location: "Donggala", time: "05:16 WIB", distance: "Kedalaman: 26 km", magnitude: 5.2 },
    { id: 3, location: "Manado", time: "04:47 WIB", distance: "Kedalaman: 28 km", magnitude: 4.0 },
    { id: 4, location: "Toli-toli", time: "03:22 WIB", distance: "Kedalaman: 22 km", magnitude: 4.5 },
    { id: 5, location: "Morowali", time: "09:50 WIB", distance: "Kedalaman: 35 km", magnitude: 4.2 },
  ]

  const getMagnitudeColor = (magnitude: number) => {
    if (magnitude >= 5.5) return "bg-red-100 text-red-700"
    if (magnitude >= 5.0) return "bg-orange-100 text-orange-700"
    if (magnitude >= 4.5) return "bg-yellow-100 text-yellow-700"
    return "bg-green-100 text-green-700"
  }

  return (
    <div className="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
      <h2 className="text-lg font-semibold text-gray-800 mb-4">Gempa Terbaru Sulawesi Tengah</h2>

      <div className="space-y-3 max-h-96 overflow-y-auto">
        {earthquakes.map((eq) => (
          <div
            key={eq.id}
            className="flex items-start gap-3 p-3 hover:bg-gray-50 rounded-lg transition-smooth border border-gray-100"
          >
            <div
              className={`flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm ${getMagnitudeColor(eq.magnitude)}`}
            >
              {eq.magnitude}
            </div>
            <div className="flex-1 min-w-0">
              <p className="font-medium text-gray-800 text-sm">{eq.location}</p>
              <p className="text-xs text-gray-500 mt-1 flex items-center gap-1">
                <Clock size={12} />
                {eq.time}
              </p>
              <p className="text-xs text-gray-500">{eq.distance}</p>
            </div>
          </div>
        ))}
      </div>
    </div>
  )
}
