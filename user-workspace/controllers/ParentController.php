<?php
require_once __DIR__ . '/../includes/Controller.php';

class ParentController extends Controller {
    private $parentId;
    private $children = [];

    public function __construct() {
        parent::__construct();
        $this->requireRole('parent');
        $this->parentId = Session::getUserId();
        $this->loadChildren();
    }

    private function loadChildren() {
        try {
            $sql = "SELECT * FROM Students WHERE parent_id = :parent_id";
            $this->children = $this->db->fetchAll($sql, ['parent_id' => $this->parentId]);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading children data: ' . $e->getMessage());
            $this->children = [];
        }
    }

    // Dashboard
    public function dashboard() {
        try {
            $data = [
                'children' => $this->children,
                'recentGrades' => [],
                'recentAttendance' => [],
                'upcomingFees' => []
            ];

            // Get recent grades for all children
            if (!empty($this->children)) {
                $childrenIds = array_column($this->children, 'id');
                $placeholders = str_repeat('?,', count($childrenIds) - 1) . '?';
                
                // Recent grades
                $sql = "SELECT s.name as student_name, g.* 
                        FROM Grades g 
                        JOIN Students s ON g.student_id = s.id 
                        WHERE g.student_id IN ($placeholders) 
                        ORDER BY g.id DESC LIMIT 5";
                $data['recentGrades'] = $this->db->fetchAll($sql, $childrenIds);

                // Recent attendance
                $sql = "SELECT s.name as student_name, a.* 
                        FROM Attendance a 
                        JOIN Students s ON a.student_id = s.id 
                        WHERE a.student_id IN ($placeholders) 
                        ORDER BY a.date DESC LIMIT 5";
                $data['recentAttendance'] = $this->db->fetchAll($sql, $childrenIds);

                // Upcoming fees
                $sql = "SELECT s.name as student_name, i.* 
                        FROM Invoices i 
                        JOIN Students s ON i.student_id = s.id 
                        WHERE i.student_id IN ($placeholders) 
                        AND i.status = 'unpaid' 
                        ORDER BY i.id DESC";
                $data['upcomingFees'] = $this->db->fetchAll($sql, $childrenIds);
            }

            $this->render('parent/dashboard', $data);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading dashboard: ' . $e->getMessage());
            $this->render('parent/dashboard', ['children' => $this->children]);
        }
    }

    // View Grades
    public function viewGrades($studentId = null) {
        try {
            if ($studentId && !$this->validateChild($studentId)) {
                $this->setFlash('error', 'Unauthorized access');
                $this->redirect('/parent/dashboard');
                return;
            }

            $conditions = $studentId 
                ? ['student_id' => $studentId] 
                : ['student_id' => array_column($this->children, 'id')];

            $sql = "SELECT s.name as student_name, g.* 
                    FROM Grades g 
                    JOIN Students s ON g.student_id = s.id 
                    WHERE g.student_id = :student_id 
                    ORDER BY g.subject";
            
            $grades = $this->db->fetchAll($sql, $conditions);
            
            $this->render('parent/grades', [
                'grades' => $grades,
                'children' => $this->children,
                'selectedStudent' => $studentId
            ]);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading grades: ' . $e->getMessage());
            $this->redirect('/parent/dashboard');
        }
    }

    // View Attendance
    public function viewAttendance($studentId = null) {
        try {
            if ($studentId && !$this->validateChild($studentId)) {
                $this->setFlash('error', 'Unauthorized access');
                $this->redirect('/parent/dashboard');
                return;
            }

            $conditions = $studentId 
                ? ['student_id' => $studentId] 
                : ['student_id' => array_column($this->children, 'id')];

            $sql = "SELECT s.name as student_name, a.* 
                    FROM Attendance a 
                    JOIN Students s ON a.student_id = s.id 
                    WHERE a.student_id = :student_id 
                    ORDER BY a.date DESC";
            
            $attendance = $this->db->fetchAll($sql, $conditions);
            
            $this->render('parent/attendance', [
                'attendance' => $attendance,
                'children' => $this->children,
                'selectedStudent' => $studentId
            ]);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading attendance: ' . $e->getMessage());
            $this->redirect('/parent/dashboard');
        }
    }

    // View Fees
    public function viewFees($studentId = null) {
        try {
            if ($studentId && !$this->validateChild($studentId)) {
                $this->setFlash('error', 'Unauthorized access');
                $this->redirect('/parent/dashboard');
                return;
            }

            $conditions = $studentId 
                ? ['student_id' => $studentId] 
                : ['student_id' => array_column($this->children, 'id')];

            $sql = "SELECT s.name as student_name, i.* 
                    FROM Invoices i 
                    JOIN Students s ON i.student_id = s.id 
                    WHERE i.student_id = :student_id 
                    ORDER BY i.id DESC";
            
            $fees = $this->db->fetchAll($sql, $conditions);
            
            $this->render('parent/fees', [
                'fees' => $fees,
                'children' => $this->children,
                'selectedStudent' => $studentId
            ]);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading fees: ' . $e->getMessage());
            $this->redirect('/parent/dashboard');
        }
    }

    // Teacher Communication
    public function messages() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $message = [
                    'sender_id' => $this->parentId,
                    'receiver_id' => $_POST['teacher_id'],
                    'message' => $_POST['message'],
                    'sent_at' => date('Y-m-d H:i:s')
                ];

                $this->db->insert('Messages', $message);
                $this->setFlash('success', 'Message sent successfully');
            } catch (Exception $e) {
                $this->setFlash('error', 'Error sending message: ' . $e->getMessage());
            }
        }

        try {
            // Get all teachers
            $teachers = $this->db->fetchAll("SELECT id, name FROM Users WHERE role = 'teacher'");
            
            // Get conversation history
            $sql = "SELECT m.*, u.name as sender_name 
                    FROM Messages m 
                    JOIN Users u ON m.sender_id = u.id 
                    WHERE m.sender_id = :parent_id OR m.receiver_id = :parent_id 
                    ORDER BY m.sent_at DESC";
            
            $messages = $this->db->fetchAll($sql, ['parent_id' => $this->parentId]);
            
            $this->render('parent/messages', [
                'teachers' => $teachers,
                'messages' => $messages
            ]);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading messages: ' . $e->getMessage());
            $this->redirect('/parent/dashboard');
        }
    }

    // Helper method to validate child belongs to parent
    private function validateChild($studentId) {
        return in_array($studentId, array_column($this->children, 'id'));
    }
}
