<main>

    <h1>Edit Event</h1>

    <div class="main-content">
        <span class="events-title">Update details for
            <?php echo htmlspecialchars($event_data['event_name']); ?></span>

        <div class="events-card">
            <form id="editEventForm" action="<?php echo $domena . '/events/edit/' . $event_data['id']; ?>"
                method="POST">

                <ul class="form-layout">

                    <li class="form-layout__item">
                        <label for="event_name" class="form-layout__label">Event Name:</label>
                        <input type="text" id="event_name" name="event_name" class="form-layout__input"
                            value="<?php echo htmlspecialchars($event_data['event_name']); ?>">
                    </li>

                    <li class="form-layout__item">
                        <label for="location" class="form-layout__label">Location:</label>
                        <input type="text" id="location" name="location" class="form-layout__input"
                            value="<?php echo htmlspecialchars($event_data['location'] ?? ''); ?>">
                    </li>

                    <li class="form-layout__item">
                        <label for="status" class="form-layout__label">Status:</label>
                        <select name="status" id="status" class="form-layout__input">
                            <option value="confirmed" <?php echo $event_data['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="planning" <?php echo $event_data['status'] === 'planning' ? 'selected' : ''; ?>>Planning</option>
                            <option value="cancelled" <?php echo $event_data['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </li>

                    <div id="section-confirmed">
                        <li class="form-layout__item">
                            <label for="start_at" class="form-layout__label">Start Time:</label>
                            <input type="datetime-local" id="start_at" name="start_at" class="form-layout__input"
                                value="<?php echo !empty($event_data['start_at']) ? date('Y-m-d\TH:i', strtotime($event_data['start_at'])) : ''; ?>">
                        </li>
                        <li class="form-layout__item">
                            <label for="end_at" class="form-layout__label">End Time:</label>
                            <input type="datetime-local" id="end_at" name="end_at" class="form-layout__input"
                                value="<?php echo !empty($event_data['end_at']) ? date('Y-m-d\TH:i', strtotime($event_data['end_at'])) : ''; ?>">
                        </li>
                    </div>

                    <div class="section-planning" id="section-planning">
                        <li class="form-layout__item">
                            <label for="voting_deadline" class="form-layout__label">Voting Deadline:</label>
                            <input type="datetime-local" id="voting_deadline" name="voting_deadline"
                                class="form-layout__input"
                                value="<?php echo !empty($event_data['voting_deadline']) ? date('Y-m-d\TH:i', strtotime($event_data['voting_deadline'])) : ''; ?>">
                        </li>

                        <div class="proposed-dates">
                            <h2>Proposed Dates</h2>
                            <p class="proposed-dates__hint">Add at least 2 proposed date ranges for participants to vote
                                on.</p>

                            <div id="poll-options-container">
                                <div class="poll-option">
                                    <div class="poll-option__field">
                                        <span>Start:</span>
                                        <input type="datetime-local" name="poll_starts[]" class="form-layout__input">
                                    </div>
                                    <div class="poll-option__field">
                                        <span>End:</span>
                                        <input type="datetime-local" name="poll_ends[]" class="form-layout__input">
                                    </div>
                                    <button type="button"
                                        class="btn-group__item btn-group__item--danger btn-group__item--small poll-remove-btn">X</button>
                                </div>
                                <div class="poll-option">
                                    <div class="poll-option__field">
                                        <span>Start:</span>
                                        <input type="datetime-local" name="poll_starts[]" class="form-layout__input">
                                    </div>
                                    <div class="poll-option__field">
                                        <span>End:</span>
                                        <input type="datetime-local" name="poll_ends[]" class="form-layout__input">
                                    </div>
                                    <button type="button"
                                        class="btn-group__item btn-group__item--danger btn-group__item--small poll-remove-btn">X</button>
                                </div>
                            </div>

                            <button type="button" id="add-option-btn" class="btn-group__item">+ Add Another
                                Date</button>
                        </div>
                    </div>

                    <li class="form-layout__item form-layout__item--description">
                        <label for="description_event" class="form-layout__label">Description:</label>
                        <textarea id="description_event" name="description_event"
                            class="form-layout__input form-layout__input--description"
                            rows="5"><?php echo htmlspecialchars($event_data['description_event'] ?? ''); ?></textarea>
                    </li>
                </ul>

                <h3>Invite Friends</h3>
                <?php if (!empty($friends_list)): ?>
                    <div class="friends-invite-list">
                        <?php foreach ($friends_list as $friend): ?>
                            <label class="friends-invite-list__item">
                                <input type="checkbox" name="invitee_ids[]" value="<?php echo $friend['id']; ?>">
                                <span><?php echo htmlspecialchars($friend['first_name'] . ' ' . $friend['surname']); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text--muted">You have no friends to invite yet. <a href="friends">Find friends</a></p>
                <?php endif; ?>

                <div class="btn-group">
                    <button type="submit" name="submit_update_event"
                        class="btn-group__item btn-group__item--primary">Save Changes</button>
                    <button type="submit" name="submit_delete_event" class="btn-group__item btn-group__item--danger"
                        data-confirm="Are you sure you want to delete this event? This action cannot be undone.">Delete
                        Event</button>
                    <a href="events/detail/<?php echo $event_data['id']; ?>" class="btn-group__item">Cancel</a>
                </div>

            </form>
        </div>
    </div>
</main>