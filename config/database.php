<?php
class Database {
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $database = 'smart_aquarium';
    public $conn;

    public function __construct() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
        
        if ($this->conn->connect_error) {
            die("Koneksi gagal: " . $this->conn->connect_error);
        }
    }

    public function getSensorData() {
        $query = "SELECT * FROM light_sensors ORDER BY timestamp DESC LIMIT 1";
        $result = $this->conn->query($query);
        return $result->fetch_assoc();
    }

    public function getLampStatus() {
        $query = "SELECT * FROM lamp_controls ORDER BY toggle_time DESC LIMIT 1";
        $result = $this->conn->query($query);
        return $result->fetch_assoc();
    }

    public function getFeedingAlarms() {
        $query = "SELECT * FROM feeding_alarms WHERE is_active = 1";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getFeedingHistory() {
        $query = "SELECT * FROM feeding_history ORDER BY timestamp DESC LIMIT 10";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function addAlarm($time) {
        $query = "INSERT INTO feeding_alarms (alarm_time) VALUES (?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $time);
        return $stmt->execute();
    }

    public function toggleAlarm($id, $status) {
        $query = "UPDATE feeding_alarms SET is_active = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $status, $id);
        return $stmt->execute();
    }

    public function toggleLamp($status) {
        $query = "INSERT INTO lamp_controls (status) VALUES (?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $status);
        return $stmt->execute();
    }

    public function manualFeed($amount) {
        $query = "INSERT INTO feeding_history (feed_type, amount) VALUES ('Manual', ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("d", $amount);
        return $stmt->execute();
    }
}
?>