<?php
// Database configuration
define('DB_PATH', __DIR__ . '/data/earthquake_db.sqlite');

// Create data directory if not exists
if (!is_dir(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0755, true);
}

// Initialize database
function initDatabase() {
    try {
        $db = new PDO('sqlite:' . DB_PATH);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create users table
        $db->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                email TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Create earthquakes table
        $db->exec("
            CREATE TABLE IF NOT EXISTS earthquakes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                location TEXT NOT NULL,
                magnitude REAL NOT NULL,
                latitude REAL NOT NULL,
                longitude REAL NOT NULL,
                depth REAL NOT NULL,
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        return $db;
    } catch (Exception $e) {
        die("Database error: " . $e->getMessage());
    }
}

// Get database connection
function getDB() {
    static $db = null;
    if ($db === null) {
        $db = new PDO('sqlite:' . DB_PATH);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    return $db;
}

// Mock earthquake data
$mock_earthquakes = [
    ['location' => 'Palu', 'magnitude' => 5.8, 'latitude' => -0.893, 'longitude' => 119.877, 'depth' => 12, 'timestamp' => date('Y-m-d H:i:s', strtotime('-2 hours'))],
    ['location' => 'Donggala', 'magnitude' => 5.2, 'latitude' => -0.645, 'longitude' => 119.802, 'depth' => 15, 'timestamp' => date('Y-m-d H:i:s', strtotime('-4 hours'))],
    ['location' => 'Manado', 'magnitude' => 4.0, 'latitude' => 1.487, 'longitude' => 124.843, 'depth' => 18, 'timestamp' => date('Y-m-d H:i:s', strtotime('-6 hours'))],
    ['location' => 'Toli-toli', 'magnitude' => 4.5, 'latitude' => 0.822, 'longitude' => 120.794, 'depth' => 22, 'timestamp' => date('Y-m-d H:i:s', strtotime('-8 hours'))],
    ['location' => 'Morowali', 'magnitude' => 4.2, 'latitude' => -1.355, 'longitude' => 121.717, 'depth' => 20, 'timestamp' => date('Y-m-d H:i:s', strtotime('-10 hours'))]
];

// Initialize DB on first load
initDatabase();
?>
