<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zen Flow - Daily Management System</title>
    <style></style>
    <link rel="stylesheet" href="{{asset('assets/css/styles.css')}}">

    <style>
        .container{
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>


<div class="container">
    <form style="width: 400px" action="{{route('auth.login')}}" method="post">
        @csrf
        <div class="form-group">
            <label class="form-label" for="username">Username</label>
            <input type="text" class="form-input" id="username" name="username" placeholder="e.g john" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" class="form-input" id="password" name="password" placeholder="e.g john" required>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary" id="save-journal">Login</button>
        </div>
    </form>
</div>


<!-- Confirmation Modal -->
<div class="modal" id="confirm-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Confirm Action</h2>
            <button class="close-modal" id="close-confirm-modal">&times;</button>
        </div>
        <p id="confirm-message">Are you sure you want to delete this item?</p>
        <div class="form-actions">
            <button type="button" class="btn btn-outline" id="cancel-confirm">Cancel</button>
            <button type="button" class="btn btn-primary" id="confirm-action">Confirm</button>
        </div>
    </div>
</div>

<!-- Notification -->
<div id="notification"
     style="display: none; position: fixed; bottom: 20px; right: 20px; background-color: var(--primary); color: white; padding: 15px 20px; border-radius: 6px; box-shadow: var(--shadow); z-index: 1000; transition: var(--transition);">
    <div id="notification-content">Task saved successfully!</div>
</div>

<!-- Placeholder for JavaScript code -->
<script>
    // Modal Functionality
    document.addEventListener('DOMContentLoaded', function () {
        // Notification
        const notification = document.getElementById('notification');
        const notificationContent = document.getElementById('notification-content');


        function openModal(modal) {
            modal.style.display = 'flex';
            // Add a small delay before adding the class to ensure the transition works
            setTimeout(() => {
                modal.classList.add('active');
            }, 10);
        }

        function closeModal(modal) {
            modal.classList.remove('active');
            // Add a delay to match the transition duration
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }

        function showNotification(message, type) {
            notificationContent.textContent = message;
            notification.style.display = 'block';
            notification.classList.add(type)

            // Hide notification after 3 seconds
            setTimeout(() => {
                notification.style.display = 'none';
                notification.classList.remove(type)
            }, 3000);
        }


        // Update recent tasks on dashboard
        // function updateRecentTasks() {
        //     const recentTasksContainer = document.getElementById('recent-tasks-container');
        //     const tasks = Array.from(document.querySelectorAll('.task-item')).slice(0, 5);
        //
        //     recentTasksContainer.innerHTML = '';
        //
        //     if (tasks.length === 0) {
        //         recentTasksContainer.innerHTML = '<div class="activity-item">No tasks yet</div>';
        //         return;
        //     }
        //
        //     tasks.forEach(task => {
        //         const taskText = task.querySelector('.task-text').textContent;
        //         const taskPriority = task.getAttribute('data-priority');
        //         const isCompleted = task.classList.contains('completed');
        //
        //         const activityItem = document.createElement('div');
        //         activityItem.className = 'activity-item';
        //
        //         activityItem.innerHTML = `
        //         <div class="activity-dot activity-task" style="background-color: ${getPriorityColor(taskPriority)}"></div>
        //         <div class="activity-content">
        //             <div class="activity-title">${taskText}</div>
        //             <div class="activity-meta">${isCompleted ? 'Completed' : 'Active'} • ${taskPriority} priority</div>
        //         </div>
        //     `;
        //
        //         recentTasksContainer.appendChild(activityItem);
        //     });
        // }

        // Update recent journals on dashboard
        // function updateRecentJournals() {
        //     const recentJournalsContainer = document.getElementById('recent-journals-container');
        //     const journals = Array.from(document.querySelectorAll('.journal-card')).slice(0, 5);
        //
        //     recentJournalsContainer.innerHTML = '';
        //
        //     if (journals.length === 0) {
        //         recentJournalsContainer.innerHTML = '<div class="activity-item">No journal entries yet</div>';
        //         return;
        //     }
        //
        //     journals.forEach(journal => {
        //         const journalTitle = journal.querySelector('.journal-title').textContent;
        //         const journalDate = journal.querySelector('.journal-date').textContent;
        //
        //         const activityItem = document.createElement('div');
        //         activityItem.className = 'activity-item';
        //
        //         activityItem.innerHTML = `
        //         <div class="activity-dot activity-journal"></div>
        //         <div class="activity-content">
        //             <div class="activity-title">${journalTitle}</div>
        //             <div class="activity-meta">${journalDate}</div>
        //         </div>
        //     `;
        //
        //         recentJournalsContainer.appendChild(activityItem);
        //     });
        // }

        @if(session('error'))
            showNotification("{{session('error')}}", 'danger');
        @endif

        @if(session('success'))
            showNotification("{{session('success')}}", 'success');
        @endif

        // Helper function to format a date
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {month: 'short', day: 'numeric'});
        }

        // Helper function to format a date and time
        function formatDateTime(dateTimeString) {
            const date = new Date(dateTimeString);
            return date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // Helper function to get priority color
        function getPriorityColor(priority) {
            switch (priority) {
                case 'high':
                    return 'tomato';
                case 'medium':
                    return 'orange';
                case 'low':
                    return 'seagreen';
                default:
                    return 'var(--primary)';
            }
        }

        // Add CSS transition for modals
        const style = document.createElement('style');
        style.textContent = `
        .modal {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal.active {
            opacity: 1;
        }

        .modal-content {
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }

        .modal.active .modal-content {
            transform: translateY(0);
        }
    `;
        document.head.appendChild(style);
    });
</script>
</body>
</html>
