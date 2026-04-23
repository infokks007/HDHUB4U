<?php
require_once '../config/db.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_loggedin'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_loggedin'] = true;
            header("Location: dashboard.php");
            exit;
        }
    }
    $error = 'Invalid username or password!';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <style>
        body { font-family: sans-serif; background: #1a1a1a; color: #fff; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { background: #2c2c2c; padding: 40px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.5); width: 320px; text-align: center; }
        h2 { margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; text-align: left; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #555; background: #333; color: #fff; box-sizing: border-box; }
        button { width: 100%; padding: 12px; border: none; border-radius: 5px; background-color: #f57f26; color: white; font-weight: bold; cursor: pointer; }
        .error { color: #ff416c; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <form action="index.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>