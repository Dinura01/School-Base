<?php
$pageTitle = 'Settings';
$breadcrumbs = ['Dashboard' => '/' . Session::getUserRole() . '/dashboard', 'Settings' => null];

$user = $auth->getCurrentUser();
?>

<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-body">
                <div class="nav flex-column nav-pills" role="tablist">
                    <button class="nav-link active mb-2" data-bs-toggle="pill" data-bs-target="#notifications">
                        <i class="fas fa-bell me-2"></i>Notifications
                    </button>
                    <button class="nav-link mb-2" data-bs-toggle="pill" data-bs-target="#preferences">
                        <i class="fas fa-cog me-2"></i>Preferences
                    </button>
                    <button class="nav-link mb-2" data-bs-toggle="pill" data-bs-target="#privacy">
                        <i class="fas fa-shield-alt me-2"></i>Privacy
                    </button>
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#security">
                        <i class="fas fa-lock me-2"></i>Security
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="tab-content">
            <!-- Notifications Settings -->
            <div class="tab-pane fade show active" id="notifications">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Notification Settings</h5>
                    </div>
                    <div class="card-body">
                        <form id="notificationForm">
                            <h6 class="mb-3">Email Notifications</h6>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="emailNews" checked>
                                    <label class="form-check-label" for="emailNews">
                                        School News and Updates
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="emailEvents" checked>
                                    <label class="form-check-label" for="emailEvents">
                                        Event Reminders
                                    </label>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="emailReports">
                                    <label class="form-check-label" for="emailReports">
                                        Report Notifications
                                    </label>
                                </div>
                            </div>

                            <h6 class="mb-3">System Notifications</h6>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="systemAlerts" checked>
                                    <label class="form-check-label" for="systemAlerts">
                                        System Alerts
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="loginAlerts" checked>
                                    <label class="form-check-label" for="loginAlerts">
                                        Login Alerts
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Notification Settings
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Preferences Settings -->
            <div class="tab-pane fade" id="preferences">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Preferences</h5>
                    </div>
                    <div class="card-body">
                        <form id="preferencesForm">
                            <div class="mb-3">
                                <label class="form-label">Language</label>
                                <select class="form-select">
                                    <option value="en">English</option>
                                    <option value="es">Spanish</option>
                                    <option value="fr">French</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Time Zone</label>
                                <select class="form-select">
                                    <option value="UTC">UTC</option>
                                    <option value="EST">Eastern Time</option>
                                    <option value="CST">Central Time</option>
                                    <option value="PST">Pacific Time</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Date Format</label>
                                <select class="form-select">
                                    <option value="MM/DD/YYYY">MM/DD/YYYY</option>
                                    <option value="DD/MM/YYYY">DD/MM/YYYY</option>
                                    <option value="YYYY-MM-DD">YYYY-MM-DD</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Preferences
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Privacy Settings -->
            <div class="tab-pane fade" id="privacy">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Privacy Settings</h5>
                    </div>
                    <div class="card-body">
                        <form id="privacyForm">
                            <div class="mb-3">
                                <label class="form-label">Profile Visibility</label>
                                <select class="form-select">
                                    <option value="public">Public</option>
                                    <option value="private">Private</option>
                                    <option value="school">School Only</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="showEmail">
                                    <label class="form-check-label" for="showEmail">
                                        Show email address to other users
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="showPhone">
                                    <label class="form-check-label" for="showPhone">
                                        Show phone number to other users
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Privacy Settings
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="tab-pane fade" id="security">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Security Settings</h5>
                    </div>
                    <div class="card-body">
                        <form id="securityForm">
                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="twoFactor">
                                    <label class="form-check-label" for="twoFactor">
                                        Enable Two-Factor Authentication
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="loginNotification" checked>
                                    <label class="form-check-label" for="loginNotification">
                                        Email notification on new login
                                    </label>
                                </div>
                            </div>

                            <h6 class="mb-3">Active Sessions</h6>
                            <div class="table-responsive mb-4">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Device</th>
                                            <th>Location</th>
                                            <th>Last Active</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Chrome - Windows</td>
                                            <td>New York, USA</td>
                                            <td>Now</td>
                                            <td>Current Session</td>
                                        </tr>
                                        <tr>
                                            <td>Safari - iPhone</td>
                                            <td>Los Angeles, USA</td>
                                            <td>2 days ago</td>
                                            <td>
                                                <button class="btn btn-sm btn-danger">
                                                    Revoke
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Security Settings
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle form submissions
    const forms = ['notificationForm', 'preferencesForm', 'privacyForm', 'securityForm'];
    
    forms.forEach(formId => {
        document.getElementById(formId).addEventListener('submit', function(e) {
            e.preventDefault();
            saveSettings(formId);
        });
    });
});

function saveSettings(formId) {
    const formData = new FormData(document.getElementById(formId));
    
    $.ajax({
        url: '/settings/save',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                showAlert('success', 'Settings saved successfully');
            } else {
                showAlert('error', response.message);
            }
        },
        error: function() {
            showAlert('error', 'An error occurred while saving settings');
        }
    });
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
