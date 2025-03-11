<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zen Flow - Daily Management System</title>
    <link rel="stylesheet" href="{{env('APP_ENV') === 'local' ? asset('assets/css/styles.css') : asset('public/assets/css/styles.css')}}">

    <style>
        /* Base styles */
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        /* Container styling with responsive width */
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            box-sizing: border-box;
        }

        /* Form styling */
        form {
            width: 100%;
            max-width: 400px;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Form group spacing */
        .form-group {
            margin-bottom: 20px;
        }

        /* Label styling */
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        /* Input styling */
        .form-input {
            width: 100%;
        }

        .form-input:focus {
            border-color: var(--primary);
            outline: none;
        }

        /* Button styling */
        .form-actions {
            margin-top: 24px;
        }

        .btn {
            padding: 12px 20px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            border: none;
        }

        /* Responsive adjustments for smaller screens */
        @media (max-width: 480px) {
            .container {
                padding: 15px;
            }

            form {
                padding: 20px;
            }

            .form-input {
                padding: 10px;
                font-size: 15px;
            }

            .btn {
                padding: 10px 16px;
                font-size: 15px;
            }
        }

        /* Notification styling */
        #notification {
            width: calc(100% - 40px);
            max-width: 400px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>

<div class="container">
    <form action="{{route('auth.login')}}" method="post">
        @csrf
        <div class="form-group">
            <label class="form-label" for="username">Username</label>
            <input type="text" class="form-input" id="username" name="username" placeholder="Enter your username" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" class="form-input" id="password" name="password" placeholder="Enter your password" required>
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
