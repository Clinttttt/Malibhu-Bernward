<?php
try {
    $conn = new PDO('sqlite:' . __DIR__ . '/../database.db');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("PRAGMA foreign_keys = ON");
    
    // Create tables if they don't exist
    $conn->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $conn->exec("
        CREATE TABLE IF NOT EXISTS reservations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            event_type TEXT NOT NULL,
            reservation_date DATE NOT NULL,
            checkout_date DATE,
            guests INTEGER NOT NULL,
            special_request TEXT,
            status TEXT DEFAULT 'Pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");

    $columns = $conn->query("PRAGMA table_info(reservations)")->fetchAll(PDO::FETCH_COLUMN, 1);
    if(!in_array('checkout_date', $columns)){
        $conn->exec("ALTER TABLE reservations ADD COLUMN checkout_date DATE");
    }
    if(!in_array('special_request', $columns)){
        $conn->exec("ALTER TABLE reservations ADD COLUMN special_request TEXT");
    }
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
