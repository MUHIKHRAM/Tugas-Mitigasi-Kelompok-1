"use client"

export default function DangerZones() {
  const zones = [
    { name: "Palu & Sekitarnya", status: "Bahaya", percentage: 95 },
    { name: "Donggala", status: "Bahaya", percentage: 87 },
    { name: "Manado", status: "Sedang", percentage: 62 },
    { name: "Toli-toli", status: "Sedang", percentage: 58 },
    { name: "Morowali", status: "Sedang", percentage: 45 },
  ]

  const getStatusColor = (status: string) => {
    if (status === "Bahaya") return "bg-red-500"
    if (status === "Sedang") return "bg-orange-500"
    return "bg-yellow-500"
  }

  const getStatusText = (status: string) => {
    if (status === "Bahaya") return "text-red-600"
    if (status === "Sedang") return "text-orange-600"
    return "text-yellow-600"
  }

  return (
    <div className="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
      <h2 className="text-lg font-semibold text-gray-800 mb-6">Zona Rawan Bencana</h2>

      <div className="space-y-4">
        {zones.map((zone, index) => (
          <div key={index}>
            <div className="flex justify-between items-center mb-2">
              <span className="text-sm font-medium text-gray-700">{zone.name}</span>
              <span
                className={`text-xs font-semibold px-3 py-1 rounded-full ${getStatusText(zone.status)} ${getStatusColor(zone.status)}`}
              >
                {zone.status}
              </span>
            </div>
            <div className="w-full bg-gray-200 rounded-full h-2">
              <div
                className={`h-2 rounded-full transition-all ${getStatusColor(zone.status)}`}
                style={{ width: `${zone.percentage}%` }}
              ></div>
            </div>
            <p className="text-xs text-gray-500 mt-1">{zone.percentage}% dari kapasitas</p>
          </div>
        ))}
      </div>
    </div>
  )
}
