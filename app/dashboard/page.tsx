"use client"

import { useEffect, useState } from "react"
import { useRouter } from "next/navigation"
import Header from "@/components/dashboard/header"
import Sidebar from "@/components/dashboard/sidebar"
import StatsGrid from "@/components/dashboard/stats-grid"
import MapView from "@/components/dashboard/map-view"
import DangerZones from "@/components/dashboard/danger-zones"
import Statistics from "@/components/dashboard/statistics"
import RecentEarthquakes from "@/components/dashboard/recent-earthquakes"

export default function DashboardPage() {
  const router = useRouter()
  const [isClient, setIsClient] = useState(false)
  const [username, setUsername] = useState("")
  const [sidebarOpen, setSidebarOpen] = useState(true)

  useEffect(() => {
    setIsClient(true)
    const isLoggedIn = localStorage.getItem("isLoggedIn")
    const storedUsername = localStorage.getItem("username")

    if (!isLoggedIn) {
      router.push("/login")
    } else {
      setUsername(storedUsername || "User")
    }
  }, [router])

  if (!isClient) return null

  return (
    <div className="flex h-screen bg-background">
      <Sidebar open={sidebarOpen} onToggle={() => setSidebarOpen(!sidebarOpen)} />

      <div className="flex-1 flex flex-col overflow-hidden">
        <Header
          username={username}
          onLogout={() => {
            localStorage.removeItem("isLoggedIn")
            localStorage.removeItem("username")
            router.push("/login")
          }}
        />

        <main className="flex-1 overflow-auto">
          <div className="p-6 space-y-6">
            <div>
              <h1 className="text-3xl font-bold text-foreground mb-2">Monitoring Gempa Sulawesi Tengah</h1>
              <p className="text-muted-foreground">Provinsi dengan Aktivitas Seismik Tinggi</p>
            </div>

            <StatsGrid />

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
              <div className="lg:col-span-2">
                <MapView />
              </div>
              <div>
                <RecentEarthquakes />
              </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <DangerZones />
              <Statistics />
            </div>
          </div>
        </main>
      </div>
    </div>
  )
}
