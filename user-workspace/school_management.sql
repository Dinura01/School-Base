-- Create database
CREATE DATABASE IF NOT EXISTS school_management;
USE school_management;

-- Users table
CREATE TABLE IF NOT EXISTS Users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('principal', 'teacher', 'parent', 'accountant') NOT NULL,
    phone VARCHAR(20),
    photo VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Students table
CREATE TABLE IF NOT EXISTS Students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    roll_no VARCHAR(20) UNIQUE NOT NULL,
    grade INT NOT NULL,
    section CHAR(1) NOT NULL,
    dob DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    parent_id INT,
    address TEXT,
    photo VARCHAR(255),
    blood_group VARCHAR(5),
    status ENUM('active', 'inactive') DEFAULT 'active',
    admission_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES Users(id) ON DELETE SET NULL
);

-- Attendance table
CREATE TABLE IF NOT EXISTS Attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('present', 'absent', 'late') NOT NULL,
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES Students(id) ON DELETE CASCADE
);

-- Subjects table
CREATE TABLE IF NOT EXISTS Subjects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Grades table
CREATE TABLE IF NOT EXISTS Grades (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    grade VARCHAR(5) NOT NULL,
    remarks TEXT,
    exam_type ENUM('midterm', 'final', 'unit_test') NOT NULL,
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES Students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES Subjects(id) ON DELETE CASCADE
);

-- Invoices table
CREATE TABLE IF NOT EXISTS Invoices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description TEXT,
    due_date DATE NOT NULL,
    status ENUM('paid', 'unpaid', 'overdue') DEFAULT 'unpaid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES Students(id) ON DELETE CASCADE
);

-- Transactions table
CREATE TABLE IF NOT EXISTS Transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id INT,
    amount DECIMAL(10,2) NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    description TEXT,
    date DATE NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES Invoices(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES Users(id)
);

-- SchoolCalendar table
CREATE TABLE IF NOT EXISTS SchoolCalendar (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    category ENUM('academic', 'exam', 'holiday', 'activity') NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES Users(id)
);

-- Messages table
CREATE TABLE IF NOT EXISTS Messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    read_status BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES Users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES Users(id) ON DELETE CASCADE
);

-- Notifications table
CREATE TABLE IF NOT EXISTS Notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'warning', 'success', 'error') DEFAULT 'info',
    read_status BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE
);

-- Settings table
CREATE TABLE IF NOT EXISTS Settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    setting_key VARCHAR(50) NOT NULL,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_setting (user_id, setting_key)
);

-- Budgets table
CREATE TABLE IF NOT EXISTS Budgets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    department VARCHAR(100) NOT NULL,
    category VARCHAR(100) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    fiscal_year YEAR NOT NULL,
    approved_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (approved_by) REFERENCES Users(id) ON DELETE SET NULL
);

-- Create indexes
CREATE INDEX idx_student_grade ON Students(grade);
CREATE INDEX idx_attendance_date ON Attendance(date);
CREATE INDEX idx_invoice_status ON Invoices(status);
CREATE INDEX idx_transaction_date ON Transactions(date);
CREATE INDEX idx_calendar_dates ON SchoolCalendar(start_date, end_date);
CREATE INDEX idx_message_users ON Messages(sender_id, receiver_id);
CREATE INDEX idx_notification_user ON Notifications(user_id, read_status);

-- Insert default admin user
INSERT INTO Users (name, email, password, role) 
VALUES ('Admin', 'admin@school.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'principal');
