<?php
$pageTitle = 'School Calendar';
$breadcrumbs = ['Dashboard' => '/principal/dashboard', 'Calendar' => null];
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h4>School Calendar</h4>
    </div>
    <div class="col-md-4 text-end">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
            <i class="fas fa-plus me-2"></i>Add Event
        </button>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Event Categories</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="academicEvents" checked>
                        <label class="form-check-label" for="academicEvents">
                            <span class="badge bg-primary me-2">●</span>Academic
                        </label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="examEvents" checked>
                        <label class="form-check-label" for="examEvents">
                            <span class="badge bg-danger me-2">●</span>Exams
                        </label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="holidayEvents" checked>
                        <label class="form-check-label" for="holidayEvents">
                            <span class="badge bg-success me-2">●</span>Holidays
                        </label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="activityEvents" checked>
                        <label class="form-check-label" for="activityEvents">
                            <span class="badge bg-warning me-2">●</span>Activities
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Upcoming Events</h6>
            </div>
            <div class="card-body">
                <div class="upcoming-events">
                    <?php foreach ($upcomingEvents as $event): ?>
                    <div class="event-item mb-3">
                        <div class="d-flex align-items-center">
                            <div class="event-date text-center me-3">
                                <div class="date-box bg-light rounded p-2">
                                    <div class="month text-uppercase small"><?php echo date('M', strtotime($event['start_date'])); ?></div>
                                    <div class="day h4 mb-0"><?php echo date('d', strtotime($event['start_date'])); ?></div>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1"><?php echo htmlspecialchars($event['title']); ?></h6>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    <?php echo date('g:i A', strtotime($event['start_date'])); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="card">
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addEventForm">
                    <div class="mb-3">
                        <label for="eventTitle" class="form-label">Event Title *</label>
                        <input type="text" class="form-control" id="eventTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="eventCategory" class="form-label">Category *</label>
                        <select class="form-select" id="eventCategory" required>
                            <option value="academic">Academic</option>
                            <option value="exam">Exam</option>
                            <option value="holiday">Holiday</option>
                            <option value="activity">Activity</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="startDate" class="form-label">Start Date *</label>
                                <input type="date" class="form-control" id="startDate" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="startTime" class="form-label">Start Time</label>
                                <input type="time" class="form-control" id="startTime">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="endDate" class="form-label">End Date *</label>
                                <input type="date" class="form-control" id="endDate" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="endTime" class="form-label">End Time</label>
                                <input type="time" class="form-control" id="endTime">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="eventDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="eventDescription" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveEvent()">Save Event</button>
            </div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventDetailsTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="eventDetailsContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="deleteEvent()">Delete</button>
                <button type="button" class="btn btn-primary" onclick="editEvent()">Edit</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: '/principal/calendar/events',
        editable: true,
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true,
        select: function(arg) {
            $('#startDate').val(arg.startStr);
            $('#endDate').val(arg.endStr);
            $('#addEventModal').modal('show');
        },
        eventClick: function(arg) {
            showEventDetails(arg.event);
        },
        eventDrop: function(arg) {
            updateEventDates(arg.event);
        }
    });
    calendar.render();
});

function saveEvent() {
    const formData = {
        title: $('#eventTitle').val(),
        category: $('#eventCategory').val(),
        start_date: $('#startDate').val(),
        start_time: $('#startTime').val(),
        end_date: $('#endDate').val(),
        end_time: $('#endTime').val(),
        description: $('#eventDescription').val()
    };

    $.ajax({
        url: '/principal/calendar/add',
        method: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                $('#addEventModal').modal('hide');
                $('#addEventForm')[0].reset();
                calendar.refetchEvents();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('An error occurred while saving the event.');
        }
    });
}

function showEventDetails(event) {
    $('#eventDetailsTitle').text(event.title);
    $('#eventDetailsContent').html(`
        <p><strong>Category:</strong> ${event.extendedProps.category}</p>
        <p><strong>Start:</strong> ${event.start.toLocaleString()}</p>
        <p><strong>End:</strong> ${event.end ? event.end.toLocaleString() : 'N/A'}</p>
        <p><strong>Description:</strong> ${event.extendedProps.description || 'No description'}</p>
    `);
    $('#eventDetailsModal').modal('show');
}

function updateEventDates(event) {
    $.ajax({
        url: '/principal/calendar/update',
        method: 'POST',
        data: {
            id: event.id,
            start: event.start,
            end: event.end
        },
        error: function() {
            alert('An error occurred while updating the event.');
            calendar.refetchEvents();
        }
    });
}

function deleteEvent() {
    if (confirm('Are you sure you want to delete this event?')) {
        // Add delete logic here
    }
}

function editEvent() {
    // Add edit logic here
}
</script>
