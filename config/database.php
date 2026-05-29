<?php
require_once __DIR__ . '/config.php';

class Database {
    private $host = DB_HOST;
    private $db_user = DB_USER;
    private $db_pass = DB_PASS;
    private $db_name = DB_NAME;
    private $connection;
    private $error;

    public function connect() {
        $this->connection = new mysqli($this->host, $this->db_user, $this->db_pass, $this->db_name);

        // Check connection
        if ($this->connection->connect_error) {
            $this->error = 'Connection Error: ' . $this->connection->connect_error;
            return false;
        }

        // Set charset
        $this->connection->set_charset('utf8');
        return $this->connection;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function getError() {
        return $this->error;
    }

    public function closeConnection() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}

$database = new Database();
$connection = $database->connect();

if (!$connection) {
    die('Database connection failed: ' . $database->getError());
}
?>
