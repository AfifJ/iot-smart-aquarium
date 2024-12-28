<?php
require_once '../config/database.php';
$db = new Database();

$data = json_decode(file_get_contents("php://input"));
$id = $data->id;

if ($db->deleteAlarm($id)) { // Set is_active to 0 to deactivate
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>