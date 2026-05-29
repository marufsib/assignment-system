<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

class AuthController {
    private $connection;

    public function __construct($conn) {
        $this->connection = $conn;
    }

    public function login($username, $password) {
        $query = "SELECT * FROM users WHERE username = ? AND status = 'active'";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                return true;
            }
        }
        return false;
    }

    public function logout() {
        session_destroy();
        return true;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    public function getCurrentUser() {
        return isset($_SESSION['user_id']) ? $_SESSION : null;
    }

    public function registerUser($username, $email, $password, $full_name, $role = 'user') {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO users (username, email, password, full_name, role, status) VALUES (?, ?, ?, ?, ?, 'active')";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("sssss", $username, $email, $hashed_password, $full_name, $role);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function changePassword($user_id, $old_password, $new_password) {
        $query = "SELECT password FROM users WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (password_verify($old_password, $user['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $query = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("si", $hashed_password, $user_id);
            return $stmt->execute();
        }
        return false;
    }
}
?>
