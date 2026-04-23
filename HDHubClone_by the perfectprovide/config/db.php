<?php
// --- DATABASE CONNECTION ---
$db_host = 'localhost';
$db_user = 'minicart_demo'; // <-- CHANGE THIS
$db_pass = 'minicart_demo'; // <-- CHANGE THIS
$db_name = 'minicart_demo';               // <-- The database name from Part 1

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check for connection errors
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Start the session for the entire application
session_start();
?>