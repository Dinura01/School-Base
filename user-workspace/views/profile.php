<?php
$pageTitle = 'My Profile';
$breadcrumbs = ['Dashboard' => '/' . Session::getUserRole() . '/dashboard', 'Profile' => null];

$user = $auth->getCurrentUser();
?>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body text-center">
                <?php if (!empty($user['photo'])): ?>
                    <img src="<?php echo htmlspecialchars($user['photo']); ?>" 
                         class="rounded-circle mb-3" 
                         style="width: 150px; height: 150px; object-fit: cover;"
                         alt="Profile photo">
                <?php else: ?>
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width: 150px; height: 150px; font-size: 64px;">
                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
                
                <h5 class="mb-1"><?php echo htmlspecialchars($user['name']); ?></h5>
                <p class="text-muted mb-3"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></p>
                
                <button class="btn btn-primary btn-sm" 
                        onclick="document.getElementById('photoInput').click()">
                    <i class="fas fa-camera me-2"></i>Change Photo
                </button>
                <input type="file" id="photoInput" style="display: none;" 
                       accept="image/*" onchange="updatePhoto(this)">
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title mb-3">Account Information</h6>
                <div class="mb-3">
                    <label class="small text-muted">User ID</label>
                    <div><?php echo $user['id']; ?></div>
                </div>
                <div class="mb-3">
                    <label class="small text-muted">Role</label>
                    <div><?php echo ucfirst($user['role']); ?></div>
                </div>
                <div class="mb-3">
                    <label class="small text-muted">Join Date</label>
                    <div><?php echo date('M d, Y', strtotime($user['created_at'])); ?></div>
                </div>
                <div class="mb-3">
                    <label class="small text-muted">Last Login</label>
                    <div><?php echo date('M d, Y H:i', strtotime($user['last_login'])); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Profile</h5>
            </div>
            <div class="card-body">
                <form id="profileForm" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name"
                               value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        <div class="invalid-feedback">Please enter your name</div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        <div class="invalid-feedback">Please enter a valid email address</div>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone"
                               value="<?php echo htmlspecialchars($user['phone']); ?>">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Change Password</h5>
            </div>
            <div class="card-body">
                <form id="passwordForm" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Current Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="currentPassword" 
                                   name="current_password" required>
                            <button class="btn btn-outline-secondary" type="button" 
                                    onclick="togglePassword('currentPassword')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback">Please enter your current password</div>
                    </div>

                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="newPassword" 
                                   name="new_password" required minlength="8">
                            <button class="btn btn-outline-secondary" type="button" 
                                    onclick="togglePassword('newPassword')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback">Password must be at least 8 characters long</div>
                    </div>

                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirmPassword" 
                                   name="confirm_password" required>
                            <button class="btn btn-outline-secondary" type="button" 
                                    onclick="togglePassword('confirmPassword')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback">Passwords do not match</div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-key me-2"></i>Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.getElementById('profileForm');
    const passwordForm = document.getElementById('passwordForm');

    profileForm.addEventListener('submit', function(event) {
        event.preventDefault();
        if (!this.checkValidity()) {
            event.stopPropagation();
        } else {
            updateProfile();
        }
        this.classList.add('was-validated');
    });

    passwordForm.addEventListener('submit', function(event) {
        event.preventDefault();
        if (!this.checkValidity()) {
            event.stopPropagation();
        } else {
            updatePassword();
        }
        this.classList.add('was-validated');
    });

    // Password confirmation validation
    const newPassword = document.getElementById('newPassword');
    const confirmPassword = document.getElementById('confirmPassword');
    
    confirmPassword.addEventListener('input', function() {
        if (this.value !== newPassword.value) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });
});

function updateProfile() {
    const formData = new FormData(document.getElementById('profileForm'));
    
    $.ajax({
        url: '/profile/update',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                showAlert('success', 'Profile updated successfully');
            } else {
                showAlert('error', response.message);
            }
        },
        error: function() {
            showAlert('error', 'An error occurred while updating profile');
        }
    });
}

function updatePassword() {
    const formData = new FormData(document.getElementById('passwordForm'));
    
    $.ajax({
        url: '/profile/change-password',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                showAlert('success', 'Password changed successfully');
                document.getElementById('passwordForm').reset();
            } else {
                showAlert('error', response.message);
            }
        },
        error: function() {
            showAlert('error', 'An error occurred while changing password');
        }
    });
}

function updatePhoto(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            showAlert('error', 'File size must be less than 2MB');
            return;
        }

        // Validate file type
        if (!file.type.startsWith('image/')) {
            showAlert('error', 'Please upload an image file');
            return;
        }

        const formData = new FormData();
        formData.append('photo', file);

        $.ajax({
            url: '/profile/update-photo',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function() {
                showAlert('error', 'An error occurred while updating photo');
            }
        });
    }
}

function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    const alertContainer = document.createElement('div');
    alertContainer.innerHTML = alertHtml;
    document.querySelector('.container').insertBefore(
        alertContainer,
        document.querySelector('.container').firstChild
    );

    // Auto-hide alert after 5 seconds
    setTimeout(() => {
        alertContainer.querySelector('.alert').remove();
    }, 5000);
}
</script>
