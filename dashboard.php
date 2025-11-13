<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$current_page = $_GET['page'] ?? 'dashboard';
$username = $_SESSION['username'] ?? 'User';
$db = getDB();

// Get earthquake data
$earthquakes = $GLOBALS['mock_earthquakes'] ?? [];

// Calculate stats
$latest_magnitude = $earthquakes[0]['magnitude'] ?? 0;
$danger_count = count(array_filter($earthquakes, fn($e) => $e['magnitude'] >= 5.0));
$avg_magnitude = count($earthquakes) > 0 ? array_sum(array_column($earthquakes, 'magnitude')) / count($earthquakes) : 0;

// Regional data for Sulawesi Tengah
$regions = [
    ['name' => 'Palu', 'lat' => -0.9, 'lng' => 119.8, 'risk' => 'Tinggi', 'color' => '#ef4444'],
    ['name' => 'Donggala', 'lat' => -0.65, 'lng' => 119.8, 'risk' => 'Tinggi', 'color' => '#f97316'],
    ['name' => 'Buol', 'lat' => 0.65, 'lng' => 120.8, 'risk' => 'Sedang', 'color' => '#eab308'],
    ['name' => 'Toli-Toli', 'lat' => 0.9, 'lng' => 120.5, 'risk' => 'Sedang', 'color' => '#eab308'],
    ['name' => 'Morowali', 'lat' => -1.2, 'lng' => 121.5, 'risk' => 'Rendah', 'color' => '#22c55e'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta.Gem - <?php echo ucfirst($current_page); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Geist', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #1a2817;
            color: #f5f5f5;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar - Army Green & Brown Theme */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #2d4a2b 0%, #1f3622 100%);
            border-right: 2px solid #5a8c4c;
            padding: 24px;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
            transition: all 0.3s ease;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3);
        }
        
        /* Hamburger menu for mobile */
        .hamburger {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 101;
            background: none;
            border: none;
            color: #d4a574;
            font-size: 24px;
            cursor: pointer;
        }
        
        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #5a8c4c;
        }
        
        .sidebar-header h2 {
            font-size: 24px;
            font-weight: 700;
            color: #d4a574;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu li {
            margin-bottom: 8px;
        }
        
        /* Interactive menu links with green-brown theme */
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #b0b8a8;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }
        
        .sidebar-menu a:hover {
            background: rgba(212, 165, 116, 0.1);
            color: #d4a574;
            transform: translateX(4px);
        }
        
        .sidebar-menu a.active {
            background: rgba(90, 140, 76, 0.3);
            color: #d4a574;
            border-left: 3px solid #d4a574;
            padding-left: 13px;
            font-weight: 600;
            box-shadow: inset 0 0 10px rgba(90, 140, 76, 0.2);
        }
        
        /* Menu icon styling */
        .menu-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }
        
        .sidebar-footer {
            position: absolute;
            bottom: 24px;
            left: 24px;
            right: 24px;
        }
        
        .logout-btn {
            width: 100%;
            padding: 10px;
            background: rgba(212, 165, 116, 0.15);
            color: #d4a574;
            border: 1px solid rgba(212, 165, 116, 0.3);
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .logout-btn:hover {
            background: rgba(212, 165, 116, 0.25);
            color: #e8c08c;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            flex: 1;
            padding: 32px;
            transition: all 0.3s ease;
        }
        
        /* Page content sections (hidden by default) */
        .page-content {
            display: none;
        }
        
        .page-content.active {
            display: block;
        }
        
        /* Header */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding-bottom: 24px;
            border-bottom: 2px solid #5a8c4c;
        }
        
        .dashboard-header h1 {
            font-size: 32px;
            font-weight: 700;
            color: #d4a574;
        }
        
        .user-info {
            text-align: right;
        }
        
        .user-info p {
            color: #b0b8a8;
            font-size: 14px;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        /* Stat cards with green-brown theme */
        .stat-card {
            background: linear-gradient(135deg, #2d4a2b 0%, #3a5c36 100%);
            border: 2px solid #5a8c4c;
            border-radius: 12px;
            padding: 24px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        
        .stat-card:hover {
            border-color: #d4a574;
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(212, 165, 116, 0.15);
        }
        
        .stat-label {
            color: #b0b8a8;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        
        .stat-value {
            font-size: 36px;
            font-weight: 700;
            color: #d4a574;
            margin-bottom: 12px;
        }
        
        .stat-unit {
            color: #b0b8a8;
            font-size: 12px;
        }
        
        /* Map Container */
        .map-container {
            background: linear-gradient(135deg, #2d4a2b 0%, #3a5c36 100%);
            border: 2px solid #5a8c4c;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 40px;
            min-height: 650px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        
        .map-container h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #d4a574;
        }
        
        .map-wrapper {
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }
        
        .map-main {
            flex: 1;
            background: linear-gradient(135deg, #1a2817 0%, #2d4a2b 100%);
            border: 2px solid #5a8c4c;
            border-radius: 12px;
            padding: 20px;
            position: relative;
            overflow: hidden;
            box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.3);
        }
        
        .map-legend {
            width: 200px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
            padding: 16px;
            font-size: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .map-legend h4 {
            color: #1a2817;
            margin-bottom: 12px;
            font-size: 13px;
            font-weight: 600;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            color: #333;
        }
        
        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 3px;
            border: 1px solid #ccc;
        }
        
        svg {
            width: 100%;
            height: 100%;
            max-width: 100%;
        }
        
        .region-path {
            transition: all 0.3s ease;
            cursor: pointer;
            filter: drop-shadow(0 0 0 rgba(0,0,0,0));
        }
        
        .region-path:hover {
            filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.3));
            opacity: 0.8;
        }
        
        .region-label {
            cursor: pointer;
            user-select: none;
            transition: all 0.3s ease;
        }
        
        .region-label:hover {
            font-weight: bold;
        }
        
        .tooltip {
            position: absolute;
            background: rgba(0, 0, 0, 0.9);
            color: #d4a574;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 11px;
            pointer-events: none;
            z-index: 1000;
            display: none;
            white-space: nowrap;
            border: 1px solid #d4a574;
        }
        
        /* Earthquakes List */
        .earthquakes-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }
        
        /* Earthquake list styling with green-brown theme */
        .earthquakes-list {
            background: linear-gradient(135deg, #2d4a2b 0%, #3a5c36 100%);
            border: 2px solid #5a8c4c;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        
        .earthquakes-list h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #d4a574;
        }
        
        .earthquake-item {
            padding: 16px;
            background: rgba(90, 140, 76, 0.15);
            border-left: 3px solid #d4a574;
            border-radius: 8px;
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }
        
        .earthquake-item:hover {
            background: rgba(212, 165, 116, 0.15);
            border-left-color: #d4a574;
        }
        
        .earthquake-location {
            font-weight: 600;
            color: #f5f5f5;
            margin-bottom: 4px;
        }
        
        .earthquake-magnitude {
            display: inline-block;
            background: #5a8c4c;
            color: #f5f5f5;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            margin-right: 8px;
        }
        
        .earthquake-depth {
            color: #b0b8a8;
            font-size: 12px;
        }
        
        /* Danger Zones */
        .danger-zones {
            background: linear-gradient(135deg, #2d4a2b 0%, #3a5c36 100%);
            border: 2px solid #5a8c4c;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        
        .danger-zones h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #d4a574;
        }
        
        .danger-zone-item {
            margin-bottom: 16px;
        }
        
        .zone-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 13px;
        }
        
        .zone-label span:first-child {
            color: #f5f5f5;
            font-weight: 500;
        }
        
        .zone-label span:last-child {
            color: #b0b8a8;
        }
        
        .zone-bar {
            height: 8px;
            background: rgba(90, 140, 76, 0.2);
            border-radius: 4px;
            overflow: hidden;
        }
        
        .zone-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #5a8c4c 0%, #d4a574 100%);
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        
        /* Empty state for other pages */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 400px;
            text-align: center;
            color: #b0b8a8;
        }
        
        .empty-state h2 {
            font-size: 28px;
            color: #d4a574;
            margin-bottom: 12px;
        }
        
        .empty-state p {
            font-size: 14px;
            margin-bottom: 24px;
        }
        
        .empty-state-icon {
            font-size: 60px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        @media (max-width: 1024px) {
            .earthquakes-section {
                grid-template-columns: 1fr;
            }
            
            .map-wrapper {
                flex-direction: column;
            }
            
            .map-legend {
                width: 100%;
            }
            
            .sidebar {
                width: 200px;
            }
            
            .main-content {
                margin-left: 200px;
                padding: 20px;
            }
        }
        
        /* Mobile responsive hamburger menu */
        @media (max-width: 768px) {
            .hamburger {
                display: block;
            }
            
            .sidebar {
                position: fixed;
                left: -280px;
                width: 280px;
                height: 100%;
                transition: left 0.3s ease;
                z-index: 1000;
            }
            
            .sidebar.active {
                left: 0;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .earthquakes-section {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <button class="hamburger" id="hamburger">☰</button>
    
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2>Peta.Gem</h2>
            </div>
            
            <!-- Interactive menu with page navigation -->
            <ul class="sidebar-menu">
                <li><a href="?page=dashboard" class="menu-link <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>" data-page="dashboard"><span class="menu-icon">📊</span> Dashboard</a></li>
                <li><a href="?page=monitoring" class="menu-link <?php echo $current_page === 'monitoring' ? 'active' : ''; ?>" data-page="monitoring"><span class="menu-icon">📡</span> Monitoring</a></li>
                <li><a href="?page=analytics" class="menu-link <?php echo $current_page === 'analytics' ? 'active' : ''; ?>" data-page="analytics"><span class="menu-icon">📈</span> Analytics</a></li>
                <li><a href="?page=reports" class="menu-link <?php echo $current_page === 'reports' ? 'active' : ''; ?>" data-page="reports"><span class="menu-icon">📄</span> Reports</a></li>
                <li><a href="?page=settings" class="menu-link <?php echo $current_page === 'settings' ? 'active' : ''; ?>" data-page="settings"><span class="menu-icon">⚙️</span> Settings</a></li>
            </ul>
            
            <div class="sidebar-footer">
                <form method="POST" action="">
                    <button type="submit" name="logout" class="logout-btn">🚪 Logout</button>
                </form>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Dashboard Page -->
            <div id="dashboard" class="page-content <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
                <div class="dashboard-header">
                    <div>
                        <h1>Monitoring Gempa Sulawesi Tengah</h1>
                        <p style="color: #b0b8a8; margin-top: 4px;">Provincial dengan Aktivitas Seismik Tinggi</p>
                    </div>
                    <div class="user-info">
                        <p>Welcome,</p>
                        <p style="font-weight: 600; font-size: 16px;"><?php echo htmlspecialchars($username); ?></p>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-label">Gempa Hari Ini</div>
                        <div class="stat-value"><?php echo count($earthquakes); ?></div>
                        <div class="stat-unit">kejadian</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Magnitude Terbaru</div>
                        <div class="stat-value"><?php echo number_format($latest_magnitude, 1); ?></div>
                        <div class="stat-unit">Skala</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Zona Bahaya</div>
                        <div class="stat-value"><?php echo $danger_count; ?></div>
                        <div class="stat-unit">lokasi berbahaya</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Rata-rata Magnitude</div>
                        <div class="stat-value"><?php echo number_format($avg_magnitude, 1); ?></div>
                        <div class="stat-unit">Skala</div>
                    </div>
                </div>
                
                <!-- Map Container -->
                <div class="map-container">
                    <h3>Peta Administrasi Sulawesi Tengah dengan Sebaran Gempa</h3>
                    
                    <div class="map-wrapper">
                        <div class="map-main" id="mapContainer">
                            <svg viewBox="0 0 1200 700" preserveAspectRatio="xMidYMid meet">
                                <!-- Ocean background -->
                                <defs>
                                    <pattern id="water" patternUnits="userSpaceOnUse" width="100" height="100">
                                        <rect fill="rgba(26, 100, 200, 0.1)"/>
                                    </pattern>
                                </defs>
                                
                                <!-- Ocean -->
                                <rect width="1200" height="700" fill="none"/>
                                
                                <!-- Ocean labels -->
                                <text x="300" y="100" font-size="14" fill="rgba(255,255,255,0.5)" font-style="italic">LAUT MAKASSAR</text>
                                <text x="850" y="200" font-size="14" fill="rgba(255,255,255,0.5)" font-style="italic">LAUT SULAWESI</text>
                                <text x="550" y="400" font-size="14" fill="rgba(255,255,255,0.5)" font-style="italic">TELUK TOMINI</text>
                                <text x="950" y="500" font-size="14" fill="rgba(255,255,255,0.5)" font-style="italic">LAUT MALUKU</text>
                                
                                <!-- Sulawesi regions -->
                                <path d="M 550 150 L 580 180 L 600 200 L 610 250 L 605 300 L 590 280 L 570 260 L 555 220 Z" 
                                      fill="#d4a574" stroke="#8B7355" stroke-width="2" class="region-path" data-region="Manado" data-risk="Sedang"/>
                                
                                <path d="M 500 200 L 550 250 L 560 320 L 555 380 L 540 420 L 520 400 L 505 350 L 495 280 Z" 
                                      fill="#a89968" stroke="#8B7355" stroke-width="2" class="region-path" data-region="Palu" data-risk="Tinggi"/>
                                
                                <path d="M 650 280 L 720 320 L 750 380 L 740 450 L 700 480 L 650 450 L 640 380 Z" 
                                      fill="#b8a584" stroke="#8B7355" stroke-width="2" class="region-path" data-region="Morowali" data-risk="Rendah"/>
                                
                                <path d="M 520 420 L 560 460 L 580 520 L 570 580 L 540 590 L 510 550 L 500 480 Z" 
                                      fill="#c9b896" stroke="#8B7355" stroke-width="2" class="region-path" data-region="Malili" data-risk="Rendah"/>
                                
                                <path d="M 420 250 L 480 280 L 500 350 L 470 380 L 430 360 L 410 300 Z" 
                                      fill="#d9c8a3" stroke="#8B7355" stroke-width="2" class="region-path" data-region="Toli-Toli" data-risk="Sedang"/>
                                
                                <!-- Earthquake markers -->
                                <?php foreach ($earthquakes as $idx => $eq): ?>
                                    <?php 
                                    $x = 550 + ($eq['longitude'] - 120) * 40;
                                    $y = 350 + ($eq['latitude'] + 1.5) * 60;
                                    $magnitude = $eq['magnitude'];
                                    
                                    if ($magnitude >= 5.5) {
                                        $color = '#ef4444';
                                        $size = 12;
                                    } elseif ($magnitude >= 5.0) {
                                        $color = '#f97316';
                                        $size = 10;
                                    } else {
                                        $color = '#eab308';
                                        $size = 8;
                                    }
                                    ?>
                                    <g class="earthquake-marker" onmouseover="showTooltip(event, '<?php echo htmlspecialchars($eq['location']); ?> - <?php echo $magnitude; ?> SR')" 
                                       onmouseout="hideTooltip()">
                                        <circle cx="<?php echo $x; ?>" cy="<?php echo $y; ?>" r="<?php echo $size * 1.5; ?>" 
                                                fill="<?php echo $color; ?>" opacity="0.25"/>
                                        <circle cx="<?php echo $x; ?>" cy="<?php echo $y; ?>" r="<?php echo $size; ?>" 
                                                fill="<?php echo $color; ?>" opacity="0.9" style="cursor: pointer;"/>
                                    </g>
                                <?php endforeach; ?>
                                
                                <!-- Region labels -->
                                <text x="580" y="180" font-size="11" fill="#1a1a1a" font-weight="600" text-anchor="middle" class="region-label">MANADO</text>
                                <text x="520" y="320" font-size="11" fill="#1a1a1a" font-weight="600" text-anchor="middle" class="region-label">PALU</text>
                                <text x="700" y="380" font-size="11" fill="#1a1a1a" font-weight="600" text-anchor="middle" class="region-label">MOROWALI</text>
                                <text x="450" y="320" font-size="11" fill="#1a1a1a" font-weight="600" text-anchor="middle" class="region-label">TOLI-TOLI</text>
                                <text x="540" y="520" font-size="11" fill="#1a1a1a" font-weight="600" text-anchor="middle" class="region-label">MALILI</text>
                            </svg>
                        </div>
                        
                        <div class="map-legend">
                            <h4>LEGENDA</h4>
                            
                            <div style="margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #ddd;">
                                <div style="font-weight: 600; margin-bottom: 8px; color: #000;">Skala Gempa:</div>
                                <div class="legend-item">
                                    <div class="legend-color" style="background: #ef4444;"></div>
                                    <span>≥ 5.5 (Parah)</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color" style="background: #f97316;"></div>
                                    <span>5.0 - 5.4 (Sedang)</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color" style="background: #eab308;"></div>
                                    <span>&lt; 5.0 (Ringan)</span>
                                </div>
                            </div>
                            
                            <div style="margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #ddd;">
                                <div style="font-weight: 600; margin-bottom: 8px; color: #000;">Tingkat Risiko:</div>
                                <div class="legend-item">
                                    <span style="color: #ef4444; font-weight: 600;">●</span>
                                    <span>Tinggi</span>
                                </div>
                                <div class="legend-item">
                                    <span style="color: #f97316; font-weight: 600;">●</span>
                                    <span>Sedang</span>
                                </div>
                                <div class="legend-item">
                                    <span style="color: #22c55e; font-weight: 600;">●</span>
                                    <span>Rendah</span>
                                </div>
                            </div>
                            
                            <div style="font-size: 11px; color: #666;">
                                Hover pada wilayah untuk melihat detail
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Earthquakes List & Danger Zones -->
                <div class="earthquakes-section">
                    <div class="earthquakes-list">
                        <h3>Gempa Terbaru Sulawesi Tengah</h3>
                        <?php foreach (array_slice($earthquakes, 0, 5) as $eq): ?>
                            <div class="earthquake-item">
                                <div class="earthquake-location"><?php echo htmlspecialchars($eq['location']); ?></div>
                                <div>
                                    <span class="earthquake-magnitude"><?php echo number_format($eq['magnitude'], 1); ?> SR</span>
                                    <span class="earthquake-depth">Kedalaman: <?php echo $eq['depth']; ?> km</span>
                                </div>
                                <div class="earthquake-depth" style="margin-top: 4px;"><?php echo date('d M H:i', strtotime($eq['timestamp'])); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="danger-zones">
                        <h3>Zona Rawan Bencana</h3>
                        <?php foreach ($regions as $region): ?>
                            <div class="danger-zone-item">
                                <div class="zone-label">
                                    <span><?php echo $region['name']; ?></span>
                                    <span><?php echo $region['risk']; ?></span>
                                </div>
                                <div class="zone-bar">
                                    <div class="zone-bar-fill" style="width: <?php echo $region['risk'] === 'Tinggi' ? '90' : ($region['risk'] === 'Sedang' ? '60' : '30'); ?>%;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Monitoring Page -->
            <div id="monitoring" class="page-content <?php echo $current_page === 'monitoring' ? 'active' : ''; ?>">
                <div class="dashboard-header">
                    <div>
                        <h1>Sistem Monitoring Real-time</h1>
                        <p style="color: #b0b8a8; margin-top: 4px;">Pantau aktivitas seismik secara real-time</p>
                    </div>
                </div>
                <div class="empty-state">
                    <div class="empty-state-icon">📡</div>
                    <h2>Monitoring Real-time</h2>
                    <p>Fitur monitoring real-time sedang dalam pengembangan. Sistem akan menampilkan data seismik langsung dari sensor-sensor di Sulawesi Tengah.</p>
                </div>
            </div>
            
            <!-- Analytics Page -->
            <div id="analytics" class="page-content <?php echo $current_page === 'analytics' ? 'active' : ''; ?>">
                <div class="dashboard-header">
                    <div>
                        <h1>Analisis Data Gempa</h1>
                        <p style="color: #b0b8a8; margin-top: 4px;">Pola dan tren aktivitas seismik</p>
                    </div>
                </div>
                <div class="empty-state">
                    <div class="empty-state-icon">📈</div>
                    <h2>Analisis & Statistik</h2>
                    <p>Tampilan analitik mendalam dengan grafik tren magnitude, frekuensi gempa, dan prediksi aktivitas seismik akan ditampilkan di sini.</p>
                </div>
            </div>
            
            <!-- Reports Page -->
            <div id="reports" class="page-content <?php echo $current_page === 'reports' ? 'active' : ''; ?>">
                <div class="dashboard-header">
                    <div>
                        <h1>Laporan Gempa</h1>
                        <p style="color: #b0b8a8; margin-top: 4px;">Laporan detail aktivitas seismik harian, mingguan, dan bulanan</p>
                    </div>
                </div>
                <div class="empty-state">
                    <div class="empty-state-icon">📄</div>
                    <h2>Laporan Komprehensif</h2>
                    <p>Laporan detail tentang aktivitas seismik, zona bahaya, dan rekomendasi mitigasi bencana akan tersedia di halaman ini untuk diunduh.</p>
                </div>
            </div>
            
            <!-- Settings Page -->
            <div id="settings" class="page-content <?php echo $current_page === 'settings' ? 'active' : ''; ?>">
                <div class="dashboard-header">
                    <div>
                        <h1>Pengaturan</h1>
                        <p style="color: #b0b8a8; margin-top: 4px;">Kelola preferensi akun dan sistem</p>
                    </div>
                </div>
                <div class="empty-state">
                    <div class="empty-state-icon">⚙️</div>
                    <h2>Pengaturan Sistem</h2>
                    <p>Pengaturan profil, notifikasi, preferensi tampilan, dan manajemen akun akan tersedia di halaman ini.</p>
                </div>
            </div>
        </main>
    </div>
    
    <div class="tooltip" id="tooltip"></div>
    
    <script>
        const hamburger = document.getElementById('hamburger');
        const sidebar = document.getElementById('sidebar');
        const menuLinks = document.querySelectorAll('.menu-link');
        const pageContents = document.querySelectorAll('.page-content');
        
        // Hamburger menu toggle
        hamburger.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
        
        // Close sidebar when clicking outside
        document.addEventListener('click', (e) => {
            if (!sidebar.contains(e.target) && e.target !== hamburger) {
                sidebar.classList.remove('active');
            }
        });
        
        // Tooltip functions
        function showTooltip(event, text) {
            const tooltip = document.getElementById('tooltip');
            tooltip.textContent = text;
            tooltip.style.display = 'block';
            tooltip.style.left = (event.pageX + 10) + 'px';
            tooltip.style.top = (event.pageY - 20) + 'px';
        }
        
        function hideTooltip() {
            document.getElementById('tooltip').style.display = 'none';
        }
        
        // Region interaction
        document.querySelectorAll('.region-path').forEach(region => {
            region.addEventListener('click', function() {
                const regionName = this.getAttribute('data-region');
                const risk = this.getAttribute('data-risk');
                alert(`Wilayah: ${regionName}\nTingkat Risiko: ${risk}`);
            });
            
            region.addEventListener('mouseenter', function() {
                const regionName = this.getAttribute('data-region');
                showTooltip(event, regionName);
            });
            
            region.addEventListener('mouseleave', hideTooltip);
        });
        
        // Handle logout
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (this.querySelector('button[name="logout"]')) {
                    e.preventDefault();
                    fetch('logout.php').then(() => {
                        window.location.href = 'login.php';
                    });
                }
            });
        });
    </script>
</body>
</html>
