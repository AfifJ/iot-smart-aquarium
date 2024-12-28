<?php
require_once '../config/database.php';
$db = new Database();

$data = json_decode(file_get_contents("php://input"));
$lightLevel = $data->light_level;
$status = $data->status;

if ($db->insertSensorData($lightLevel, $status)) {
    echo json_encode(value: ['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>