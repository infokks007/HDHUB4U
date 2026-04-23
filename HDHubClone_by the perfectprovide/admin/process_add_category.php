<?php
require_once 'auth.php';

// Function to create a URL-friendly slug
function create_slug($string) {
   $string = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($string));
   return trim($string, '-');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $slug = create_slug($name); // Auto-generate the slug
    
    $stmt = $conn->prepare("INSERT INTO categories (name, slug, style_class, sort_order) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $name, $slug, $_POST['style_class'], $_POST['sort_order']);
    $stmt->execute();
    
    header("Location: dashboard.php");
    exit;
}
?>