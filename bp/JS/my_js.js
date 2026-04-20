/* ==========================================================
   GLOBAL FUNCTIONS
   ========================================================== */

function fadeOutEffect(element) {
    element.classList.add('notification--hiding');
    setTimeout(() => element.remove(), 800);
}

function showNotification(message, type = 'success') {
    let container = document.querySelector('.notification__container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'notification__container';
        document.body.appendChild(container);
    }

    const notif = document.createElement('div');
    notif.className = `notification notification--${type}`;
    notif.textContent = message;
    container.appendChild(notif);

    setTimeout(() => fadeOutEffect(notif), 4000);
}

function showConfirm(message, onConfirm) {
    const modal = document.getElementById('confirmModal');
    document.getElementById('confirmMessage').textContent = message;
    modal.classList.add('confirm-modal--visible');

    document.getElementById('confirmYes').onclick = function () {
        modal.classList.remove('confirm-modal--visible');
        onConfirm();
    };

    document.getElementById('confirmNo').onclick = function () {
        modal.classList.remove('confirm-modal--visible');
    };
}

// data confirm for all buttons
document.addEventListener('click', function (e) {
    const btn = e.target.closest('button[data-confirm]');
    if (!btn || btn._confirmed) {
        if (btn) btn._confirmed = false;
        return;
    }
    e.preventDefault();
    showConfirm(btn.dataset.confirm, function () {
        btn._confirmed = true;
        btn.focus();
        btn.click();
    });
});


/* ==========================================================
   AFTER PAGE LOAD
   ========================================================== */

document.addEventListener("DOMContentLoaded", function () {

    /*
     * -----------------------------------------
     * FLASH NOTIFICATIONS (from session)
     * -----------------------------------------
     */
    document.querySelectorAll('.notification').forEach(n => {
        setTimeout(() => fadeOutEffect(n), 4000);
    });


    /*
     * -----------------------------------------
     * VALIDATION — CREATE EVENT
     * -----------------------------------------
     */
    const createEventForm = document.getElementById('createEventForm');

    if (createEventForm) {
        createEventForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const name = document.getElementById('event_name').value.trim();
            const status = document.getElementById('status').value;
            const start = document.getElementById('start_at')?.value;
            const end = document.getElementById('end_at')?.value;
            const deadline = document.getElementById('voting_deadline')?.value;

            if (!name) {
                showNotification('Event name is required.', 'error');
                document.getElementById('event_name').focus();
                return;
            }

            if (status === 'confirmed') {
                if (!start) {
                    showNotification('Start time is required for confirmed events.', 'error');
                    document.getElementById('start_at').focus();
                    return;
                }
                if (end && new Date(end) <= new Date(start)) {
                    showNotification('End time must be after start time.', 'error');
                    document.getElementById('end_at').focus();
                    return;
                }
            }

            if (status === 'planning') {
                if (!deadline) {
                    showNotification('Voting deadline is required.', 'error');
                    document.getElementById('voting_deadline').focus();
                    return;
                }

                const starts = document.querySelectorAll('input[name="poll_starts[]"]');
                const ends = document.querySelectorAll('input[name="poll_ends[]"]');
                let validCount = 0;

                starts.forEach((s, i) => {
                    const ps = s.value;
                    const pe = ends[i]?.value;
                    if (ps && pe && new Date(pe) > new Date(ps)) validCount++;
                });

                if (validCount < 2) {
                    showNotification('Please add at least 2 valid proposed dates for voting.', 'error');
                    return;
                }

                // Deadline must be earlier than the earliest poll option
                const pollStartDates = [...starts].map(s => new Date(s.value)).filter(d => !isNaN(d));
                if (pollStartDates.length > 0) {
                    const earliestPoll = new Date(Math.min(...pollStartDates));
                    if (new Date(deadline) >= earliestPoll) {
                        showNotification('Voting deadline must be earlier than the earliest proposed date.', 'error');
                        document.getElementById('voting_deadline').focus();
                        return;
                    }
                }
            }

            const submitInput = document.createElement('input');
            submitInput.type = 'hidden';
            submitInput.name = 'submit_create_event';
            submitInput.value = '1';
            this.appendChild(submitInput);
            this.submit();
        });
    }


    /*
     * -----------------------------------------
     * VALIDATION — EDIT EVENT
     * -----------------------------------------
     */
    const editEventForm = document.getElementById('editEventForm');

    if (editEventForm) {
        editEventForm.addEventListener('submit', async function (e) {
            if (document.activeElement?.name === 'submit_delete_event') return;

            e.preventDefault();

            const name = document.getElementById('event_name').value.trim();
            const status = document.getElementById('status').value;
            const start = document.getElementById('start_at')?.value;
            const end = document.getElementById('end_at')?.value;
            const deadline = document.getElementById('voting_deadline')?.value;

            if (!name) {
                showNotification('Event name is required.', 'error');
                document.getElementById('event_name').focus();
                return;
            }

            if (status === 'confirmed') {
                if (!start) {
                    showNotification('Start time is required for confirmed events.', 'error');
                    document.getElementById('start_at').focus();
                    return;
                }
                if (end && new Date(end) <= new Date(start)) {
                    showNotification('End time must be after start time.', 'error');
                    document.getElementById('end_at').focus();
                    return;
                }
            }

            if (status === 'planning') {
                if (!deadline) {
                    showNotification('Voting deadline is required for planning events.', 'error');
                    document.getElementById('voting_deadline').focus();
                    return;
                }

                const starts = document.querySelectorAll('input[name="poll_starts[]"]');
                const ends = document.querySelectorAll('input[name="poll_ends[]"]');
                let validCount = 0;

                starts.forEach((s, i) => {
                    const ps = s.value;
                    const pe = ends[i]?.value;
                    if (ps && pe && new Date(pe) > new Date(ps)) validCount++;
                });

                if (validCount < 2) {
                    showNotification('Please add at least 2 valid proposed dates for voting.', 'error');
                    return;
                }

                // Deadline must be earlier than the earliest poll option
                const pollStartDates = [...starts].map(s => new Date(s.value)).filter(d => !isNaN(d));
                if (pollStartDates.length > 0) {
                    const earliestPoll = new Date(Math.min(...pollStartDates));
                    if (new Date(deadline) >= earliestPoll) {
                        showNotification('Voting deadline must be earlier than the earliest proposed date.', 'error');
                        document.getElementById('voting_deadline').focus();
                        return;
                    }
                }
            }

            const submitInputEdit = document.createElement('input');
            submitInputEdit.type = 'hidden';
            submitInputEdit.name = 'submit_update_event';
            submitInputEdit.value = '1';
            this.appendChild(submitInputEdit);
            this.submit();
        });
    }


    /*
     * -----------------------------------------
     * VALIDATION — LOGIN
     * -----------------------------------------
     */
    const loginForm = document.getElementById('login-form');

    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            const email = document.getElementById('email').value.trim();
            const passwd = document.getElementById('password').value;

            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                e.preventDefault();
                showNotification('Please enter a valid email address.', 'error');
                document.getElementById('email').focus();
                return;
            }

            if (!passwd) {
                e.preventDefault();
                showNotification('Password is required.', 'error');
                document.getElementById('password').focus();
                return;
            }
        });
    }


    /*
     * -----------------------------------------
     * VALIDATION — REGISTER
     * -----------------------------------------
     */
    const registerForm = document.getElementById('register-form');

    if (registerForm) {
        registerForm.addEventListener('submit', function (e) {
            const firstName = document.getElementById('first_name').value.trim();
            const surname = document.getElementById('surname').value.trim();
            const email = document.getElementById('email').value.trim();
            const passwd = document.getElementById('passwd').value;
            const repeat = document.getElementById('repeatpasswd').value;
            const birthYear = parseInt(document.getElementById('birth_year').value);
            const gender = document.getElementById('gender').value;
            const currentYear = new Date().getFullYear();
            const tel = document.getElementById('tel').value.trim();

            if (!firstName || !surname) {
                e.preventDefault();
                showNotification('First name and surname are required.', 'error');
                return;
            }

            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                e.preventDefault();
                showNotification('Please enter a valid email address.', 'error');
                document.getElementById('email').focus();
                return;
            }

            if (passwd.length < 8 || passwd.length > 72) {
                e.preventDefault();
                showNotification('Password must be between 8 and 72 characters.', 'error');
                document.getElementById('passwd').focus();
                return;
            }

            if (passwd !== repeat) {
                e.preventDefault();
                showNotification('Passwords do not match.', 'error');
                document.getElementById('repeatpasswd').focus();
                return;
            }

            if (!birthYear || birthYear < 1900 || birthYear > currentYear) {
                e.preventDefault();
                showNotification('Please enter a valid birth year.', 'error');
                document.getElementById('birth_year').focus();
                return;
            }

            if (!gender) {
                e.preventDefault();
                showNotification('Please select a gender.', 'error');
                return;
            }

            if (!tel || !/^\+?[0-9\s\-()]{9,}$/.test(tel)) {
                e.preventDefault();
                showNotification('Please enter a valid phone number. For example: +420 123 456 789 or 123 456 789 or 123456789', 'error');
                return;
            }


        });
    }


    /*
     * -----------------------------------------
     * EDIT PROFILE VALIDATION
     * -----------------------------------------
     */
    const editProfileForm = document.getElementById('editProfileForm');

    if (editProfileForm) {
        editProfileForm.addEventListener('submit', function (e) {
            if (document.activeElement?.name === 'submit_delete_profile') return;

            const firstName = document.getElementById('first_name').value.trim();
            const surname = document.getElementById('surname').value.trim();
            const email = document.getElementById('email').value.trim();
            const passwd = document.getElementById('passwd').value;
            const birthYear = parseInt(document.getElementById('birth_year').value);
            const currentYear = new Date().getFullYear();
            const tel = document.getElementById('tel').value.trim();

            if (!firstName || !surname) {
                e.preventDefault();
                showNotification('First name and last name are required.', 'error');
                return;
            }

            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                e.preventDefault();
                showNotification('Please enter a valid email address.', 'error');
                document.getElementById('email').focus();
                return;
            }

            if (passwd && (passwd.length < 8 || passwd.length > 72)) {
                e.preventDefault();
                showNotification('Password must be between 8 and 72 characters.', 'error');
                document.getElementById('passwd').focus();
                return;
            }

            if (!birthYear || birthYear < 1900 || birthYear > currentYear) {
                e.preventDefault();
                showNotification('Please enter a valid birth year.', 'error');
                document.getElementById('birth_year').focus();
                return;
            }

            if (!tel || !/^\+?[0-9\s\-()]{9,}$/.test(tel)) {
                e.preventDefault();
                showNotification('Please enter a valid phone number. For example: +420 123 456 789 or 123456789', 'error');
                document.getElementById('tel').focus();
                return;
            }
        });
    }


    /*
     * -----------------------------------------
     * VALIDATION — FORGOT PASSWORD
     * -----------------------------------------
     */
    const forgotPasswordForm = document.querySelector('form[action*="forgot-password"]');

    if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener('submit', function (e) {
            const email = document.getElementById('email').value.trim();

            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                e.preventDefault();
                showNotification('Please enter a valid email address.', 'error');
                document.getElementById('email').focus();
                return;
            }
        });
    }


    /*
     * -----------------------------------------
     * VALIDATION — RESET PASSWORD
     * -----------------------------------------
     */
    const resetPasswordForm = document.querySelector('form[action*="reset-password"]');

    if (resetPasswordForm) {
        resetPasswordForm.addEventListener('submit', function (e) {
            const passwd = document.getElementById('passwd').value;
            const repeat = document.getElementById('repeatpasswd').value;

            if (passwd.length < 8 || passwd.length > 72) {
                e.preventDefault();
                showNotification('Password must be between 8 and 72 characters.', 'error');
                document.getElementById('passwd').focus();
                return;
            }

            if (passwd !== repeat) {
                e.preventDefault();
                showNotification('Passwords do not match.', 'error');
                document.getElementById('repeatpasswd').focus();
                return;
            }
        });
    }


    /*
    * -----------------------------------------
    * FULLCALENDAR
    * -----------------------------------------
    */

    const calendarEl = document.getElementById('calendar');

    if (calendarEl) {

        const isMobile = () => window.innerWidth < 769;

        function getInitialView() {
            return isMobile() ? 'listMonth' : 'dayGridMonth';
        }

        function getToolbar() {
            if (isMobile()) {
                return {
                    left: 'prev,next',
                    center: 'title',
                    right: 'listDay,listWeek,listMonth'
                };
            }
            return {
                left: 'prev,next today',
                center: 'title',
                right: 'timeGridDay,timeGridWeek,dayGridMonth'
            };
        }

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: getInitialView(),
            headerToolbar: getToolbar(),
            locale: 'en',
            firstDay: 1,
            buttonText: {
                listDay: 'Day',
                listWeek: 'Week',
                listMonth: 'Month',
                timeGridDay: 'Day',
                timeGridWeek: 'Week',
                dayGridMonth: 'Month'
            },
            noEventsContent: 'No events to display.',
            events: myCalendarEvents || [],
            eventClick: function (info) {
                window.location.href = domena + "/events/detail/" + info.event.id;
            },
            windowResize: function () {
                calendar.changeView(getInitialView());
                calendar.setOption('headerToolbar', getToolbar());
            }
        });

        calendar.render();

    }

    /*
     * -----------------------------------------
     * TOGGLING SECTIONS CONFIRMED / PLANNING
     * -----------------------------------------
     */
    const statusSelect = document.getElementById("status");
    const sectionConfirmed = document.getElementById("section-confirmed");
    const sectionPlanning = document.getElementById("section-planning");

    if (statusSelect && sectionConfirmed && sectionPlanning) {
        function toggleStatusSections() {
            sectionConfirmed.style.display = statusSelect.value === 'confirmed' ? 'block' : 'none';
            sectionPlanning.style.display = statusSelect.value === 'planning' ? 'block' : 'none';
        }
        toggleStatusSections();
        statusSelect.addEventListener("change", toggleStatusSections);
    }


    /*
     * -----------------------------------------
     * ADDING / REMOVING POLL DATE OPTIONS
     * -----------------------------------------
     */
    const addOptionBtn = document.getElementById("add-option-btn");
    const pollContainer = document.getElementById('poll-options-container');

    if (pollContainer) {
        pollContainer.addEventListener('click', function (e) {
            const btn = e.target.closest('.poll-remove-btn');
            if (!btn) return;

            const items = pollContainer.querySelectorAll('.poll-option');
            if (items.length <= 2) {
                showNotification('At least 2 date options are required.', 'error');
                return;
            }
            btn.closest('.poll-option').remove();
        });
    }

    if (addOptionBtn) {
        addOptionBtn.addEventListener("click", function () {
            const newOption = document.createElement("div");
            newOption.className = "poll-option";
            newOption.innerHTML = `
                <div class="poll-option__field">
                    <span>Start:</span>
                    <input type="datetime-local" name="poll_starts[]" class="form-layout__input">
                </div>
                <div class="poll-option__field">
                    <span>End:</span>
                    <input type="datetime-local" name="poll_ends[]" class="form-layout__input">
                </div>
                <button type="button" class="btn-group__item btn-group__item--danger btn-group__item--small poll-remove-btn">X</button>
            `;
            pollContainer.appendChild(newOption);
        });
    }


    /*
     * -----------------------------------------
     * POLL OPTION CARDS — VISUAL SELECTION
     * -----------------------------------------
     */
    const pollCards = document.querySelectorAll('.poll-option-card');

    pollCards.forEach(card => {
        card.addEventListener('click', function () {
            pollCards.forEach(c => c.classList.remove('poll-option-card--selected'));
            this.classList.add('poll-option-card--selected');
        });
    });


    /*
     * -----------------------------------------
     * TABLE SEARCH
     * -----------------------------------------
     */
    const eventsSearch = document.getElementById('eventsSearch');
    const friendsSearch = document.getElementById('friendsSearch');

    [eventsSearch, friendsSearch].forEach(input => {
        if (!input) return;
        input.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            document.querySelectorAll('.events-table__row').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(query) ? '' : 'none';
            });
        });
    });


    /*
     * -----------------------------------------
     * VALIDATION — FRIENDS SEARCH
     * -----------------------------------------
     */
    const friendsSearchForm = document.querySelector('form[action*="friends/search"]');

    if (friendsSearchForm) {
        friendsSearchForm.addEventListener('submit', function (e) {
            const email = this.querySelector('input[name="email"]').value.trim();

            if (!email) {
                e.preventDefault();
                showNotification('Please enter an email address.', 'error');
                return;
            }

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                e.preventDefault();
                showNotification('Please enter a valid email address.', 'error');
                return;
            }
        });
    }


    /*
     * -----------------------------------------
     * AVATAR FILE INPUT — DISPLAY FILENAME
     * -----------------------------------------
     */
    const avatarInput = document.getElementById('avatar');

    if (avatarInput) {
        avatarInput.addEventListener('change', function () {
            const label = document.getElementById('avatarFileName');
            if (label) label.textContent = this.files[0] ? this.files[0].name : 'No file chosen';
        });
    }

    /*
     * -----------------------------------------
     * HAMBURGER MENU (MOBILE)
     * -----------------------------------------
     */
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const navDropdown = document.getElementById('navDropdown');

    if (hamburgerBtn && navDropdown) {
        hamburgerBtn.addEventListener('click', function () {
            navDropdown.classList.toggle('navbar__dropdown--open');
        });

        navDropdown.querySelectorAll('.navbar__dropdown-item').forEach(item => {
            item.addEventListener('click', () => {
                navDropdown.classList.remove('navbar__dropdown--open');
            });
        });
    }

});