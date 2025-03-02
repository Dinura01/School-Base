<?php
$pageTitle = 'Add New Student';
$breadcrumbs = [
    'Dashboard' => '/principal/dashboard',
    'Student Management' => '/principal/students/view',
    'Add Student' => null
];
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h4>Add New Student</h4>
    </div>
    <div class="col-md-4 text-end">
        <a href="/principal/students/view" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Student List
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form id="addStudentForm" method="POST" action="/principal/students/add" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <!-- Student Information -->
                    <h5 class="card-title mb-4">Student Information</h5>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="firstName" class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="firstName" name="first_name" required>
                                <div class="invalid-feedback">Please enter first name</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="middleName" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="middleName" name="middle_name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="lastName" class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="lastName" name="last_name" required>
                                <div class="invalid-feedback">Please enter last name</div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="dob" class="form-label">Date of Birth *</label>
                                <input type="date" class="form-control" id="dob" name="dob" required>
                                <div class="invalid-feedback">Please select date of birth</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="gender" class="form-label">Gender *</label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                                <div class="invalid-feedback">Please select gender</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="bloodGroup" class="form-label">Blood Group</label>
                                <select class="form-select" id="bloodGroup" name="blood_group">
                                    <option value="">Select Blood Group</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="religion" class="form-label">Religion</label>
                                <input type="text" class="form-control" id="religion" name="religion">
                            </div>
                        </div>
                    </div>

                    <!-- Academic Information -->
                    <h5 class="card-title mb-4">Academic Information</h5>
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="grade" class="form-label">Grade *</label>
                                <select class="form-select" id="grade" name="grade" required>
                                    <option value="">Select Grade</option>
                                    <?php for($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?php echo $i; ?>">Grade <?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <div class="invalid-feedback">Please select grade</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="section" class="form-label">Section *</label>
                                <select class="form-select" id="section" name="section" required>
                                    <option value="">Select Section</option>
                                    <option value="A">Section A</option>
                                    <option value="B">Section B</option>
                                    <option value="C">Section C</option>
                                </select>
                                <div class="invalid-feedback">Please select section</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="rollNo" class="form-label">Roll Number *</label>
                                <input type="text" class="form-control" id="rollNo" name="roll_no" required>
                                <div class="invalid-feedback">Please enter roll number</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="admissionDate" class="form-label">Admission Date *</label>
                                <input type="date" class="form-control" id="admissionDate" name="admission_date" required>
                                <div class="invalid-feedback">Please select admission date</div>
                            </div>
                        </div>
                    </div>

                    <!-- Parent/Guardian Information -->
                    <h5 class="card-title mb-4">Parent/Guardian Information</h5>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="parentName" class="form-label">Parent/Guardian Name *</label>
                                <input type="text" class="form-control" id="parentName" name="parent_name" required>
                                <div class="invalid-feedback">Please enter parent/guardian name</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="relationship" class="form-label">Relationship *</label>
                                <select class="form-select" id="relationship" name="relationship" required>
                                    <option value="">Select Relationship</option>
                                    <option value="father">Father</option>
                                    <option value="mother">Mother</option>
                                    <option value="guardian">Guardian</option>
                                </select>
                                <div class="invalid-feedback">Please select relationship</div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="parentPhone" class="form-label">Phone Number *</label>
                                <input type="tel" class="form-control" id="parentPhone" name="parent_phone" required>
                                <div class="invalid-feedback">Please enter phone number</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="parentEmail" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="parentEmail" name="parent_email">
                                <div class="invalid-feedback">Please enter valid email address</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="parentOccupation" class="form-label">Occupation</label>
                                <input type="text" class="form-control" id="parentOccupation" name="parent_occupation">
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <h5 class="card-title mb-4">Contact Information</h5>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="address" class="form-label">Address *</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                                <div class="invalid-feedback">Please enter address</div>
                            </div>
                        </div>
                    </div>

                    <!-- Photo Upload -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="photo" class="form-label">Student Photo</label>
                                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                <div class="form-text">Upload a recent photo (Max size: 2MB)</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <img id="photoPreview" src="#" alt="Photo preview" style="max-width: 200px; display: none;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Student
                            </button>
                            <button type="reset" class="btn btn-secondary ms-2">
                                <i class="fas fa-undo me-2"></i>Reset Form
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('addStudentForm');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });

    // Photo preview
    const photo = document.getElementById('photo');
    const photoPreview = document.getElementById('photoPreview');
    
    photo.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB');
                this.value = '';
                photoPreview.style.display = 'none';
                return;
            }

            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('Please upload an image file');
                this.value = '';
                photoPreview.style.display = 'none';
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                photoPreview.src = e.target.result;
                photoPreview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });

    // Auto-generate roll number
    document.getElementById('grade').addEventListener('change', function() {
        const grade = this.value;
        const section = document.getElementById('section').value;
        if (grade && section) {
            generateRollNumber(grade, section);
        }
    });

    document.getElementById('section').addEventListener('change', function() {
        const grade = document.getElementById('grade').value;
        const section = this.value;
        if (grade && section) {
            generateRollNumber(grade, section);
        }
    });

    function generateRollNumber(grade, section) {
        // Make an AJAX call to get the next available roll number
        $.ajax({
            url: '/principal/students/get-next-roll',
            method: 'GET',
            data: { grade: grade, section: section },
            success: function(response) {
                if (response.success) {
                    document.getElementById('rollNo').value = response.roll_no;
                }
            }
        });
    }
});
</script>
