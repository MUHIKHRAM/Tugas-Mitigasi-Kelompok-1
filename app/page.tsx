"use client"

import { useRouter } from "next/navigation"
import { useEffect, useState } from "react"
import Link from "next/link"

export default function Home() {
  const router = useRouter()
  const [isClient, setIsClient] = useState(false)

  useEffect(() => {
    setIsClient(true)
    const isLoggedIn = localStorage.getItem("isLoggedIn")
    if (isLoggedIn) {
      router.push("/dashboard")
    } else {
      router.push("/login")
    }
  }, [router])

  if (!isClient) return null

  return (
    <div className="auth-background flex items-center justify-center min-h-screen">
      <div className="bg-white/80 backdrop-blur-sm rounded-3xl shadow-2xl p-12 max-w-md w-full mx-4">
        <div className="text-center mb-8">
          <h1 className="text-4xl font-bold text-green-700 mb-2">Peta.Gem</h1>
          <p className="text-gray-600 text-lg">Earthquake Monitoring System</p>
        </div>

        <div className="space-y-4">
          <Link href="/login">
            <button className="w-full bg-green-600 text-white py-3 rounded-full font-semibold hover:bg-green-700 transition-smooth">
              Login
            </button>
          </Link>

          <Link href="/register">
            <button className="w-full border-2 border-green-600 text-green-600 py-3 rounded-full font-semibold hover:bg-green-50 transition-smooth">
              Register
            </button>
          </Link>
        </div>

        <p className="text-center text-gray-600 mt-8 text-sm">
          Monitor real-time earthquake data and disaster alerts for Sulawesi Tengah
        </p>
      </div>
    </div>
  )
}
