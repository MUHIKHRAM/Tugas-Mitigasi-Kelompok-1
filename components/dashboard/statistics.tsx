"use client"

export default function Statistics() {
  const stats = [
    { label: "Total Gempa", value: "287", color: "text-red-500" },
    { label: "Magnitude ≥ 5.0", value: "31", color: "text-orange-500" },
    { label: "Potensi Tsunami", value: "12", color: "text-red-600" },
  ]

  return (
    <div className="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
      <h2 className="text-lg font-semibold text-gray-800 mb-6">Statistik Bulan Sulawesi Tengah</h2>

      <div className="space-y-6">
        {stats.map((stat, index) => (
          <div key={index}>
            <div className="flex justify-between items-end mb-2">
              <span className="text-sm text-gray-600">{stat.label}</span>
              <span className={`text-2xl font-bold ${stat.color}`}>{stat.value}</span>
            </div>
            <div className="w-full bg-gray-200 rounded-full h-3">
              <div
                className={`h-3 rounded-full transition-all ${stat.color}`}
                style={{ width: `${Math.random() * 100}%` }}
              ></div>
            </div>
          </div>
        ))}
      </div>
    </div>
  )
}
