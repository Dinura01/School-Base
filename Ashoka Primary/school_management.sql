-- Create database
CREATE DATABASE IF NOT EXISTS school_management;
USE school_management;

-- Create Users table
CREATE TABLE Users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    role ENUM('principal', 'parent', 'accountant', 'staff') NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Create Students table
CREATE TABLE Students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    grade VARCHAR(10) NOT NULL,
    parent_id INT,
    FOREIGN KEY (parent_id) REFERENCES Users(id)
);

-- Create Attendance table
CREATE TABLE Attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    date DATE NOT NULL,
    status ENUM('present', 'absent') NOT NULL,
    FOREIGN KEY (student_id) REFERENCES Students(id)
);

-- Create Grades table
CREATE TABLE Grades (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    subject VARCHAR(100) NOT NULL,
    grade VARCHAR(2) NOT NULL,
    FOREIGN KEY (student_id) REFERENCES Students(id)
);

-- Create Transactions table
CREATE TABLE Transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    amount DECIMAL(10, 2) NOT NULL,
    date DATE NOT NULL,
    description VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES Users(id)
);

-- Create LeaveRequests table
CREATE TABLE LeaveRequests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    teacher_name VARCHAR(100) NOT NULL,
    leave_date DATE NOT NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL
);

-- Create Invoices table
CREATE TABLE Invoices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    amount DECIMAL(10, 2) NOT NULL,
    status ENUM('paid', 'unpaid') NOT NULL,
    FOREIGN KEY (student_id) REFERENCES Students(id)
);

-- Insert example data into Users table
INSERT INTO Users (name, role, password) VALUES
('Principal John', 'principal', '12345'),
('Parent Alice', 'parent', '12345'),
('Accountant Bob', 'accountant', '12345'),
('Staff Mary', 'staff', '12345'),
('AAAB', 'staff', '123'); -- New user with username "AAAB" and password "123"

-- Insert example data into Students table
INSERT INTO Students (name, grade, parent_id) VALUES
('Student One', '5th', 2),
('Student Two', '6th', 2),
('Student Three', '5th', 2);

-- Insert example data into Attendance table
INSERT INTO Attendance (student_id, date, status) VALUES
(1, '2025-02-01', 'present'),
(1, '2025-02-02', 'absent'),
(2, '2025-02-01', 'present'),
(2, '2025-02-02', 'present'),
(3, '2025-02-01', 'absent');

-- Insert example data into Grades table
INSERT INTO Grades (student_id , subject, grade) VALUES
(1, 'Math', 'A'),
(1, 'Science', 'B'),
(2, 'Math', 'B'),
(2, 'Science', 'A'),
(3, 'Math', 'C'),
(3, 'Science', 'B');

-- Insert example data into Transactions table
INSERT INTO Transactions (user_id, amount, date, description) VALUES
(3, 100.00, '2025-01-15', 'Tuition Fee'),
(3, 50.00, '2025-01-20', 'Library Fee');

-- Insert example data into LeaveRequests table
INSERT INTO LeaveRequests (teacher_name, leave_date, status) VALUES
('Teacher A', '2025-02-10', 'pending'),
('Teacher B', '2025-02-12', 'approved');

-- Insert example data into Invoices table
INSERT INTO Invoices (student_id, amount, status) VALUES
(1, 500.00, 'unpaid'),
(2, 500.00, 'paid'),
(3, 500.00, 'unpaid');