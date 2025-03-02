<?php
$pageTitle = 'Add New Staff';
$breadcrumbs = [
    'Dashboard' => '/principal/dashboard',
    'Staff Management' => '/principal/staff/view',
    'Add Staff' => null
];
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h4>Add New Staff Member</h4>
    </div>
    <div class="col-md-4 text-end">
        <a href="/principal/staff/view" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Staff List
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form id="addStaffForm" method="POST" action="/principal/staff/add" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <!-- Personal Information -->
                    <h5 class="card-title mb-4">Personal Information</h5>
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
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="invalid-feedback">Please enter a valid email address</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number *</label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                                <div class="invalid-feedback">Please enter phone number</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="dob" class="form-label">Date of Birth *</label>
                                <input type="date" class="form-control" id="dob" name="dob" required>
                                <div class="invalid-feedback">Please select date of birth</div>
                            </div>
                        </div>
                    </div>

                    <!-- Employment Information -->
                    <h5 class="card-title mb-4">Employment Information</h5>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="role" class="form-label">Role *</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="teacher">Teacher</option>
                                    <option value="admin_staff">Administrative Staff</option>
                                    <option value="support_staff">Support Staff</option>
                                </select>
                                <div class="invalid-feedback">Please select a role</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="department" class="form-label">Department *</label>
                                <select class="form-select" id="department" name="department" required>
                                    <option value="">Select Department</option>
                                    <option value="english">English</option>
                                    <option value="mathematics">Mathematics</option>
                                    <option value="science">Science</option>
                                    <option value="social_studies">Social Studies</option>
                                    <option value="administration">Administration</option>
                                </select>
                                <div class="invalid-feedback">Please select a department</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="joinDate" class="form-label">Join Date *</label>
                                <input type="date" class="form-control" id="joinDate" name="join_date" required>
                                <div class="invalid-feedback">Please select join date</div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <h5 class="card-title mb-4">Additional Information</h5>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="qualification" class="form-label">Qualification *</label>
                                <input type="text" class="form-control" id="qualification" name="qualification" required>
                                <div class="invalid-feedback">Please enter qualification</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="experience" class="form-label">Experience (Years) *</label>
                                <input type="number" class="form-control" id="experience" name="experience" min="0" required>
                                <div class="invalid-feedback">Please enter years of experience</div>
                            </div>
                        </div>
                    </div>

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
                                <label for="photo" class="form-label">Staff Photo</label>
                                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                <div class="form-text">Upload a professional photo (Max size: 2MB)</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <img id="photoPreview" src="#" alt="Photo preview" style="max-width: 200px; display: none;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Staff Member
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
    const form = document.getElementById('addStaffForm');
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

    // Role-based department options
    const role = document.getElementById('role');
    const department = document.getElementById('department');
    
    role.addEventListener('change', function() {
        const selectedRole = this.value;
        department.innerHTML = ''; // Clear existing options
        
        // Add default option
        department.add(new Option('Select Department', ''));
        
        // Add role-specific departments
        if (selectedRole === 'teacher') {
            const departments = ['English', 'Mathematics', 'Science', 'Social Studies'];
            departments.forEach(dept => {
                department.add(new Option(dept, dept.toLowerCase()));
            });
        } else if (selectedRole === 'admin_staff') {
            const departments = ['Administration', 'Finance', 'HR'];
            departments.forEach(dept => {
                department.add(new Option(dept, dept.toLowerCase()));
            });
        } else if (selectedRole === 'support_staff') {
            const departments = ['Maintenance', 'IT Support', 'Library'];
            departments.forEach(dept => {
                department.add(new Option(dept, dept.toLowerCase()));
            });
        }
    });
});
</script>
