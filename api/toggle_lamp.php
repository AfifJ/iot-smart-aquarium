<?php
require_once '../config/database.php';
$db = new Database();

$data = json_decode(file_get_contents("php://input"));
$status = $data->status;

if ($db->toggleLamp($status)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>