@extends('layouts.app')

@section('content')
    <div id="journal-view" style="display: block">
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
                    Journal
                </h1>
            </div>
            <div class="date" id="current-date">March 6, 2025</div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Create New Entry</h2>
            </div>
            <button class="btn btn-primary" id="new-journal-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="lucide lucide-plus">
                    <path d="M5 12h14"/>
                    <path d="M12 5v14"/>
                </svg>
                New Journal Entry
            </button>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">My Journal</h2>
                <div class="search-container">
                    <input type="text" class="form-input" id="journal-search" placeholder="Search journals...">
                </div>
            </div>
            <div class="journal-list" id="journal-list">
                <!-- Journal entries will be loaded here -->
                <span class="loader" style="display: none;"></span>
            </div>
        </div>
    </div>
@endsection

<div class="modal" id="journal-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title" id="journal-modal-title">New Journal Entry</h2>
            <button class="close-modal" id="close-journal-modal">&times;</button>
        </div>
        <form id="journal-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="journal-id" name="journal_id">
            <div class="form-group">
                <label class="form-label" for="journal-title">Title</label>
                <input type="text" class="form-input" id="journal-title" name="title" placeholder="Enter title"
                       required>
            </div>
            <div class="form-group">
                <label class="form-label" for="journal-content">Content</label>
                <textarea class="form-textarea" id="journal-content" name="content"
                          placeholder="What's on your mind today?" required></textarea>
            </div>
            <div class="form-group">
                <label class="form-label" for="journal-tags">Tags (comma separated)</label>
                <input type="text" class="form-input" id="journal-tags" name="tags"
                       placeholder="personal, work, idea...">
            </div>
            <div class="form-group">
                <label class="form-label" for="cover">Attach Image</label>
                <input type="file" accept="image/*" class="form-input" name="cover" id="cover">
                <div id="current-cover-container" style="margin-top: 10px; display: none;">
                    <p>Current image:</p>
                    <img id="current-cover" src="" alt="Current cover" style="max-width: 200px; max-height: 200px;">
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-outline" id="cancel-journal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="save-journal">Save Entry</button>
            </div>
        </form>
    </div>
</div>

<div class="modal" id="journal-modal-show">
    <div class="modal-content">
        <button class="close-modal" id="close-journal-modal">&times;</button>

        <div class="journal-content">
            <div>
                <h2 class="modal-title" id="journal-modal-title">I Missed Up</h2>
                <p class="date">12 Mar, 2023</p>
            </div>

            <img src="http://127.0.0.1:8000/storage/journals/d83dae8a-fbc8-4da4-8b33-71881be27c27.png" alt="">
            <p></p>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const elements = {

                journalModal: document.getElementById('journal-modal'),
                journalModalShow: document.getElementById('journal-modal-show'),
                confirmModal: document.getElementById('confirm-modal'),


                journalForm: document.getElementById('journal-form'),
                journalId: document.getElementById('journal-id'),
                journalTitle: document.getElementById('journal-title'),
                journalContent: document.getElementById('journal-content'),
                journalTags: document.getElementById('journal-tags'),
                cover: document.getElementById('cover'),
                currentCoverContainer: document.getElementById('current-cover-container'),
                currentCover: document.getElementById('current-cover'),


                newJournalBtn: document.getElementById('new-journal-btn'),
                closeJournalModal: document.querySelectorAll('.close-modal'),
                cancelJournal: document.getElementById('cancel-journal'),
                saveJournal: document.getElementById('save-journal'),
                closeConfirmModal: document.getElementById('close-confirm-modal'),
                cancelConfirm: document.getElementById('cancel-confirm'),
                confirmAction: document.getElementById('confirm-action'),
                journalSearch: document.getElementById('journal-search'),


                journalList: document.getElementById('journal-list'),
                notification: document.getElementById('notification'),
                notificationContent: document.getElementById('notification-content'),
                loader: document.querySelector('.loader')
            };


            loadJournals();


            elements.newJournalBtn.addEventListener('click', handleNewJournalClick);
            elements.cancelJournal.addEventListener('click', () => closeModal(elements.journalModal));
            elements.closeConfirmModal.addEventListener('click', () => closeModal(elements.confirmModal));
            elements.cancelConfirm.addEventListener('click', () => closeModal(elements.confirmModal));
            elements.journalForm.addEventListener('submit', handleJournalFormSubmit);
            elements.journalSearch.addEventListener('input', debounce(handleSearch, 500));


            elements.closeJournalModal.forEach((item) => {
                item.addEventListener('click', () => {
                    closeModal(elements.journalModal);
                    closeModal(elements.journalModalShow);
                })
            })

            function handleNewJournalClick() {

                elements.journalForm.reset();
                elements.journalId.value = '';
                document.getElementById('journal-modal-title').textContent = 'New Journal Entry';
                elements.currentCoverContainer.style.display = 'none';

                openModal(elements.journalModal);
            }


            function handleJournalFormSubmit(e) {
                e.preventDefault();

                const formData = new FormData(elements.journalForm);
                const journalId = elements.journalId.value;

                if (journalId) {
                    updateJournal(journalId, formData);
                } else {
                    createJournal(formData);
                }
            }


            function handleSearch() {
                const searchQuery = elements.journalSearch.value.trim();
                loadJournals(searchQuery);
            }


            function loadJournals(search = '') {
                elements.journalList.innerHTML = '<span class="loader"></span>';

                $.ajax({
                    url: '{{ route("journals.getData") }}',
                    type: 'GET',
                    data: {search: search},
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {

                        elements.journalList.innerHTML = '';

                        if (response.length === 0) {
                            elements.journalList.innerHTML = '<div class="empty-state">No journal entries found</div>';
                            return;
                        }


                        response.forEach(journal => {
                            addJournalToList(journal);
                        });
                    },
                    error: function (xhr, status, error) {
                        showNotification('Error loading journals: ' + error, 'error');
                        console.error("Error:", error);
                    },
                    complete: function () {
                        // elements.loader.style.display = 'none';
                    }
                });
            }


            function createJournal(formData) {
                elements.saveJournal.disabled = true;

                $.ajax({
                    url: '{{ route("journals.store") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        closeModal(elements.journalModal);
                        showNotification('Journal entry created successfully!', 'success');
                        loadJournals();
                    },
                    error: function (xhr) {
                        handleFormErrors(xhr);
                    },
                    complete: function () {
                        elements.saveJournal.disabled = false;
                    }
                });
            }


            function updateJournal(journalId, formData) {
                elements.saveJournal.disabled = true;

                formData.append('_method', 'PUT');

                $.ajax({
                    url: '{{ url("journals") }}/' + journalId,
                    data: formData,
                    type: 'POST',
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        closeModal(elements.journalModal);
                        showNotification('Journal entry updated successfully!', 'success');
                        loadJournals();
                    },
                    error: function (xhr) {
                        handleFormErrors(xhr);
                    },
                    complete: function () {
                        elements.saveJournal.disabled = false;
                    }
                });
            }


            window.editJournal = function (journalId) {
                $.ajax({
                    url: '{{ url("journals") }}/' + journalId,
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (journal) {
                        document.getElementById('journal-modal-title').textContent = 'Edit Journal Entry';
                        elements.journalId.value = journal.id;
                        elements.journalTitle.value = journal.title;
                        elements.journalContent.value = journal.content;


                        const tagNames = journal.tags.map(tag => tag.name).join(', ');
                        elements.journalTags.value = tagNames;


                        if (journal.cover_url) {
                            elements.currentCoverContainer.style.display = 'block';
                            elements.currentCover.src = journal.cover_url;
                        } else {
                            elements.currentCoverContainer.style.display = 'none';
                        }

                        openModal(elements.journalModal);
                    },
                    error: function (xhr, status, error) {
                        showNotification('Error fetching journal details: ' + error, 'error');
                    }
                });
            };


            window.deleteJournal = function (journalId) {
                showConfirmation('Are you sure you want to delete this journal entry?', function () {
                    $.ajax({
                        url: '{{ url("journals") }}/' + journalId,
                        type: 'DELETE',
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            showNotification('Journal entry deleted successfully!', 'success');


                            const journalElement = document.querySelector(`.journal-card[data-id="${journalId}"]`);
                            if (journalElement) {
                                journalElement.remove();
                            }


                            if (elements.journalList.children.length === 0) {
                                elements.journalList.innerHTML = '<div class="empty-state">No journal entries found</div>';
                            }
                        },
                        error: function (xhr, status, error) {
                            showNotification('Error deleting journal: ' + error, 'error');
                        }
                    });
                });
            };


            function addJournalToList(journal) {
                const journalCard = document.createElement('div');
                journalCard.className = 'card journal-card';
                journalCard.setAttribute('data-id', journal.id);


                const tagHTML = journal.tags && journal.tags.length
                    ? journal.tags.map(tag => `<span class="journal-tag">${tag.name}</span>`).join('')
                    : '';


                const createdAt = new Date(journal.created_at);
                const formattedDate = formatDateTime(createdAt);

                journalCard.innerHTML = `
                    <div class="journal-header">
                        <h3 class="journal-title">${journal.title}</h3>
                        <div class="journal-date">${formattedDate}</div>
                    </div>
                    ${journal.cover_url ? `<img src="${journal.cover_url}" alt="${journal.title}" class="journal-img">` : ''}
                    <div class="journal-preview">${truncateText(journal.content, 200)}</div>
                    <div class="journal-tags">
                        ${tagHTML}
                    </div>
                    <div class="task-actions journal-actions" style="opacity: 1; margin-top: 10px;">
                        <button class="task-btn edit" onclick="editJournal('${journal.id}')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen"><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"/></svg>
                        </button>
                        <button class="task-btn delete" onclick="deleteJournal('${journal.id}')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                        </button>
                    </div>
                `;


                elements.journalList.appendChild(journalCard);

                // add function to show journal card
                journalCard.querySelector('.journal-title').addEventListener('click', () => {
                    $.ajax({
                        url: '{{ url("journals") }}/' + journal.id,
                        type: 'GET',
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (journal) {

                            document.querySelector('.journal-content').innerHTML = `
                              <div>
                                    <h2 class="modal-title" id="journal-modal-title">${journal.title}</h2>
                                     <p class="date">${journal.created_at}</p>
                              </div>

                              <img src="${journal.cover_url}" alt="">
                              <p>${journal.content}</p>
                            `;

                            openModal(elements.journalModalShow);
                        },
                        error: function (xhr, status, error) {
                            showNotification('Error fetching journal details: ' + error, 'error');
                        }
                    });
                });
            }


            window.showConfirmation = function (message, callback) {

                document.getElementById('confirm-message').textContent = message;


                elements.confirmAction.onclick = function () {
                    closeModal(elements.confirmModal);
                    if (typeof callback === 'function') {
                        callback();
                    }
                };


                openModal(elements.confirmModal);
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


            function formatDateTime(date) {
                return date.toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }


            function truncateText(text, maxLength) {
                if (text.length <= maxLength) return text;
                return text.substring(0, maxLength) + '...';
            }


            function debounce(func, wait) {
                let timeout;
                return function () {
                    const context = this;
                    const args = arguments;
                    clearTimeout(timeout);
                    timeout = setTimeout(function () {
                        func.apply(context, args);
                    }, wait);
                };
            }
        });
    </script>
@endpush
