"use client"

import { Bell, LogOut, Settings } from "lucide-react"

interface HeaderProps {
  username: string
  onLogout: () => void
}

export default function Header({ username, onLogout }: HeaderProps) {
  return (
    <header className="border-b border-border bg-card glass-effect backdrop-blur-xl px-6 py-4 flex justify-between items-center sticky top-0 z-40">
      <div className="flex items-center gap-3">
        <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center">
          <span className="text-lg font-bold text-white">📊</span>
        </div>
        <h1 className="text-xl font-bold text-foreground">Peta.Gem</h1>
      </div>

      <div className="flex items-center gap-4">
        <button className="relative text-muted-foreground hover:text-foreground transition-smooth p-2 hover:bg-muted/50 rounded-lg">
          <Bell size={20} />
          <span className="absolute top-1 right-1 w-2 h-2 bg-destructive rounded-full animate-pulse"></span>
        </button>

        <button className="text-muted-foreground hover:text-foreground transition-smooth p-2 hover:bg-muted/50 rounded-lg">
          <Settings size={20} />
        </button>

        <div className="flex items-center gap-3 pl-4 border-l border-border">
          <div className="text-right">
            <p className="text-sm font-medium text-foreground">{username}</p>
            <p className="text-xs text-muted-foreground">Administrator</p>
          </div>
          <div className="w-10 h-10 rounded-lg bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white font-bold">
            {username.charAt(0).toUpperCase()}
          </div>
        </div>

        <button
          onClick={onLogout}
          className="text-muted-foreground hover:text-destructive transition-smooth p-2 hover:bg-destructive/10 rounded-lg"
        >
          <LogOut size={20} />
        </button>
      </div>
    </header>
  )
}
