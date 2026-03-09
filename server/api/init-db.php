<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
setCorsHeaders();

if (getMethod() !== 'GET') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

try {
    $db = getDb();

    // Kullanıcılar
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id VARCHAR(50) PRIMARY KEY,
            username VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            role VARCHAR(20) NOT NULL DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Çekiciler
    $db->exec("
        CREATE TABLE IF NOT EXISTS trucks (
            id VARCHAR(50) PRIMARY KEY,
            plate VARCHAR(50) NOT NULL,
            category VARCHAR(50) NOT NULL DEFAULT 'asmira',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Dorseler
    $db->exec("
        CREATE TABLE IF NOT EXISTS trailers (
            id VARCHAR(50) PRIMARY KEY,
            plate VARCHAR(50) NOT NULL,
            category VARCHAR(50) NOT NULL DEFAULT 'asmira',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Araç Setleri
    $db->exec("
        CREATE TABLE IF NOT EXISTS vehicle_sets (
            id VARCHAR(50) PRIMARY KEY,
            truck_id VARCHAR(50) NOT NULL,
            trailer_id VARCHAR(50) NOT NULL,
            category VARCHAR(50) NOT NULL DEFAULT 'asmira'
        )
    ");

    // Araç Evrakları
    $db->exec("
        CREATE TABLE IF NOT EXISTS vehicle_documents (
            id INT AUTO_INCREMENT PRIMARY KEY,
            owner_id VARCHAR(50) NOT NULL,
            owner_type VARCHAR(20) NOT NULL,
            doc_type VARCHAR(100) NOT NULL,
            label VARCHAR(255) NOT NULL,
            file_name VARCHAR(255),
            file_path VARCHAR(500),
            expiry_date DATE,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");

    // Şoförler
    $db->exec("
        CREATE TABLE IF NOT EXISTS drivers (
            id VARCHAR(50) PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            tc_no VARCHAR(20) NOT NULL,
            phone VARCHAR(30) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Şoför Evrakları
    $db->exec("
        CREATE TABLE IF NOT EXISTS driver_documents (
            id INT AUTO_INCREMENT PRIMARY KEY,
            driver_id VARCHAR(50) NOT NULL,
            doc_type VARCHAR(100) NOT NULL,
            label VARCHAR(255) NOT NULL,
            file_name VARCHAR(255),
            file_path VARCHAR(500),
            expiry_date DATE,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");

    // Operasyonlar
    $db->exec("
        CREATE TABLE IF NOT EXISTS operations (
            id VARCHAR(50) PRIMARY KEY,
            vessel_name VARCHAR(255) NOT NULL,
            vessel_type VARCHAR(20) NOT NULL DEFAULT 'ship',
            imo_number VARCHAR(50),
            quantity DECIMAL(10,2) NOT NULL DEFAULT 0,
            unit VARCHAR(10) NOT NULL DEFAULT 'MT',
            loading_place VARCHAR(255),
            port VARCHAR(255) NOT NULL,
            date DATE NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'planned',
            driver_name VARCHAR(255) DEFAULT '',
            driver_phone VARCHAR(50) DEFAULT '',
            agent_note TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");

    // Dilekçe Kategorileri
    $db->exec("
        CREATE TABLE IF NOT EXISTS petition_categories (
            id VARCHAR(50) PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            icon VARCHAR(50) DEFAULT 'FileText',
            slug VARCHAR(100) NOT NULL
        )
    ");

    // Dilekçe Şablonları
    $db->exec("
        CREATE TABLE IF NOT EXISTS petition_templates (
            id VARCHAR(50) PRIMARY KEY,
            short_name VARCHAR(100) NOT NULL,
            name VARCHAR(255) NOT NULL,
            default_text TEXT,
            category VARCHAR(100) NOT NULL,
            is_default TINYINT DEFAULT 0,
            created_at BIGINT
        )
    ");

    // Tedarikçi Firmaları (JSON blob)
    $db->exec("
        CREATE TABLE IF NOT EXISTS supplier_data (
            id INT PRIMARY KEY DEFAULT 1,
            data LONGTEXT NOT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");

    // Varsayılan admin kullanıcısı
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute(['asmira']);
    if (!$stmt->fetch()) {
        $db->prepare("INSERT INTO users (id, username, password, name, role) VALUES (?, ?, ?, ?, ?)")
           ->execute(['user_1', 'asmira', '123', 'Asmira Operasyon', 'admin']);
    }

    jsonResponse(['success' => true, 'message' => 'Veritabanı tabloları oluşturuldu']);

} catch (Exception $e) {
    jsonResponse(['error' => 'Veritabanı başlatılamadı', 'details' => $e->getMessage()], 500);
}
