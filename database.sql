-- Tabel Sensor Cahaya
CREATE TABLE light_sensors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    light_level FLOAT NOT NULL,
    status VARCHAR(50) NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Kontrol Lampu
CREATE TABLE lamp_controls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    status BOOLEAN NOT NULL,
    toggle_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    duration INT
);

-- Tabel Alarm Pakan
CREATE TABLE feeding_alarms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alarm_name VARCHAR(100),
    alarm_time TIME NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Riwayat Pakan
CREATE TABLE feeding_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    feed_type ENUM('Automatic', 'Manual') NOT NULL,
    amount FLOAT NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    alarm_id INT,
    FOREIGN KEY (alarm_id) REFERENCES feeding_alarms(id)
);

-- Tabel Konfigurasi Umum
CREATE TABLE system_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    config_key VARCHAR(100) NOT NULL,
    config_value VARCHAR(255) NOT NULL,
    description TEXT
);

-- Contoh Insert Awal
INSERT INTO system_config (config_key, config_value, description) VALUES 
('feed_amount_default', '10', 'Default feeding amount in grams'),
('light_sensor_threshold', '500', 'Light level threshold for automatic lamp control');