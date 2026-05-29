<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../../src/controllers/AssignmentController.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ?page=login');
    exit();
}

$assignmentCtrl = new AssignmentController($connection);
$stats = $assignmentCtrl->getStatistics();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Assignment System</title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../layouts/navbar.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <div class="dashboard-header">
                <h1>Welcome, <?php echo $_SESSION['full_name']; ?>! 👋</h1>
                <p>Manage supervisors and assessors assignments</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card success">
                    <h3>Total Assignments</h3>
                    <div class="value"><?php echo $stats['total_assignments']; ?></div>
                </div>

                <div class="stat-card info">
                    <h3>Completed</h3>
                    <div class="value"><?php echo $stats['completed_assignments']; ?></div>
                </div>

                <div class="stat-card warning">
                    <h3>Pending</h3>
                    <div class="value"><?php echo $stats['pending_assignments']; ?></div>
                </div>

                <div class="stat-card danger">
                    <h3>Active Candidates</h3>
                    <div class="value"><?php echo $stats['total_candidates']; ?></div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <h3 style="margin-bottom: 15px; color: #2c3e50;">Quick Actions</h3>
                    <a href="?page=candidates" class="btn btn-primary" style="display: block; margin-bottom: 10px; text-align: center;">
                        📋 Manage Candidates
                    </a>
                    <a href="?page=assignments" class="btn btn-success" style="display: block; margin-bottom: 10px; text-align: center;">
                        ➕ New Assignment
                    </a>
                    <a href="?page=view_assignments" class="btn btn-info" style="display: block; margin-bottom: 10px; text-align: center;">
                        👁️ View Assignments
                    </a>
                    <a href="?page=reports" class="btn btn-secondary" style="display: block; text-align: center;">
                        📊 Generate Reports
                    </a>
                </div>

                <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <h3 style="margin-bottom: 15px; color: #2c3e50;">System Info</h3>
                    <p><strong>Application:</strong> Supervisor & Assessor Assignment System</p>
                    <p><strong>Version:</strong> 1.0.0</p>
                    <p><strong>Your Role:</strong> <span style="background: #3498db; color: white; padding: 2px 8px; border-radius: 4px;"><?php echo ucfirst($_SESSION['role']); ?></span></p>
                    <p><strong>Last Login:</strong> <?php echo date('d-m-Y H:i:s'); ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
