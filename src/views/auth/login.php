<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Assignment System</title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h1>Assignment System</h1>
            
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                require_once __DIR__ . '/../controllers/AuthController.php';
                
                $username = $_POST['username'] ?? '';
                $password = $_POST['password'] ?? '';
                
                $auth = new AuthController($connection);
                
                if ($auth->login($username, $password)) {
                    header('Location: ?page=dashboard');
                    exit();
                } else {
                    echo '<div class="alert alert-danger">Invalid username or password</div>';
                }
            }
            ?>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            
            <div style="margin-top: 20px; text-align: center; color: #7f8c8d;">
                <p><small>Demo Credentials:<br>Username: <strong>admin</strong><br>Password: <strong>admin123</strong></small></p>
            </div>
        </div>
    </div>
</body>
</html>
