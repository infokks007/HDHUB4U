<?php
require_once 'auth.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $conn->prepare("INSERT INTO sliders (image_url, target_url, sort_order) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $_POST['image_url'], $_POST['target_url'], $_POST['sort_order']);
    $stmt->execute();
    header("Location: dashboard.php");
    exit;
}
?>