<?php
require_once '../config/database.php';
$db = new Database();

$data = json_decode(file_get_contents("php://input"));
$id = $data->id;
$time = $data->time;

$query = "UPDATE feeding_alarms SET alarm_time = ? WHERE id = ?";
$stmt = $db->conn->prepare($query);
$stmt->bind_param("si", $time, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
