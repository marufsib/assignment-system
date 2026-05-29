<?php
require_once __DIR__ . '/../../config/config.php';

// Get current page
$current_page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation</title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                Assignment System
            </div>
            
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">☰</button>
            
            <div class="navbar-menu">
                <a href="?page=dashboard" class="<?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
                    📊 Dashboard
                </a>
                <a href="?page=candidates" class="<?php echo $current_page === 'candidates' ? 'active' : ''; ?>">
                    👥 Candidates
                </a>
                <a href="?page=assignments" class="<?php echo $current_page === 'assignments' ? 'active' : ''; ?>">
                    ✏️ Create Assignment
                </a>
                <a href="?page=view_assignments" class="<?php echo $current_page === 'view_assignments' ? 'active' : ''; ?>">
                    👁️ View Assignments
                </a>
                <a href="?page=reports" class="<?php echo $current_page === 'reports' ? 'active' : ''; ?>">
                    📋 Reports
                </a>
                <a href="?page=settings" class="<?php echo $current_page === 'settings' ? 'active' : ''; ?>">
                    ⚙️ Settings
                </a>
            </div>
            
            <div class="navbar-user">
                <span style="font-size: 0.9rem;"><?php echo $_SESSION['username'] ?? 'User'; ?></span>
                <div class="user-avatar"><?php echo substr($_SESSION['username'] ?? 'U', 0, 1); ?></div>
                <a href="?page=logout" class="logout-btn">Logout</a>
            </div>
        </div>
    </nav>

    <script src="<?php echo APP_URL; ?>/js/script.js"></script>
</body>
</html>
