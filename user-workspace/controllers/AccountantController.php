<?php
require_once __DIR__ . '/../includes/Controller.php';

class AccountantController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->requireRole('accountant');
    }

    // Dashboard
    public function dashboard() {
        try {
            // Get financial overview
            $totalFees = $this->db->fetch("SELECT SUM(amount) as total FROM Invoices")['total'] ?? 0;
            $collectedFees = $this->db->fetch(
                "SELECT SUM(amount) as total FROM Invoices WHERE status = 'paid'"
            )['total'] ?? 0;
            $pendingFees = $this->db->fetch(
                "SELECT SUM(amount) as total FROM Invoices WHERE status = 'unpaid'"
            )['total'] ?? 0;
            
            // Get recent transactions
            $recentTransactions = $this->db->fetchAll(
                "SELECT * FROM Transactions ORDER BY date DESC LIMIT 5"
            );
            
            // Get defaulter count
            $defaulterCount = $this->db->fetch(
                "SELECT COUNT(DISTINCT student_id) as count FROM Invoices WHERE status = 'unpaid'"
            )['count'];

            $this->render('accountant/dashboard', [
                'totalFees' => $totalFees,
                'collectedFees' => $collectedFees,
                'pendingFees' => $pendingFees,
                'recentTransactions' => $recentTransactions,
                'defaulterCount' => $defaulterCount
            ]);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading dashboard: ' . $e->getMessage());
            $this->render('accountant/dashboard');
        }
    }

    // Fee Management
    public function manageFees() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $invoice = [
                    'student_id' => $_POST['student_id'],
                    'amount' => $_POST['amount'],
                    'status' => 'unpaid',
                    'due_date' => $_POST['due_date'] ?? date('Y-m-d', strtotime('+30 days'))
                ];

                $this->db->insert('Invoices', $invoice);
                $this->setFlash('success', 'Fee invoice created successfully');
            } catch (Exception $e) {
                $this->setFlash('error', 'Error creating invoice: ' . $e->getMessage());
            }
        }

        try {
            // Get all students with their fee status
            $sql = "SELECT s.*, 
                    COALESCE(SUM(CASE WHEN i.status = 'unpaid' THEN i.amount ELSE 0 END), 0) as pending_fees,
                    COALESCE(SUM(CASE WHEN i.status = 'paid' THEN i.amount ELSE 0 END), 0) as paid_fees
                    FROM Students s
                    LEFT JOIN Invoices i ON s.id = i.student_id
                    GROUP BY s.id
                    ORDER BY s.name";
            
            $students = $this->db->fetchAll($sql);
            
            $this->render('accountant/fees/manage', ['students' => $students]);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading fee management: ' . $e->getMessage());
            $this->redirect('/accountant/dashboard');
        }
    }

    // Record Payment
    public function recordPayment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->db->beginTransaction();

                // Update invoice status
                $this->db->update('Invoices',
                    ['status' => 'paid'],
                    ['id' => $_POST['invoice_id']]
                );

                // Record transaction
                $transaction = [
                    'user_id' => Session::getUserId(),
                    'amount' => $_POST['amount'],
                    'date' => date('Y-m-d'),
                    'description' => 'Fee payment for invoice #' . $_POST['invoice_id']
                ];
                $this->db->insert('Transactions', $transaction);

                $this->db->commit();
                $this->setFlash('success', 'Payment recorded successfully');
            } catch (Exception $e) {
                $this->db->rollBack();
                $this->setFlash('error', 'Error recording payment: ' . $e->getMessage());
            }
        }

        try {
            // Get unpaid invoices
            $sql = "SELECT i.*, s.name as student_name 
                    FROM Invoices i 
                    JOIN Students s ON i.student_id = s.id 
                    WHERE i.status = 'unpaid' 
                    ORDER BY i.due_date";
            
            $invoices = $this->db->fetchAll($sql);
            
            $this->render('accountant/fees/record-payment', ['invoices' => $invoices]);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading payment page: ' . $e->getMessage());
            $this->redirect('/accountant/dashboard');
        }
    }

    // Generate Financial Reports
    public function generateReport($type = 'collection') {
        try {
            $data = [];
            switch ($type) {
                case 'collection':
                    $sql = "SELECT DATE_FORMAT(date, '%Y-%m') as month,
                           SUM(amount) as total_amount,
                           COUNT(*) as transaction_count
                           FROM Transactions
                           GROUP BY month
                           ORDER BY month DESC";
                    $data['collections'] = $this->db->fetchAll($sql);
                    break;

                case 'defaulters':
                    $sql = "SELECT s.name as student_name,
                           COUNT(i.id) as unpaid_invoices,
                           SUM(i.amount) as total_pending
                           FROM Students s
                           JOIN Invoices i ON s.id = i.student_id
                           WHERE i.status = 'unpaid'
                           GROUP BY s.id, s.name
                           ORDER BY total_pending DESC";
                    $data['defaulters'] = $this->db->fetchAll($sql);
                    break;

                case 'summary':
                    // Monthly summary
                    $sql = "SELECT 
                           DATE_FORMAT(date, '%Y-%m') as month,
                           SUM(amount) as total_amount,
                           COUNT(*) as transaction_count
                           FROM Transactions
                           GROUP BY month
                           ORDER BY month DESC
                           LIMIT 12";
                    $data['monthly_summary'] = $this->db->fetchAll($sql);

                    // Fee type breakdown
                    $sql = "SELECT 
                           COUNT(*) as invoice_count,
                           SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as collected,
                           SUM(CASE WHEN status = 'unpaid' THEN amount ELSE 0 END) as pending
                           FROM Invoices";
                    $data['fee_summary'] = $this->db->fetch($sql);
                    break;
            }
            
            $this->render('accountant/reports/' . $type, $data);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error generating report: ' . $e->getMessage());
            $this->redirect('/accountant/dashboard');
        }
    }

    // Send Payment Reminders
    public function sendReminders() {
        try {
            // Get defaulters
            $sql = "SELECT s.*, u.name as parent_name,
                   GROUP_CONCAT(i.id) as invoice_ids,
                   SUM(i.amount) as total_pending
                   FROM Students s
                   JOIN Users u ON s.parent_id = u.id
                   JOIN Invoices i ON s.id = i.student_id
                   WHERE i.status = 'unpaid'
                   GROUP BY s.id
                   HAVING total_pending > 0";
            
            $defaulters = $this->db->fetchAll($sql);
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Send reminders (implement actual reminder sending logic here)
                foreach ($_POST['selected_students'] as $studentId) {
                    // Record reminder
                    $reminder = [
                        'student_id' => $studentId,
                        'sent_at' => date('Y-m-d H:i:s'),
                        'sent_by' => Session::getUserId()
                    ];
                    $this->db->insert('PaymentReminders', $reminder);
                }
                
                $this->setFlash('success', 'Payment reminders sent successfully');
            }
            
            $this->render('accountant/fees/reminders', ['defaulters' => $defaulters]);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error managing reminders: ' . $e->getMessage());
            $this->redirect('/accountant/dashboard');
        }
    }

    // Track Expenses
    public function trackExpenses() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $expense = [
                    'amount' => $_POST['amount'],
                    'description' => $_POST['description'],
                    'date' => $_POST['date'] ?? date('Y-m-d'),
                    'category' => $_POST['category']
                ];

                $this->db->insert('Expenses', $expense);
                $this->setFlash('success', 'Expense recorded successfully');
            } catch (Exception $e) {
                $this->setFlash('error', 'Error recording expense: ' . $e->getMessage());
            }
        }

        try {
            // Get expense categories
            $categories = $this->db->fetchAll("SELECT DISTINCT category FROM Expenses");
            
            // Get recent expenses
            $expenses = $this->db->fetchAll(
                "SELECT * FROM Expenses ORDER BY date DESC LIMIT 10"
            );
            
            // Get category-wise totals
            $sql = "SELECT category, SUM(amount) as total 
                    FROM Expenses 
                    GROUP BY category";
            $categoryTotals = $this->db->fetchAll($sql);
            
            $this->render('accountant/expenses/track', [
                'categories' => $categories,
                'expenses' => $expenses,
                'categoryTotals' => $categoryTotals
            ]);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading expenses: ' . $e->getMessage());
            $this->redirect('/accountant/dashboard');
        }
    }
}
