<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'assignment_system');

// Application Settings
define('APP_NAME', 'Supervisor & Assessor Assignment System');
define('APP_URL', 'http://localhost/assignment-system');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('REPORT_DIR', __DIR__ . '/../reports/');

// Session Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour
define('SESSION_NAME', 'assignment_system');

// Date Format
define('DATE_FORMAT', 'd-m-Y');
define('DATE_TIME_FORMAT', 'd-m-Y H:i:s');

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable in production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Start Session
if (!headers_sent()) {
    session_name(SESSION_NAME);
    session_start();
}
?>
