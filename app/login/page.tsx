"use client"

import type React from "react"
import { useState } from "react"
import { useRouter } from "next/navigation"
import Link from "next/link"
import { ArrowRight, Zap } from "lucide-react"

export default function LoginPage() {
  const router = useRouter()
  const [formData, setFormData] = useState({
    username: "",
    password: "",
  })
  const [error, setError] = useState("")
  const [isLoading, setIsLoading] = useState(false)

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }))
  }

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    setError("")
    setIsLoading(true)

    if (!formData.username || !formData.password) {
      setError("Please fill in all fields")
      setIsLoading(false)
      return
    }

    setTimeout(() => {
      localStorage.setItem("isLoggedIn", "true")
      localStorage.setItem("username", formData.username)
      router.push("/dashboard")
      setIsLoading(false)
    }, 500)
  }

  return (
    <div
      className="min-h-screen flex items-center justify-center relative overflow-hidden"
      style={{
        backgroundImage: `linear-gradient(135deg, rgba(10, 15, 8, 0.85) 0%, rgba(26, 40, 23, 0.8) 100%), url('/mountain-forest-lake-nature-landscape-scenic.jpg')`,
        backgroundSize: "cover",
        backgroundPosition: "center",
        backgroundAttachment: "fixed",
      }}
    >
      <div className="absolute inset-0 bg-gradient-to-br from-secondary/20 via-transparent to-primary/10" />

      <div className="absolute top-10 left-1/4 w-96 h-96 bg-primary/15 rounded-full blur-3xl animate-pulse" />
      <div
        className="absolute bottom-20 right-1/3 w-96 h-96 bg-secondary/15 rounded-full blur-3xl animate-pulse"
        style={{ animationDelay: "1s" }}
      />

      <div className="relative w-full max-w-md mx-4">
        {/* Logo */}
        <div className="flex items-center justify-center mb-12">
          <div className="flex items-center gap-3">
            <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center shadow-lg shadow-primary/50">
              <Zap className="w-6 h-6 text-white" />
            </div>
            <span className="text-2xl font-bold bg-gradient-to-r from-primary to-accent bg-clip-text text-transparent">
              Peta.Gem
            </span>
          </div>
        </div>

        {/* Form Card */}
        <div className="backdrop-blur-xl bg-card/60 rounded-3xl p-8 md:p-10 border border-border/40 shadow-2xl shadow-primary/20">
          <div className="text-center mb-8">
            <h1 className="text-3xl md:text-4xl font-bold text-foreground mb-2">Welcome Back</h1>
            <p className="text-muted-foreground">Monitor earthquakes in real-time</p>
          </div>

          {error && (
            <div className="bg-destructive/15 border border-destructive/50 text-destructive px-4 py-3 rounded-xl mb-6 text-sm">
              {error}
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-5">
            <div>
              <label className="block text-sm font-medium text-foreground mb-2">Username</label>
              <input
                type="text"
                name="username"
                value={formData.username}
                onChange={handleChange}
                placeholder="Enter your username"
                className="w-full px-4 py-3 rounded-xl bg-input border border-border/50 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 transition-smooth text-foreground placeholder:text-muted-foreground"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-foreground mb-2">Password</label>
              <input
                type="password"
                name="password"
                value={formData.password}
                onChange={handleChange}
                placeholder="Enter your password"
                className="w-full px-4 py-3 rounded-xl bg-input border border-border/50 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 transition-smooth text-foreground placeholder:text-muted-foreground"
              />
            </div>

            <button
              type="submit"
              disabled={isLoading}
              className="w-full bg-gradient-to-r from-primary to-secondary text-primary-foreground font-semibold py-3 rounded-xl hover:shadow-lg hover:shadow-primary/40 transition-smooth disabled:opacity-50 flex items-center justify-center gap-2 mt-6"
            >
              {isLoading ? "Logging in..." : "Login"}
              {!isLoading && <ArrowRight size={18} />}
            </button>
          </form>

          <div className="text-center mt-6">
            <p className="text-muted-foreground text-sm">
              Don&apos;t have an account?{" "}
              <Link href="/register" className="text-primary hover:text-accent font-semibold transition-smooth">
                Create one
              </Link>
            </p>
          </div>
        </div>

        {/* Footer */}
        <div className="flex justify-center gap-6 mt-8 text-xs text-muted-foreground hover:text-foreground transition-smooth">
          <a href="#" className="hover:text-accent transition-smooth">
            Privacy Policy
          </a>
          <span>•</span>
          <a href="#" className="hover:text-accent transition-smooth">
            Terms of Service
          </a>
        </div>
      </div>
    </div>
  )
}
