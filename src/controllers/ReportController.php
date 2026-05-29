<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

class ReportController {
    private $connection;

    public function __construct($conn) {
        $this->connection = $conn;
    }

    // Generate assignment report
    public function generateAssignmentReport($assignment_id, $format = 'csv') {
        $assignment = $this->getAssignmentData($assignment_id);
        
        if (!$assignment) {
            return false;
        }

        if ($format === 'csv') {
            return $this->generateCSV($assignment);
        } elseif ($format === 'excel') {
            return $this->generateExcel($assignment);
        }

        return false;
    }

    // Get assignment data
    private function getAssignmentData($assignment_id) {
        $query = "SELECT a.*, o.name as occupation, l.name as level, c.name as center 
                  FROM assignments a
                  JOIN occupations o ON a.occupation_id = o.id
                  JOIN levels l ON a.level_id = l.id
                  JOIN assessment_centers c ON a.center_id = c.id
                  WHERE a.id = ?";
        
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $assignment_id);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }

    // Get assignment details
    private function getAssignmentDetails($assignment_id) {
        $query = "SELECT ad.*, c.name, c.email, c.phone FROM assignment_details ad
                  JOIN candidates c ON ad.candidate_id = c.id
                  WHERE ad.assignment_id = ?
                  ORDER BY ad.role, c.name";
        
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $assignment_id);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Generate CSV report
    private function generateCSV($assignment) {
        $filename = 'assignment_' . $assignment['id'] . '_' . date('Y-m-d_H-i-s') . '.csv';
        $filepath = REPORT_DIR . $filename;

        // Create CSV file
        $file = fopen($filepath, 'w');
        
        // Write headers
        fputcsv($file, ['Assignment Report']);
        fputcsv($file, []);
        
        // Assignment details
        fputcsv($file, ['Assignment Details']);
        fputcsv($file, ['Occupation', $assignment['occupation']]);
        fputcsv($file, ['Level', $assignment['level']]);
        fputcsv($file, ['Assessment Center', $assignment['center']]);
        fputcsv($file, ['Assessment Date', $this->formatDate($assignment['assessment_date'])]);
        fputcsv($file, ['Status', $assignment['status']]);
        fputcsv($file, []);

        // Assignment assignments
        $details = $this->getAssignmentDetails($assignment['id']);
        fputcsv($file, ['Supervisors and Assessors']);
        fputcsv($file, ['Name', 'Email', 'Phone', 'Role']);
        
        foreach ($details as $detail) {
            fputcsv($file, [
                $detail['name'],
                $detail['email'],
                $detail['phone'],
                ucfirst($detail['role'])
            ]);
        }

        fclose($file);
        
        return [
            'filename' => $filename,
            'path' => $filepath,
            'url' => APP_URL . '/reports/' . $filename
        ];
    }

    // Generate Excel report
    private function generateExcel($assignment) {
        $filename = 'assignment_' . $assignment['id'] . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        $filepath = REPORT_DIR . $filename;

        // Create simple Excel using CSV and conversion
        // For production, use PhpSpreadsheet library
        return $this->generateCSV($assignment);
    }

    // Generate all assignments report
    public function generateAllAssignmentsReport($format = 'csv') {
        $query = "SELECT a.id, a.assessment_date, o.name as occupation, l.name as level, c.name as center, 
                  a.status, COUNT(ad.id) as total_assigned
                  FROM assignments a
                  LEFT JOIN occupations o ON a.occupation_id = o.id
                  LEFT JOIN levels l ON a.level_id = l.id
                  LEFT JOIN assessment_centers c ON a.center_id = c.id
                  LEFT JOIN assignment_details ad ON a.id = ad.assignment_id
                  GROUP BY a.id
                  ORDER BY a.created_at DESC";
        
        $result = $this->connection->query($query);
        $assignments = $result->fetch_all(MYSQLI_ASSOC);

        if ($format === 'csv') {
            $filename = 'all_assignments_' . date('Y-m-d_H-i-s') . '.csv';
            $filepath = REPORT_DIR . $filename;

            $file = fopen($filepath, 'w');
            fputcsv($file, ['All Assignments Report']);
            fputcsv($file, ['Generated on', date('d-m-Y H:i:s')]);
            fputcsv($file, []);
            
            fputcsv($file, ['ID', 'Occupation', 'Level', 'Center', 'Assessment Date', 'Status', 'Total Assigned']);
            
            foreach ($assignments as $assignment) {
                fputcsv($file, [
                    $assignment['id'],
                    $assignment['occupation'],
                    $assignment['level'],
                    $assignment['center'],
                    $this->formatDate($assignment['assessment_date']),
                    $assignment['status'],
                    $assignment['total_assigned']
                ]);
            }

            fclose($file);

            return [
                'filename' => $filename,
                'path' => $filepath,
                'url' => APP_URL . '/reports/' . $filename
            ];
        }

        return false;
    }

    // Save report record
    public function saveReport($title, $type, $assignment_id, $file_path, $format, $created_by) {
        $query = "INSERT INTO reports (title, type, assignment_id, file_path, format, created_by) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ssiissi", $title, $type, $assignment_id, $file_path, $format, $created_by);

        return $stmt->execute();
    }

    // Get all reports
    public function getAllReports() {
        $query = "SELECT * FROM reports ORDER BY created_at DESC";
        $result = $this->connection->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Delete report
    public function deleteReport($id) {
        $query = "SELECT file_path FROM reports WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $report = $stmt->get_result()->fetch_assoc();

        if ($report && file_exists($report['file_path'])) {
            unlink($report['file_path']);
        }

        $query = "DELETE FROM reports WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    // Format date
    private function formatDate($date) {
        return date('d-m-Y', strtotime($date));
    }
}
?>
