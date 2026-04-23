<?php
// Include the database connection file
require_once 'config/db.php';

// --- SET YOUR NEW PASSWORD HERE ---
// Replace 'your_new_strong_password_here' with the password you want to use.
$new_password = '123456';

// --- DO NOT EDIT BELOW THIS LINE ---

// Check if a password was set
if ($new_password === 'your_new_strong_password_here' || empty($new_password)) {
    die("Error: Please open the reset-admin-password.php file and set a new password on line 6 before running this script.");
}

// Securely hash the new password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// The ID of the admin user to update. In our setup, the first admin has ID = 1.
$admin_id = 1;

// Prepare the SQL statement to update the password
$stmt = $conn->prepare("UPDATE admin SET password = ? WHERE id = ?");

// Check if the statement was prepared successfully
if ($stmt === false) {
    die("Error preparing the SQL statement: " . $conn->error);
}

// Bind the new hashed password and the admin ID to the statement
$stmt->bind_param("si", $hashed_password, $admin_id);

// Execute the statement and check for success
if ($stmt->execute()) {
    // Check if any rows were actually updated
    if ($stmt->affected_rows > 0) {
        echo "<h1 style='font-family: sans-serif; color: green;'>Success! The admin password has been reset.</h1>";
        echo "<p style='font-family: sans-serif;'>You can now log in with your new password.</p>";
        echo "<p style='font-family: sans-serif; color: red; font-weight: bold;'>VERY IMPORTANT: For your security, please DELETE this file (reset-admin-password.php) from your server NOW!</p>";
    } else {
        echo "<h1 style='font-family: sans-serif; color: orange;'>Warning: No user found with ID " . $admin_id . ". The password was not changed.</h1>";
        echo "<p style='font-family: sans-serif;'>Please check your 'admin' table in the database to ensure a user with this ID exists.</p>";
    }
} else {
    echo "<h1 style='font-family: sans-serif; color: red;'>Error: Failed to reset the password.</h1>";
    echo "<p style='font-family: sans-serif;'>Database error: " . $stmt->error . "</p>";
}

// Close the statement and the connection
$stmt->close();
$conn->close();
?>