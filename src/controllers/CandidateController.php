<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

class CandidateController {
    private $connection;

    public function __construct($conn) {
        $this->connection = $conn;
    }

    // Create candidate
    public function createCandidate($name, $email, $phone, $gender, $qualification, $experience, $is_supervisor, $is_assessor) {
        $query = "INSERT INTO candidates (name, email, phone, gender, qualification, experience, is_supervisor, is_assessor, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("sssssibb", $name, $email, $phone, $gender, $qualification, $experience, $is_supervisor, $is_assessor);

        if ($stmt->execute()) {
            return $this->connection->insert_id;
        }
        return false;
    }

    // Get all candidates
    public function getAllCandidates($filter = []) {
        $query = "SELECT * FROM candidates WHERE status = 'active'";

        if (isset($filter['is_supervisor']) && $filter['is_supervisor']) {
            $query .= " AND is_supervisor = true";
        }

        if (isset($filter['is_assessor']) && $filter['is_assessor']) {
            $query .= " AND is_assessor = true";
        }

        $query .= " ORDER BY name ASC";
        $result = $this->connection->query($query);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get candidate by ID
    public function getCandidateById($id) {
        $query = "SELECT * FROM candidates WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    // Update candidate
    public function updateCandidate($id, $name, $email, $phone, $gender, $qualification, $experience, $is_supervisor, $is_assessor) {
        $query = "UPDATE candidates SET name = ?, email = ?, phone = ?, gender = ?, qualification = ?, 
                  experience = ?, is_supervisor = ?, is_assessor = ? WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("sssssibbi", $name, $email, $phone, $gender, $qualification, $experience, $is_supervisor, $is_assessor, $id);

        return $stmt->execute();
    }

    // Delete candidate
    public function deleteCandidate($id) {
        $query = "UPDATE candidates SET status = 'inactive' WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    // Get supervisor count
    public function getSupervisorCount() {
        $query = "SELECT COUNT(*) as total FROM candidates WHERE is_supervisor = true AND status = 'active'";
        $result = $this->connection->query($query);
        return $result->fetch_assoc()['total'];
    }

    // Get assessor count
    public function getAssessorCount() {
        $query = "SELECT COUNT(*) as total FROM candidates WHERE is_assessor = true AND status = 'active'";
        $result = $this->connection->query($query);
        return $result->fetch_assoc()['total'];
    }

    // Search candidates
    public function searchCandidates($search_term) {
        $search_term = '%' . $search_term . '%';
        $query = "SELECT * FROM candidates WHERE (name LIKE ? OR email LIKE ? OR phone LIKE ?) AND status = 'active'";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("sss", $search_term, $search_term, $search_term);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
