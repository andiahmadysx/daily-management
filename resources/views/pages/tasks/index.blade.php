@extends('layouts.app')

@section('content')
    <div id="tasks-view" style="display: block">
        <div class="header">
            <div class="ham-wrapper">
                <div class="hamburger-menu" id="hamburger-menu">
                    <div class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>

                <h1 class="section-title">
                    Task Management
                </h1>
            </div>

            <div class="date" id="current-date">March 6, 2025</div>

        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Add New Task</h2>
            </div>
            <form class="task-form" id="task-form">
                <input type="text" class="task-input" id="task-input" placeholder="What needs to be done?">
                <button type="submit" class="add-btn btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="lucide lucide-plus">
                        <path d="M5 12h14"/>
                        <path d="M12 5v14"/>
                    </svg>
                    Add Task
                </button>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">My Tasks</h2>
            </div>
            <div class="task-filters">
                <button class="filter-btn active" data-filter="all">All</button>
                <button class="filter-btn" data-filter="active">Active</button>
                <button class="filter-btn" data-filter="completed">Completed</button>
                <button class="filter-btn" data-filter="high">High Priority</button>
                <button class="filter-btn" data-filter="today">Due Today</button>
            </div>
            <ul class="task-list" id="task-list">
                <!-- Tasks will be loaded here -->
                <span class="loader" style="display: none"></span>
            </ul>
        </div>
    </div>
@endsection

<div class="modal" id="task-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title" id="task-modal-title">Add Task</h2>
            <button class="close-modal" id="close-task-modal">&times;</button>
        </div>
        <form id="task-detail-form">
            <input type="hidden" id="task-id">
            <div class="form-group">
                <label class="form-label" for="task-title">Task Title</label>
                <input type="text" class="form-input" id="task-title" placeholder="Enter task title" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="task-description">Description (Optional)</label>
                <textarea class="form-textarea" id="task-description" placeholder="Enter task description"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label" for="task-priority">Priority</label>
                <select class="form-select" id="task-priority">
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="task-due-date">Due Date</label>
                <input type="date" class="form-input" id="task-due-date">
            </div>
            <div class="form-group">
                <label class="form-label" for="task-repeat">Repeat</label>
                <select class="form-select" id="task-repeat">
                    <option value="none">Never</option>
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-outline" id="cancel-task">Cancel</button>
                <button type="submit" class="btn btn-primary" id="save-task">Save Task</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const elements = {

                taskModal: document.getElementById('task-modal'),
                confirmModal: document.getElementById('confirm-modal'),


                taskForm: document.getElementById('task-detail-form'),
                taskInput: document.getElementById('task-input'),
                taskId: document.getElementById('task-id'),
                taskTitle: document.getElementById('task-title'),
                taskDescription: document.getElementById('task-description'),
                taskPriority: document.getElementById('task-priority'),
                taskDueDate: document.getElementById('task-due-date'),
                taskRepeat: document.getElementById('task-repeat'),
                taskModalTitle: document.getElementById('task-modal-title'),


                addTaskBtn: document.querySelector('.add-btn'),
                closeTaskModal: document.getElementById('close-task-modal'),
                cancelTask: document.getElementById('cancel-task'),


                taskList: document.getElementById('task-list'),
                filterButtons: document.querySelectorAll('.filter-btn'),


                loading: document.querySelector('.loader')
            };


            init();


            elements.taskForm.addEventListener('submit', handleTaskFormSubmit);


            elements.addTaskBtn.addEventListener('click', handleAddTaskClick);
            elements.closeTaskModal.addEventListener('click', () => closeModal(elements.taskModal));
            elements.cancelTask.addEventListener('click', () => closeModal(elements.taskModal));


            elements.filterButtons.forEach(button => {
                button.addEventListener('click', handleFilterClick);
            });


            function init() {
                loadTasks();
                setupDateDefaults();
            }


            function setupDateDefaults() {
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                elements.taskDueDate.valueAsDate = tomorrow;
            }


            function handleAddTaskClick(e) {
                e.preventDefault();
                elements.taskModalTitle.textContent = 'Add Task';
                elements.taskId.value = '';
                elements.taskTitle.value = elements.taskInput.value || '';
                elements.taskDescription.value = '';
                elements.taskPriority.value = 'medium';
                elements.taskRepeat.value = 'none';
                setupDateDefaults();
                openModal(elements.taskModal);
            }


            function handleTaskFormSubmit(e) {
                e.preventDefault();

                const taskData = {
                    title: elements.taskTitle.value,
                    description: elements.taskDescription.value,
                    priority: elements.taskPriority.value,
                    due_date: elements.taskDueDate.value,
                    repeat: elements.taskRepeat.value
                };

                const taskId = elements.taskId.value;

                if (taskId) {
                    updateTask(taskId, taskData);
                } else {
                    createTask(taskData);
                }
            }


            function handleFilterClick() {

                elements.filterButtons.forEach(btn => btn.classList.remove('active'));


                this.classList.add('active');


                const filter = this.getAttribute('data-filter');
                filterTasks(filter);
            }


            function loadTasks() {
                elements.loading.style.display = 'block';

                $.ajax({
                    url: '{{ route("tasks.getData") }}',
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {

                        elements.taskList.innerHTML = '';


                        response.forEach(task => {
                            addTaskToList(task);
                        });

                        updateRecentTasks();
                    },
                    error: function (xhr, status, error) {
                        showNotification('Error loading tasks: ' + error, 'error');
                        console.error("Error:", error);
                    },
                    complete: function () {
                        elements.loading.style.display = 'none';
                    }
                });
            }


            function createTask(taskData) {
                elements.loading.style.display = 'block';

                $.ajax({
                    url: '{{ route("tasks.store") }}',
                    type: 'POST',
                    dataType: 'json',
                    data: taskData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        closeModal(elements.taskModal);
                        addTaskToList(response);
                        elements.taskInput.value = '';
                        showNotification('Task created successfully!', 'success');
                        updateRecentTasks();
                    },
                    error: function (xhr) {
                        handleFormErrors(xhr);
                    },
                    complete: function () {
                        elements.loading.style.display = 'none';
                    }
                });
            }


            function updateTask(taskId, taskData) {
                elements.loading.style.display = 'block';

                $.ajax({
                    url: '{{ route("tasks.update", ":id") }}'.replace(':id', taskId),
                    type: 'PUT',
                    dataType: 'json',
                    data: taskData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        closeModal(elements.taskModal);
                        updateTaskInList(response);
                        showNotification('Task updated successfully!', 'success');
                        updateRecentTasks();
                    },
                    error: function (xhr) {
                        handleFormErrors(xhr);
                    },
                    complete: function () {
                        elements.loading.style.display = 'none';
                    }
                });
            }


            window.editTask = function (taskId) {
                elements.loading.style.display = 'block';

                $.ajax({
                    url: '{{ route("tasks.show", ":id") }}'.replace(':id', taskId),
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (task) {
                        elements.taskModalTitle.textContent = 'Edit Task';
                        elements.taskId.value = task.id;
                        elements.taskTitle.value = task.title;
                        elements.taskDescription.value = task.description || '';
                        elements.taskPriority.value = task.priority;
                        elements.taskDueDate.value = task.due_date;
                        elements.taskRepeat.value = task.repeat;

                        openModal(elements.taskModal);
                    },
                    error: function (xhr, status, error) {
                        showNotification('Error fetching task details: ' + error, 'error');
                    },
                    complete: function () {
                        elements.loading.style.display = 'none';
                    }
                });
            };


            window.deleteTask = function (taskId) {
                showConfirmation('Are you sure you want to delete this task?', function () {
                    elements.loading.style.display = 'block';

                    $.ajax({
                        url: '{{ route("tasks.destroy", ":id") }}'.replace(':id', taskId),
                        type: 'DELETE',
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            const taskElement = document.querySelector(`.task-item[data-id="${taskId}"]`);
                            if (taskElement) {
                                taskElement.remove();
                            }

                            showNotification('Task deleted successfully!', 'success');
                            updateRecentTasks();
                        },
                        error: function (xhr, status, error) {
                            showNotification('Error deleting task: ' + error, 'error');
                        },
                        complete: function () {
                            elements.loading.style.display = 'none';
                        }
                    });
                });
            };


            window.toggleStatus = function (taskId, isCompleted) {
                $.ajax({
                    url: '{{ route("tasks.toggleStatus", ":task_id") }}'.replace(':task_id', taskId),
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        is_completed: isCompleted ? 1 : 0,
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        showNotification("Task status updated", 'success');
                        updateRecentTasks();
                    },
                    error: function (xhr, status, error) {
                        showNotification('Error updating task status: ' + error, 'error');
                        console.error("Error:", error);
                    }
                });
            };


            function addTaskToList(task) {
                const taskItem = document.createElement('li');
                taskItem.className = 'task-item' + (task.is_completed ? ' completed' : '');
                taskItem.setAttribute('data-id', task.id);
                taskItem.setAttribute('data-priority', task.priority);

                taskItem.innerHTML = `
            <div class="task-content">
                <input type="checkbox" class="task-check" ${task.is_completed ? 'checked' : ''}>
                <div class="task-text">${task.title}</div>
                <span class="task-date">${formatDate(task.due_date)}</span>
                <span class="task-priority priority-${task.priority}">${task.priority}</span>
            </div>
            <div class="task-actions">
                <button class="task-btn edit" onclick="editTask('${task.id}')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen"><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"/></svg>
                </button>
                <button class="task-btn delete" onclick="deleteTask('${task.id}')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                </button>
            </div>
        `;


                const checkbox = taskItem.querySelector('.task-check');
                checkbox.addEventListener('change', function () {
                    taskItem.classList.toggle('completed');
                    toggleStatus(task.id, checkbox.checked);
                });


                elements.taskList.appendChild(taskItem);
            }


            function updateTaskInList(task) {
                const taskItem = document.querySelector(`.task-item[data-id="${task.id}"]`);

                if (!taskItem) {

                    addTaskToList(task);
                    return;
                }


                taskItem.setAttribute('data-priority', task.priority);


                const taskText = taskItem.querySelector('.task-text');
                const taskDate = taskItem.querySelector('.task-date');
                const taskPriority = taskItem.querySelector('.task-priority');

                taskText.textContent = task.title;
                taskDate.textContent = formatDate(task.due_date);
                taskPriority.textContent = task.priority;
                taskPriority.className = `task-priority priority-${task.priority}`;
            }


            function filterTasks(filter) {
                const taskItems = document.querySelectorAll('.task-item');

                taskItems.forEach(item => {
                    const isCompleted = item.classList.contains('completed');
                    const priority = item.getAttribute('data-priority');
                    const dueDate = new Date(item.querySelector('.task-date').textContent);
                    const today = new Date();
                    const isDueToday = dueDate.toDateString() === today.toDateString();

                    let visible = false;

                    switch (filter) {
                        case 'all':
                            visible = true;
                            break;
                        case 'active':
                            visible = !isCompleted;
                            break;
                        case 'completed':
                            visible = isCompleted;
                            break;
                        case 'high':
                            visible = priority === 'high';
                            break;
                        case 'today':
                            visible = isDueToday;
                            break;
                    }

                    item.style.display = visible ? '' : 'none';
                });
            }


            function updateRecentTasks() {
                const recentTasksContainer = document.getElementById('recent-tasks-container');


                if (!recentTasksContainer) return;

                const tasks = Array.from(document.querySelectorAll('.task-item')).slice(0, 5);

                recentTasksContainer.innerHTML = '';

                if (tasks.length === 0) {
                    recentTasksContainer.innerHTML = '<div class="activity-item">No tasks yet</div>';
                    return;
                }

                tasks.forEach(task => {
                    const taskText = task.querySelector('.task-text').textContent;
                    const taskPriority = task.getAttribute('data-priority');
                    const isCompleted = task.classList.contains('completed');

                    const activityItem = document.createElement('div');
                    activityItem.className = 'activity-item';

                    activityItem.innerHTML = `
                <div class="activity-dot activity-task" style="background-color: ${getPriorityColor(taskPriority)}"></div>
                <div class="activity-content">
                    <div class="activity-title">${taskText}</div>
                    <div class="activity-meta">${isCompleted ? 'Completed' : 'Active'} • ${taskPriority} priority</div>
                </div>
            `;

                    recentTasksContainer.appendChild(activityItem);
                });
            }


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


            window.showConfirmation = function (message, callback) {

                if (!document.getElementById('confirm-modal')) {
                    const confirmModal = document.createElement('div');
                    confirmModal.id = 'confirm-modal';
                    confirmModal.className = 'modal';
                    confirmModal.innerHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title">Confirm Action</h2>
                        <button class="close-modal" id="close-confirm-modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p id="confirm-message"></p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline" id="cancel-confirm">Cancel</button>
                        <button class="btn btn-danger" id="confirm-action">Confirm</button>
                    </div>
                </div>
            `;
                    document.body.appendChild(confirmModal);


                    document.getElementById('close-confirm-modal').addEventListener('click', function () {
                        closeModal(confirmModal);
                    });

                    document.getElementById('cancel-confirm').addEventListener('click', function () {
                        closeModal(confirmModal);
                    });
                }

                const confirmModal = document.getElementById('confirm-modal');
                const confirmMessage = document.getElementById('confirm-message');
                const confirmAction = document.getElementById('confirm-action');


                confirmMessage.textContent = message;


                confirmAction.onclick = function () {
                    closeModal(confirmModal);
                    if (typeof callback === 'function') {
                        callback();
                    }
                };


                openModal(confirmModal);
            };


            function handleFormErrors(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = 'Please fix the following errors:';

                    for (const field in errors) {
                        errorMessage += `\n- ${errors[field][0]}`;
                    }
                    showNotification(errorMessage, 'error');
                } else {
                    showNotification('An unexpected error occurred. Please try again.', 'error');
                }
            }

        });
    </script>

@endpush
