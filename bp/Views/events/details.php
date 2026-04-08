<main>
    <h1>Event Details</h1>
    <div class="main-content">

        <!-- HLAVIČKA EVENTU -->
        <div class="event-detail__header">
            <?php if ($_SESSION['user_id'] === $event_data['creator'] || $user_data['role'] === 'admin'): ?>
                <a href="events/edit/<?php echo $event_data['id']; ?>" class="btn-group__item profile-header-card__edit-btn">Edit ✎</a>
            <?php endif; ?>

            <h2 class="event-detail__title"><?php echo htmlspecialchars($event_data['event_name']); ?></h2>

            <span class="event-badge event-badge--<?php echo $display_status['badge']; ?>">
                <?php echo $display_status['label']; ?>
            </span>
        </div>

        <!-- SEKCE: Hlasování -->
        <?php if ($event_data['status'] === 'planning'): ?>
            <section class="section-card">
                <h3 class="section-card__title">Vote for the Best Date</h3>
                <span class="event-detail__deadline">
                    Voting deadline: <strong><?php echo date('d.m.Y H:i', strtotime($event_data['voting_deadline'])); ?></strong>
                </span>

                <?php if (!empty($poll_options)): ?>
                    <form action="" method="POST">
                        <input type="hidden" name="event_id" value="<?php echo $event_data['id']; ?>">

                        <div class="poll-options-list">
                            <?php foreach ($poll_options as $option): ?>
                                <?php
                                $votes      = $vote_counts[$option['id']] ?? 0;
                                $is_checked = ($user_current_vote === $option['id']) ? 'checked' : '';
                                ?>
                                <label class="poll-option-card <?php echo $is_checked ? 'poll-option-card--selected' : ''; ?>">
                                    <input type="radio" name="poll_option" value="<?php echo $option['id']; ?>"
                                        <?php echo $is_checked; ?> class="poll-option-card__radio">
                                    <div class="poll-option-card__dates">
                                        <span><?php echo date('d.m.Y H:i', strtotime($option['start_at'])); ?></span>
                                        <span class="poll-option-card__arrow">→</span>
                                        <span><?php echo date('d.m.Y H:i', strtotime($option['end_at'])); ?></span>
                                    </div>
                                    <span class="poll-option-card__votes">(
                                        <?php echo $votes; ?> )
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <button type="submit" name="submit_vote" class="btn-group__item btn-group__item--primary">
                            Submit Vote
                        </button>
                    </form>
                <?php else: ?>
                    <p class="description-style">No dates proposed yet.</p>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <!-- SEKCE: Datum a místo -->
        <section class="section-card">
            <h3 class="section-card__title">Event Details</h3>

            <div class="grid--primary">
                <div class="grid--primary__item">
                    <span class="grid--primary__label">Start Date</span>
                    <span class="grid--primary__value">
                        <?php echo !empty($event_data['start_at'])
                            ? date('d.m.Y H:i', strtotime($event_data['start_at']))
                            : 'To be decided'; ?>
                    </span>
                </div>
                <div class="grid--primary__item">
                    <span class="grid--primary__label">End Date</span>
                    <span class="grid--primary__value">
                        <?php echo !empty($event_data['end_at'])
                            ? date('d.m.Y H:i', strtotime($event_data['end_at']))
                            : 'Not specified'; ?>
                    </span>
                </div>
                <div class="grid--primary__item">
                    <span class="grid--primary__label">Location</span>
                    <span class="grid--primary__value">
                        <?php echo htmlspecialchars($event_data['location'] ?? 'Not specified'); ?>
                    </span>
                </div>
                <div class="grid--primary__item">
                    <span class="grid--primary__label">Created by</span>
                    <span class="grid--primary__value">
                        <?php
                        $creator_name = getEventCreatorById($pdo, $event_data['creator']);
                        echo htmlspecialchars($creator_name);
                        if ($event_data['creator'] === $_SESSION['user_id']) echo ' (You)';
                        ?>
                    </span>
                </div>
                <div class="grid--primary__item">
                    <span class="grid--primary__label">Created on</span>
                    <span class="grid--primary__value">
                        <?php echo date('d.m.Y', strtotime($event_data['created_at'])); ?>
                    </span>
                </div>
                <div class="grid--primary__item grid--primary__item--full">
                    <span class="grid--primary__label">Description</span>
                    <span class="grid--primary__value description-style">
                        <?php echo nl2br(htmlspecialchars($event_data['description_event'] ?? 'No description provided')); ?>
                    </span>
                </div>

                <?php
                $participants = getEventsParticipants($pdo, $event_data['id']);
                $invitations  = getEventsInvitations($pdo, $event_data['id']);
                $declined_invitations = getEventsDeclinedInvitations($pdo, $event_data['id']);
                $all_ids      = array_merge(array_column($participants, 'id'), array_column($invitations, 'id'));
                $is_involved  = $event_data['creator'] === $_SESSION['user_id']
                            || $user_data['role'] === 'admin'
                            || in_array($_SESSION['user_id'], $all_ids);
                ?>
                <?php if ($is_involved): ?>
                    <div class="grid--primary__item">
                        <span class="grid--primary__label">Going</span>
                        <span class="grid--primary__value">
                            <?php
                            if (!empty($participants)) {
                                $names = [];
                                foreach ($participants as $p) {
                                    $name = htmlspecialchars($p['first_name'] . ' ' . $p['surname']);
                                    if ((int) $p['id'] === (int) $_SESSION['user_id']) $name .= ' (You)';
                                    $names[] = $name;
                                }
                                echo implode(', ', $names);
                            } else {
                                echo 'No participants yet.';
                            }
                            ?>
                        </span>
                    </div>
                    <div class="grid--primary__item">
                        <span class="grid--primary__label">Invited</span>
                        <span class="grid--primary__value">
                            <?php
                            if (!empty($invitations)) {
                                $names = [];
                                foreach ($invitations as $i) {
                                    $name = htmlspecialchars($i['first_name'] . ' ' . $i['surname']);
                                    if ((int) $i['id'] === (int) $_SESSION['user_id']) $name .= ' (You)';
                                    $names[] = $name;
                                }
                                echo implode(', ', $names);
                            } else {
                                echo 'No invitations yet.';
                            }
                            ?>
                        </span>
                    </div>
                    <div class="grid--primary__item">
                        <span class="grid--primary__label">Not Going</span>
                        <span class="grid--primary__value">
                            <?php
                            if (!empty($declined_invitations)) {
                                $names = [];
                                foreach ($declined_invitations as $i) {
                                    $name = htmlspecialchars($i['first_name'] . ' ' . $i['surname']);
                                    if ((int) $i['id'] === (int) $_SESSION['user_id']) $name .= ' (You)';
                                    $names[] = $name;
                                }
                                echo implode(', ', $names);
                            } else {
                                echo 'No refused invitations yet.';
                            }
                            ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <?php if ($_SESSION['user_id'] === $event_data['creator'] || $user_data['role'] === 'admin'): ?>
            <a href="events/edit/<?php echo $event_data['id']; ?>" class="fixed-btn fixed-btn--edit fixed-btn--mobile-only">✎</a>
        <?php endif; ?>

        <!-- SEKCE: Moje účast -->
        <?php if ($event_data['status'] !== 'cancelled' && ($user_participation_status !== null || $_SESSION['user_id'] === $event_data['creator'])): ?>
            <section class="section-card">
                <h3 class="section-card__title">My Participation</h3>
                <form action="<?php echo $domena; ?>/events/detail/<?php echo $event_data['id']; ?>" method="POST">
                    <input type="hidden" name="event_id" value="<?php echo $event_data['id']; ?>">
                    <div class="btn-group">
                        <button type="submit" name="submit_participation" value="attending"
                            class="btn-group__item <?php echo $user_participation_status === 'attending' ? 'btn-group__item--primary' : ''; ?>">
                            Going
                        </button>
                        <button type="submit" name="submit_participation" value="declined"
                            class="btn-group__item <?php echo $user_participation_status === 'declined' ? 'btn-group__item--danger' : ''; ?>">
                            Not Going
                        </button>
                    </div>
                </form>
            </section>
        <?php endif; ?>

        <!-- AKCE -->
        <div class="btn-group">
            
            <?php if (($_SESSION['user_id'] === $event_data['creator'] || $user_data['role'] === 'admin') && $event_data['status'] !== 'cancelled'): ?>
                <form class="btn--full-width" action="<?php echo $domena; ?>/events/detail/<?php echo $event_data['id']; ?>" method="POST">
                    <button type="submit" name="submit_cancel_event"
                        class="btn-group__item btn-group__item--danger"
                        data-confirm="Are you sure you want to cancel this event?">
                        Cancel Event
                    </button>
                </form>
            <?php endif; ?>
            <a href="events" class="btn-group__item">Back to Events</a>
            <a href="dashboard" class="btn-group__item">Back to Dashboard</a>
        </div>

    </div>
</main>