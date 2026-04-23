<?php
require_once '../config/db.php';

if (!isset($_SESSION['admin_loggedin'])) {
    header("Location: index.php");
    exit;
}
?>