"use client"

import { TrendingUp, AlertCircle, MapPin, Target } from "lucide-react"

export default function StatsGrid() {
  const stats = [
    {
      icon: AlertCircle,
      label: "Gempa Hari Ini",
      value: "8",
      detail: "+2 dari kemarin",
      color: "text-orange-500",
    },
    {
      icon: TrendingUp,
      label: "Magnitudo Tertinggi",
      value: "5.8",
      detail: "Palu",
      color: "text-red-500",
    },
    {
      icon: MapPin,
      label: "Lokasi Aktif",
      value: "12",
      detail: "dari 13 kota",
      color: "text-blue-500",
    },
    {
      icon: Target,
      label: "Rata-rata Magnitudo",
      value: "4.3",
      detail: "Stabil",
      color: "text-green-500",
    },
  ]

  return (
    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      {stats.map((stat, index) => (
        <div
          key={index}
          className="bg-white rounded-lg p-6 shadow-sm border border-gray-100 hover:shadow-md transition-smooth"
        >
          <div className="flex items-start justify-between">
            <div>
              <p className="text-gray-600 text-sm font-medium">{stat.label}</p>
              <p className="text-3xl font-bold text-gray-800 mt-2">{stat.value}</p>
              <p className="text-gray-500 text-xs mt-2">{stat.detail}</p>
            </div>
            <div className={`p-3 bg-gray-100 rounded-lg ${stat.color}`}>
              <stat.icon size={24} />
            </div>
          </div>
        </div>
      ))}
    </div>
  )
}
