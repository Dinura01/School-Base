<?php
require_once __DIR__ . '/../includes/Controller.php';

class PrincipalController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->requireRole('principal');
    }

    // Dashboard
    public function dashboard() {
        try {
            // Get total counts
            $totalStudents = $this->db->fetch("SELECT COUNT(*) as count FROM Students")['count'];
            $totalTeachers = $this->db->fetch("SELECT COUNT(*) as count FROM Users WHERE role = 'teacher'")['count'];
            $totalParents = $this->db->fetch("SELECT COUNT(*) as count FROM Users WHERE role = 'parent'")['count'];
            
            // Get recent attendance statistics
            $today = date('Y-m-d');
            $attendanceStats = $this->db->fetch(
                "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present
                FROM Attendance 
                WHERE date = :date",
                ['date' => $today]
            );
            
            // Calculate attendance percentage
            $attendancePercentage = $attendanceStats['total'] > 0 
                ? round(($attendanceStats['present'] / $attendanceStats['total']) * 100) 
                : 0;

            $this->render('principal/dashboard', [
                'totalStudents' => $totalStudents,
                'totalTeachers' => $totalTeachers,
                'totalParents' => $totalParents,
                'attendancePercentage' => $attendancePercentage
            ]);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading dashboard: ' . $e->getMessage());
            $this->render('principal/dashboard');
        }
    }

    // Staff Management
    public function viewStaff() {
        try {
            $sql = "SELECT * FROM Users WHERE role = 'teacher' ORDER BY name";
            $staff = $this->db->fetchAll($sql);
            
            $this->render('principal/staff/view', ['staff' => $staff]);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading staff list: ' . $e->getMessage());
            $this->render('principal/staff/view', ['staff' => []]);
        }
    }

    public function addStaff() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'name' => $_POST['name'],
                    'role' => 'teacher',
                    'password' => $_POST['password'] // Will be hashed in future
                ];

                $userId = $this->db->insert('Users', $data);
                
                if ($userId) {
                    $this->setFlash('success', 'Staff member added successfully');
                    $this->redirect('/principal/staff');
                }
            } catch (Exception $e) {
                $this->setFlash('error', 'Error adding staff: ' . $e->getMessage());
            }
        }
        
        $this->render('principal/staff/add');
    }

    // Student Management
    public function viewStudents() {
        try {
            $sql = "SELECT s.*, u.name as parent_name 
                    FROM Students s 
                    LEFT JOIN Users u ON s.parent_id = u.id 
                    ORDER BY s.name";
            $students = $this->db->fetchAll($sql);
            
            $this->render('principal/students/view', ['students' => $students]);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading student list: ' . $e->getMessage());
            $this->render('principal/students/view', ['students' => []]);
        }
    }

    // Reports
    public function generateReport($type = 'attendance') {
        try {
            $data = [];
            switch ($type) {
                case 'attendance':
                    $sql = "SELECT s.name, 
                           COUNT(*) as total_days,
                           SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_days
                           FROM Students s
                           LEFT JOIN Attendance a ON s.id = a.student_id
                           GROUP BY s.id, s.name";
                    $data['attendance'] = $this->db->fetchAll($sql);
                    break;

                case 'academic':
                    $sql = "SELECT s.name, g.subject, g.grade
                           FROM Students s
                           LEFT JOIN Grades g ON s.id = g.student_id
                           ORDER BY s.name, g.subject";
                    $data['grades'] = $this->db->fetchAll($sql);
                    break;
            }
            
            $this->render('principal/reports/' . $type, $data);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error generating report: ' . $e->getMessage());
            $this->redirect('/principal/dashboard');
        }
    }

    // Calendar Management
    public function manageCalendar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $event = [
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'start_date' => $_POST['start_date'],
                    'end_date' => $_POST['end_date']
                ];

                $this->db->insert('SchoolCalendar', $event);
                $this->setFlash('success', 'Event added successfully');
            } catch (Exception $e) {
                $this->setFlash('error', 'Error adding event: ' . $e->getMessage());
            }
        }

        // Fetch all calendar events
        try {
            $events = $this->db->fetchAll("SELECT * FROM SchoolCalendar ORDER BY start_date");
            $this->render('principal/calendar', ['events' => $events]);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading calendar: ' . $e->getMessage());
            $this->render('principal/calendar', ['events' => []]);
        }
    }

    // Budget Management
    public function manageBudget() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $budget = [
                    'amount' => $_POST['amount'],
                    'description' => $_POST['description'],
                    'status' => 'approved',
                    'approved_by' => Session::getUserId(),
                    'approved_at' => date('Y-m-d H:i:s')
                ];

                $this->db->insert('Budgets', $budget);
                $this->setFlash('success', 'Budget approved successfully');
            } catch (Exception $e) {
                $this->setFlash('error', 'Error approving budget: ' . $e->getMessage());
            }
        }

        try {
            $budgets = $this->db->fetchAll("SELECT * FROM Budgets ORDER BY approved_at DESC");
            $this->render('principal/budget', ['budgets' => $budgets]);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading budgets: ' . $e->getMessage());
            $this->render('principal/budget', ['budgets' => []]);
        }
    }
}
