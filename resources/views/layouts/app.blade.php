<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Zen Flow - Daily Management System</title>
    <link rel="stylesheet" href="{{asset('public/assets/css/styles.css')}}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    @stack('styles')
</head>
<body>
<!-- Hamburger Menu Button -->


<!-- Overlay for when sidebar is active on mobile -->
<div class="sidebar-overlay" id="sidebar-overlay"></div>

<div class="container">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <ul class="nav-links">
            <li class="nav-item">
                <a href="{{route('home')}}" class="nav-link {{request()->is('/') ? 'active' : ''}}" data-view="dashboard">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="lucide lucide-layout-dashboard">
                        <rect width="7" height="9" x="3" y="3" rx="1"/>
                        <rect width="7" height="5" x="14" y="3" rx="1"/>
                        <rect width="7" height="9" x="14" y="12" rx="1"/>
                        <rect width="7" height="5" x="3" y="16" rx="1"/>
                    </svg>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('tasks.index')}}" class="nav-link {{request()->is('tasks') ? 'active' : ''}}" data-view="tasks">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="lucide lucide-list-todo">
                        <rect x="3" y="5" width="6" height="6" rx="1"/>
                        <path d="m3 17 2 2 4-4"/>
                        <path d="M13 6h8"/>
                        <path d="M13 12h8"/>
                        <path d="M13 18h8"/>
                    </svg>
                    <span>Tasks</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('journals.index')}}" class="nav-link {{request()->is('journals') ? 'active' : ''}}" data-view="journal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="lucide lucide-notebook-pen">
                        <path d="M13.4 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-7.4"/>
                        <path d="M2 6h4"/>
                        <path d="M2 10h4"/>
                        <path d="M2 14h4"/>
                        <path d="M2 18h4"/>
                        <path d="M21.378 5.626a1 1 0 1 0-3.004-3.004l-5.01 5.012a2 2 0 0 0-.506.854l-.837 2.87a.5.5 0 0 0 .62.62l2.87-.837a2 2 0 0 0 .854-.506z"/>
                    </svg>
                    <span>Journal</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('random-pickers.index')}}" class="nav-link {{request()->is('random-pickers') ? 'active' : ''}}" data-view="journal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="lucide lucide-dices">
                        <rect width="12" height="12" x="2" y="10" rx="2" ry="2"/>
                        <path d="m17.92 14 3.5-3.5a2.24 2.24 0 0 0 0-3l-5-4.92a2.24 2.24 0 0 0-3 0L10 6"/>
                        <path d="M6 18h.01"/>
                        <path d="M10 14h.01"/>
                        <path d="M15 6h.01"/>
                        <path d="M18 9h.01"/>
                    </svg>
                    <span>Do Things?</span>
                </a>
            </li>
        </ul>

        <div style="width: 80%; margin: 0px auto">
            <a href="{{route('auth.logout')}}" class="btn btn-primary">Logout</a>
        </div>
    </div>

    <div class="main-content" id="main-content">
        @yield('content')
    </div>
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
<div id="notification" style="display: none; position: fixed; bottom: 20px; right: 20px; background-color: var(--primary); color: white; padding: 15px 20px; border-radius: 6px; box-shadow: var(--shadow); z-index: 1000; transition: var(--transition);">
    <div id="notification-content">Task saved successfully!</div>
</div>

<!-- JavaScript code -->
<script>
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
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
        switch(priority) {
            case 'high': return 'tomato';
            case 'medium': return 'orange';
            case 'low': return 'seagreen';
            default: return 'var(--primary)';
        }
    }

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
        const notification = document.getElementById('notification');
        const notificationContent = document.getElementById('notification-content');

        notificationContent.textContent = message;
        notification.style.display = 'block';
        notification.classList.add(type)

        // Hide notification after 3 seconds
        setTimeout(() => {
            notification.style.display = 'none';
            notification.classList.remove(type)
        }, 3000);
    }

    // Modal Functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Hamburger menu functionality
        const hamburgerMenu = document.getElementById('hamburger-menu');
        const hamburgerIcon = hamburgerMenu.querySelector('.hamburger-icon');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        // Toggle sidebar
        hamburgerMenu.addEventListener('click', function() {
            hamburgerIcon.classList.toggle('open');
            sidebar.classList.toggle('active');
            mainContent.classList.toggle('shifted');
            sidebarOverlay.classList.toggle('active');
        });

        // Close sidebar when clicking outside
        sidebarOverlay.addEventListener('click', function() {
            hamburgerIcon.classList.remove('open');
            sidebar.classList.remove('active');
            mainContent.classList.remove('shifted');
            sidebarOverlay.classList.remove('active');
        });

        // Close sidebar when clicking a nav link (on mobile)
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    hamburgerIcon.classList.remove('open');
                    sidebar.classList.remove('active');
                    mainContent.classList.remove('shifted');
                    sidebarOverlay.classList.remove('active');
                }
            });
        });

        // Notification
        @if(session('error'))
        showNotification("{{session('error')}}", 'danger');
        @endif

        @if(session('success'))
        showNotification("{{session('success')}}", 'success');
        @endif

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

        const currentDateElement = document.getElementById('current-date');
        if (currentDateElement) {
            const now = new Date();
            const options = {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'};
            currentDateElement.textContent = now.toLocaleDateString('en-US', options);
        }
    });
</script>

@stack('scripts')
</body>
</html>
