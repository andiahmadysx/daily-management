@extends('layouts.app')

@push('styles')
    <style>
        :root {
            --primary: #141414;
            --secondary: #f0f0f0;
            --highlight: #6d9ff5;
            --highlight-light: rgba(109, 159, 245, 0.12);
        }

        .random-picker-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            flex: 1;
            width: 100%;
            position: relative;
            padding: 2rem;
        }

        .result-display {
            position: relative;
            width: 100%;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 1rem;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 1s 0.3s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        .result-container {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            overflow: hidden;
        }

        .result {
            font-size: 2.5rem;
            font-weight: 300;
            text-align: center;
            transition: all var(--transition);
            opacity: 1;
            position: relative;
            z-index: 2;
            padding: 1rem 2rem;
            max-width: 100%;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .result.show {
            opacity: 1;
        }

        .result.highlight {
            font-weight: 400;
            color: var(--highlight);
        }

        .animation-container {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
            overflow: hidden;
            border-radius: 12px;
        }

        .controls {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
            width: 100%;
            opacity: 1;
            transform: translateY(20px);
            animation: fadeInUp 1s 0.5s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        .button-container {
            display: flex;
            gap: 1rem;
        }


        .options-btn {
            background: transparent;
            color: var(--accent);
            border: 1px solid var(--border);
        }

        .options-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--highlight-light);
            color: var(--highlight);
            padding: 0.3rem 0.7rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-top: 0.5rem;
            opacity: 0;
            transform: translateY(10px);
            animation: fadeInUp 0.5s 0.7s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        .input-section {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }


        .add-btn {
            padding: 0.9rem 1.5rem;
        }

        .options-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1rem;
            max-height: 300px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .options-list::-webkit-scrollbar {
            width: 0;
        }

        .option-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.85rem 1rem;
            background: rgba(0, 0, 0, 0.02);
            border-radius: 8px;
            transition: all var(--transition);
            animation: slideIn 0.5s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .option-item:hover {
            background: rgba(0, 0, 0, 0.04);
            transform: translateX(2px);
        }

        .option-item.removing {
            animation: slideOut 0.5s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        .option-item:nth-child(even) {
            background: rgba(0, 0, 0, 0.035);
        }

        .delete-btn {
            background: transparent;
            color: red;
            opacity: 0.6;
            border: none;
            border-radius: 50%;
            font-size: 0.85rem;
            transform: scale(0.9);
            transition: all 0.2s ease;
            width: 30px;
            height: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0;
            cursor: pointer;
        }

        .delete-btn:hover {
            opacity: 1;
            transform: scale(1);
            background: rgba(0, 0, 0, 0.05);
            box-shadow: none;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .clear-btn {
            background: transparent;
            color: var(--accent);
            font-size: 0.9rem;
            padding: 0.5rem 1.2rem;
            border: 1px solid var(--border);
        }

        .clear-btn:hover {
            color: var(--secondary);
            border-color: var(--accent);
        }

        .dark-mode .result-container {
            background: linear-gradient(145deg, rgba(30, 30, 30, 0.8), rgba(20, 20, 20, 0.4));
        }

        .no-options {
            text-align: center;
            color: var(--accent);
            padding: 2rem 1rem;
            font-style: italic;
            opacity: 0.8;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideOut {
            to {
                opacity: 0;
                transform: translateX(20px);
                height: 0;
                padding: 0;
                margin: -5px 0;
                overflow: hidden;
            }
        }

        @keyframes floatUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            20% {
                opacity: 1;
            }
            100% {
                opacity: 0;
                transform: translateY(-100px);
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.05);
                opacity: 0.8;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes ripple {
            0% {
                transform: scale(0) translate(-50%, -50%);
                opacity: 1;
            }
            100% {
                transform: scale(20) translate(-50%, -50%);
                opacity: 0;
            }
        }

        @keyframes rotateIn {
            from {
                transform: rotate(-10deg) scale(0.8);
                opacity: 0;
            }
            to {
                transform: rotate(0) scale(1);
                opacity: 1;
            }
        }

        @media (max-width: 640px) {
            .input-section {
                flex-direction: column;
            }

            h1 {
                font-size: 2rem;
            }

            .button-container {
                flex-direction: column;
                width: 100%;
            }

            .button-container button {
                width: 100%;
            }

            .result {
                font-size: 2rem;
            }
        }
    </style>

@endpush

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
                Random Picker
            </h1>
            </div>
            <div class="date" id="current-date">March 6, 2025</div>
        </div>

        <div class="card">
            <div class="random-picker-container">
                <div class="result-display">
                    <div class="result-container">
                        <div id="result" class="result">Ready to pick</div>
                    </div>
                </div>

                <div class="controls">
                    <div class="button-container">
                        <button id="pickBtn" class="pick-btn add-btn btn">Pick Random</button>
                        <button id="optionsBtn" class="options-btn btn btn-outline">Manage Options</button>
                    </div>
                    <div id="optionsCount" class="options-count">0 options available</div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal" id="task-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="task-modal-title">Manage Options</h2>
                <button class="close-modal" id="close-task-modal">&times;</button>
            </div>
            <form id="task-detail-form">
                <div class="input-section">
                    <input type="text" id="optionInput" class="task-input" placeholder="Add an option...">
                    <button id="addBtn" type="button" class="add-btn btn-primary btn">Add</button>
                </div>

                <div id="optionsList" class="options-list"></div>
                <div class="input-selection" style="display: flex; gap: .3rem; align-items: center">
                    <input type="checkbox" id="render-from-task">
                    <label for="render-from-task" class="date">Render from Task</label>
                </div>
                <div class="form-actions">


                    <button id="clearAll" type="button" class="clear-btn btn btn-outline">Clear All</button>
                    <button id="saveOptions" type="button" class="save-btn btn btn-primary">Save Options</button>
                </div>
            </form>
        </div>
    </div>

@endsection


@push('scripts')
    <script>

        const optionInput = document.getElementById('optionInput');
        const addBtn = document.getElementById('addBtn');
        const optionsList = document.getElementById('optionsList');
        const pickBtn = document.getElementById('pickBtn');
        const optionsBtn = document.getElementById('optionsBtn');
        const clearAllBtn = document.getElementById('clearAll');
        const resultDisplay = document.getElementById('result');
        const optionsCount = document.getElementById('optionsCount');
        const saveOptions = document.getElementById('saveOptions');
        const animationContainer = document.querySelector('.animation-container');
        const taskModal = document.getElementById('task-modal');
        const closeTaskModal = document.querySelector('#close-task-modal');
        const renderFromTaskCheckbox = document.getElementById('render-from-task');

        let options = [];
        let isPickingRandom = false;
        let isRenderingFromTasks = false;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function init() {

            isRenderingFromTasks = renderFromTaskCheckbox.checked;


            loadOptions();

            optionInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addOption();
                }
            });

            addBtn.addEventListener('click', addOption);
            pickBtn.addEventListener('click', pickRandom);
            optionsBtn.addEventListener('click', () => openModal(taskModal));
            clearAllBtn.addEventListener('click', clearAllOptions);
            saveOptions.addEventListener('click', () => closeModalFunc(taskModal));
            closeTaskModal.addEventListener('click', () => closeModalFunc(taskModal));


            renderFromTaskCheckbox.addEventListener('change', function () {
                isRenderingFromTasks = this.checked;
                loadOptions();
            });
        }


        function loadOptions() {
            if (isRenderingFromTasks) {
                loadOptionsFromTasks();
            } else {
                loadOptionsFromDatabase();
            }
        }


        function loadOptionsFromDatabase() {
            $.ajax({
                url: "{{route('random-pickers.getData')}}",
                method: 'GET',
                success: function (response) {
                    options = response.map(item => ({
                        id: item.id,
                        value: item.name
                    }));
                    renderOptions();
                    updateOptionsCount();
                },
                error: function (error) {
                    console.error('Error loading options:', error);
                }
            });
        }


        function loadOptionsFromTasks() {
            $.ajax({
                url: '{{ route("tasks.active.getData") }}',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    options = response.map(task => ({
                        id: task.id,
                        value: task.title
                    }));
                    renderOptions();
                    updateOptionsCount();
                },
                error: function (xhr, status, error) {
                    console.error("Error loading tasks:", error);
                }
            });
        }

        function addOption() {

            if (isRenderingFromTasks) {
                alert('Cannot add options manually when "Render from Task" is enabled. Please add tasks from the Tasks section instead.');
                return;
            }

            const value = optionInput.value.trim();

            if (value) {

                $.ajax({
                    url: "{{route('random-pickers.store')}}",
                    method: 'POST',
                    data: {
                        name: value
                    },
                    success: function (response) {
                        const newOption = {
                            id: response.id,
                            value: response.name
                        };

                        options.push(newOption);
                        optionInput.value = '';

                        const newElement = createOptionElement(newOption);
                        optionsList.appendChild(newElement);
                        newElement.classList.add('new');

                        setTimeout(() => {
                            newElement.classList.remove('new');
                        }, 1500);

                        optionInput.focus();
                        updateOptionsCount();
                        renderOptions();
                    },
                    error: function (error) {
                        console.error('Error adding option:', error);
                        if (error.responseJSON && error.responseJSON.errors) {
                            alert(error.responseJSON.errors.name[0]);
                        } else {
                            alert('Error adding option. Please try again.');
                        }
                    }
                });
            }
        }

        function createOptionElement(option) {
            const optionEl = document.createElement('div');
            optionEl.className = 'option-item';
            optionEl.dataset.id = option.id;

            const textEl = document.createElement('span');
            textEl.textContent = option.value;

            const deleteBtn = document.createElement('button');
            deleteBtn.className = 'delete-btn';
            deleteBtn.setAttribute('type', 'button');
            deleteBtn.textContent = '×';


            if (!isRenderingFromTasks) {
                deleteBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    removeOption(option.id);
                });
            } else {
                deleteBtn.style.display = 'none';
            }

            optionEl.appendChild(textEl);
            optionEl.appendChild(deleteBtn);

            return optionEl;
        }

        function renderOptions() {
            optionsList.innerHTML = '';

            if (options.length === 0) {
                const noOptions = document.createElement('div');
                noOptions.className = 'no-options';

                if (isRenderingFromTasks) {
                    noOptions.textContent = 'No tasks available. Add tasks from the Tasks section.';
                } else {
                    noOptions.textContent = 'Add some options to get started...';
                }

                optionsList.appendChild(noOptions);
                return;
            }

            options.forEach(option => {
                const optionEl = createOptionElement(option);
                optionsList.appendChild(optionEl);
            });
        }

        function updateOptionsCount() {
            optionsCount.textContent = `${options.length} option${options.length !== 1 ? 's' : ''} available`;

            optionsCount.style.animation = 'none';
            setTimeout(() => {
                optionsCount.style.animation = 'pulse 0.5s cubic-bezier(0.22, 1, 0.36, 1) forwards';
            }, 10);
        }

        function removeOption(id) {

            if (isRenderingFromTasks) {
                alert('Cannot remove options when "Render from Task" is enabled. Please manage tasks from the Tasks section.');
                return;
            }

            const optionEl = document.querySelector(`.option-item[data-id="${id}"]`);

            if (optionEl) {
                optionEl.classList.add('removing');


                $.ajax({
                    url: "{{route('random-pickers.delete', ':id')}}".replace(":id", id),
                    method: 'DELETE',
                    success: function () {
                        setTimeout(() => {
                            options = options.filter(option => option.id !== id);
                            renderOptions();
                            updateOptionsCount();
                        }, 500);
                    },
                    error: function (error) {
                        console.error('Error removing option:', error);
                        alert('Error removing option. Please try again.');
                        optionEl.classList.remove('removing');
                    }
                });
            }
        }

        function pickRandom() {
            if (options.length === 0) {
                resultDisplay.textContent = isRenderingFromTasks ? 'Add some tasks first!' : 'Add some options first!';
                resultDisplay.classList.add('show');

                setTimeout(() => {
                    openModal(taskModal);
                }, 1200);

                return;
            }

            if (isPickingRandom) return;
            isPickingRandom = true;

            resultDisplay.classList.remove('show', 'highlight');

            let shuffleCount = 0;
            const maxShuffles = 15;
            const shuffleSpeed = 80;

            const shuffleInterval = setInterval(() => {
                shuffleCount++;

                const randomIndex = Math.floor(Math.random() * options.length);
                const tempOption = options[randomIndex];

                resultDisplay.textContent = tempOption.value;
                resultDisplay.style.opacity = 0.6 + (shuffleCount / maxShuffles) * 0.4;

                if (!resultDisplay.classList.contains('show')) {
                    resultDisplay.classList.add('show');
                }

                if (shuffleCount >= maxShuffles) {
                    clearInterval(shuffleInterval);

                    const finalRandomIndex = Math.floor(Math.random() * options.length);
                    const selectedOption = options[finalRandomIndex];

                    resultDisplay.style.opacity = '';
                    resultDisplay.style.transform = '';
                    resultDisplay.style.fontSize = '';
                    resultDisplay.classList.remove('show');

                    setTimeout(() => {
                        resultDisplay.textContent = selectedOption.value;
                        resultDisplay.classList.add('show');

                        setTimeout(() => {
                            resultDisplay.classList.add('highlight');
                            isPickingRandom = false;
                        }, 300);
                    }, 200);
                }
            }, shuffleSpeed);
        }

        function clearAllOptions() {

            if (isRenderingFromTasks) {
                alert('Cannot clear options when "Render from Task" is enabled. Please manage tasks from the Tasks section.');
                return;
            }

            if (options.length === 0) return;

            if (!confirm('Are you sure you want to delete all options?')) {
                return;
            }

            const items = document.querySelectorAll('.option-item');

            let deletePromises = [];

            items.forEach((item, index) => {
                const id = item.dataset.id;


                const deletePromise = new Promise((resolve) => {
                    setTimeout(() => {
                        item.classList.add('removing');

                        $.ajax({
                            url: "{{route('random-pickers.delete', ':id')}}".replace(":id", id),
                            method: 'DELETE',
                            success: function () {
                                resolve();
                            },
                            error: function (error) {
                                console.error('Error removing option:', error);
                                resolve();
                            }
                        });
                    }, index * 80);
                });

                deletePromises.push(deletePromise);
            });


            Promise.all(deletePromises).then(() => {
                setTimeout(() => {
                    options = [];
                    renderOptions();
                    updateOptionsCount();
                }, 500);
            });
        }

        function openModal(modal) {
            modal.style.display = 'flex';

            setTimeout(() => {
                modal.classList.add('active');
            }, 10);
        }

        function closeModalFunc(modal) {
            modal.classList.remove('active');

            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }

        init();
    </script>
@endpush
