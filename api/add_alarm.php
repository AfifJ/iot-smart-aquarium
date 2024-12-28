<?php
require_once '../config/database.php';
$db = new Database();

$data = json_decode(file_get_contents("php://input"));
$name = $data->name;
$time = $data->time;

if ($db->addAlarm($name,$time)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>