<?php

include 'connection.php';

// Fetch classes from database
$class_query = "SELECT class_id, class_name FROM Class";
$class_result = $conn->query($class_query);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $class_id = mysqli_real_escape_string($conn, $_POST['class']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Insert query
    $sql = "INSERT INTO students (first_name, last_name, dob, contact_number, class_id, 
            address, gender, student_id, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssissss", $first_name, $last_name, $dob, $contact, $class_id, 
                      $address, $gender, $student_id, $password);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Student added successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="Adminpanel.css" rel="stylesheet">

</head>
<body>
    <div class="container mt-5">
        <h2>Add New Student</h2>
        <form method="POST" class="mt-4" id="studentForm" novalidate>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                    <div class="error-message"></div>
                </div>
                <div class="col-md-6">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                    <div class="error-message"></div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="dob" class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" id="dob" name="dob" required>
                    <div class="error-message"></div>
                </div>
                <div class="col-md-6">
                    <label for="contact" class="form-label">Contact Number</label>
                    <input type="tel" class="form-control" id="contact" name="contact" required>
                    <div class="error-message"></div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="class" class="form-label">Class</label>
                    <select class="form-select" id="class" name="class" required>
                        <option value="">Select Class</option>
                        <?php
                        if ($class_result->num_rows > 0) {
                            while($row = $class_result->fetch_assoc()) {
                                echo "<option value='" . $row['class_id'] . "'>" . $row['class_name'] . "</option>";
                            }
                        }
                        ?>
                    </select>
                    <div class="error-message"></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gender</label>
                    <div class="mt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="male" value="male" required>
                            <label class="form-check-label" for="male">Male</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="female" value="female">
                            <label class="form-check-label" for="female">Female</label>
                        </div>
                    </div>
                    <div class="error-message"></div>
                </div>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                <div class="error-message"></div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="student_id" class="form-label">Student ID</label>
                    <input type="text" class="form-control" id="student_id" name="student_id" required>
                    <div class="error-message"></div>
                </div>
                <div class="col-md-6">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div class="error-message"></div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Add Student</button>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('studentForm').addEventListener('submit', function(event) {
            event.preventDefault();
            let isValid = true;
            
            // Reset previous error states
            const errorFields = document.querySelectorAll('.error-field');
            errorFields.forEach(field => field.classList.remove('error-field'));
            const errorMessages = document.querySelectorAll('.error-message');
            errorMessages.forEach(message => message.textContent = '');

            // Validate each required field
            const requiredFields = [
                { id: 'first_name', name: 'First Name' },
                { id: 'last_name', name: 'Last Name' },
                { id: 'dob', name: 'Date of Birth' },
                { id: 'contact', name: 'Contact Number' },
                { id: 'class', name: 'Class' },
                { id: 'address', name: 'Address' },
                { id: 'student_id', name: 'Student ID' },
                { id: 'password', name: 'Password' }
            ];

            requiredFields.forEach(field => {
                const element = document.getElementById(field.id);
                if (!element.value.trim()) {
                    isValid = false;
                    element.classList.add('error-field');
                    element.nextElementSibling.textContent = `${field.name} is required`;
                }
            });

            // Validate gender
            const genderInputs = document.querySelectorAll('input[name="gender"]');
            const genderSelected = Array.from(genderInputs).some(input => input.checked);
            if (!genderSelected) {
                isValid = false;
                genderInputs.forEach(input => input.classList.add('error-field'));
                genderInputs[0].parentElement.parentElement.nextElementSibling.textContent = 'Gender is required';
            }

            // Additional validation for specific fields
            const contactInput = document.getElementById('contact');
            if (contactInput.value.trim() && !/^\d{10}$/.test(contactInput.value.trim())) {
                isValid = false;
                contactInput.classList.add('error-field');
                contactInput.nextElementSibling.textContent = 'Please enter a valid 10-digit contact number';
            }

            const passwordInput = document.getElementById('password');
            if (passwordInput.value.trim() && passwordInput.value.length < 6) {
                isValid = false;
                passwordInput.classList.add('error-field');
                passwordInput.nextElementSibling.textContent = 'Password must be at least 6 characters long';
            }

            // If all validations pass, submit the form
            if (isValid) {
                this.submit();
            }
        });

        // Remove error styling when user starts typing
        document.querySelectorAll('.form-control, .form-select, .form-check-input').forEach(element => {
            element.addEventListener('input', function() {
                this.classList.remove('error-field');
                if (this.nextElementSibling && this.nextElementSibling.classList.contains('error-message')) {
                    this.nextElementSibling.textContent = '';
                }
            });
        });
    </script>
</body>
</html>