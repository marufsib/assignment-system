<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

class AssignmentController {
    private $connection;

    public function __construct($conn) {
        $this->connection = $conn;
    }

    // Create new assignment batch
    public function createAssignment($occupation_id, $level_id, $center_id, $assessment_date, $num_supervisors, $num_assessors, $created_by) {
        $query = "INSERT INTO assignments (occupation_id, level_id, center_id, assessment_date, num_supervisors, num_assessors, status, created_by) 
                  VALUES (?, ?, ?, ?, ?, ?, 'pending', ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("iiisiii", $occupation_id, $level_id, $center_id, $assessment_date, $num_supervisors, $num_assessors, $created_by);

        if ($stmt->execute()) {
            return $this->connection->insert_id;
        }
        return false;
    }

    // Get all assignments
    public function getAssignments($status = null) {
        $query = "SELECT a.*, o.name as occupation, l.name as level, c.name as center 
                  FROM assignments a
                  JOIN occupations o ON a.occupation_id = o.id
                  JOIN levels l ON a.level_id = l.id
                  JOIN assessment_centers c ON a.center_id = c.id";

        if ($status) {
            $query .= " WHERE a.status = ?";
        }

        $query .= " ORDER BY a.created_at DESC";
        $stmt = $this->connection->prepare($query);

        if ($status) {
            $stmt->bind_param("s", $status);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get assignment by ID
    public function getAssignmentById($id) {
        $query = "SELECT a.*, o.name as occupation, l.name as level, c.name as center 
                  FROM assignments a
                  JOIN occupations o ON a.occupation_id = o.id
                  JOIN levels l ON a.level_id = l.id
                  JOIN assessment_centers c ON a.center_id = c.id
                  WHERE a.id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Update assignment
    public function updateAssignment($id, $occupation_id, $level_id, $center_id, $assessment_date, $num_supervisors, $num_assessors, $status) {
        $query = "UPDATE assignments SET occupation_id = ?, level_id = ?, center_id = ?, assessment_date = ?, 
                  num_supervisors = ?, num_assessors = ?, status = ? WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("iiisiii", $occupation_id, $level_id, $center_id, $assessment_date, $num_supervisors, $num_assessors, $status, $id);

        return $stmt->execute();
    }

    // Delete assignment
    public function deleteAssignment($id) {
        // First delete assignment details
        $query = "DELETE FROM assignment_details WHERE assignment_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Then delete assignment
        $query = "DELETE FROM assignments WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    // Run lottery assignment
    public function runLottery($assignment_id) {
        // Get assignment details
        $assignment = $this->getAssignmentById($assignment_id);
        if (!$assignment) {
            return false;
        }

        // Get available supervisors
        $supervisors = $this->getAvailableCandidates(true, $assignment['num_supervisors']);
        $assessors = $this->getAvailableCandidates(false, $assignment['num_assessors']);

        if (count($supervisors) < $assignment['num_supervisors'] || count($assessors) < $assignment['num_assessors']) {
            return false; // Not enough candidates
        }

        // Shuffle and assign supervisors
        shuffle($supervisors);
        $selected_supervisors = array_slice($supervisors, 0, $assignment['num_supervisors']);

        foreach ($selected_supervisors as $supervisor) {
            $this->assignCandidate($assignment_id, $supervisor['id'], 'supervisor');
        }

        // Shuffle and assign assessors
        shuffle($assessors);
        $selected_assessors = array_slice($assessors, 0, $assignment['num_assessors']);

        foreach ($selected_assessors as $assessor) {
            $this->assignCandidate($assignment_id, $assessor['id'], 'assessor');
        }

        // Update assignment status
        $query = "UPDATE assignments SET status = 'completed' WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $assignment_id);

        return $stmt->execute();
    }

    // Get available candidates
    private function getAvailableCandidates($is_supervisor = true, $limit = null) {
        $column = $is_supervisor ? 'is_supervisor' : 'is_assessor';
        $query = "SELECT * FROM candidates WHERE $column = true AND status = 'active' ORDER BY RAND()";

        if ($limit) {
            $query .= " LIMIT ?";
        }

        $stmt = $this->connection->prepare($query);

        if ($limit) {
            $stmt->bind_param("i", $limit);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Assign candidate to assignment
    private function assignCandidate($assignment_id, $candidate_id, $role) {
        $query = "INSERT INTO assignment_details (assignment_id, candidate_id, role, status) 
                  VALUES (?, ?, ?, 'assigned')";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("iis", $assignment_id, $candidate_id, $role);

        return $stmt->execute();
    }

    // Get assignment details
    public function getAssignmentDetails($assignment_id) {
        $query = "SELECT ad.*, c.name, c.email, c.phone FROM assignment_details ad
                  JOIN candidates c ON ad.candidate_id = c.id
                  WHERE ad.assignment_id = ?
                  ORDER BY ad.role, c.name";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $assignment_id);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get statistics
    public function getStatistics() {
        $stats = [];

        // Total assignments
        $query = "SELECT COUNT(*) as total FROM assignments";
        $result = $this->connection->query($query);
        $stats['total_assignments'] = $result->fetch_assoc()['total'];

        // Completed assignments
        $query = "SELECT COUNT(*) as total FROM assignments WHERE status = 'completed'";
        $result = $this->connection->query($query);
        $stats['completed_assignments'] = $result->fetch_assoc()['total'];

        // Pending assignments
        $query = "SELECT COUNT(*) as total FROM assignments WHERE status = 'pending'";
        $result = $this->connection->query($query);
        $stats['pending_assignments'] = $result->fetch_assoc()['total'];

        // Total candidates
        $query = "SELECT COUNT(*) as total FROM candidates WHERE status = 'active'";
        $result = $this->connection->query($query);
        $stats['total_candidates'] = $result->fetch_assoc()['total'];

        return $stats;
    }
}
?>
