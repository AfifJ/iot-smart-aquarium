<?php
require_once '../config/database.php';
$db = new Database();

$lampStatus = $db->getLampStatus();

if ($lampStatus) {
    echo json_encode(['success' => true, 'status' => $lampStatus['status']]);
} else {
    echo json_encode(['success' => false]);
}
?>
