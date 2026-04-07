<main>

    <h1>Events</h1>

    <div class="main-content">


        <?php
        // Všechny eventy dáme do jednoho pole pro jednu tabulku
        $all_sections = [
            ['events' => $voting_events, 'label' => 'Planning', 'badge' => 'planning'],
            ['events' => $running_events, 'label' => 'Running', 'badge' => 'running'],
            ['events' => $upcoming_events, 'label' => 'Upcoming', 'badge' => 'upcoming'],
            ['events' => $past_events, 'label' => 'Ended', 'badge' => 'ended'],
        ];
        ?>

        <!-- SEKCE: Čekající pozvánky -->
        <?php if (!empty($pending_invitations)): ?>
            <div class="section-card section-card--wide">
                <h3 class="section-card__title">Pending Invitations (<?php echo count($pending_invitations); ?>)
                </h3>
                <?php foreach ($pending_invitations as $inv): ?>
                    <div class="friend-request-row">
                        <span class="friend-request-row__name">
                            <strong><?php echo htmlspecialchars($inv['event_name']); ?></strong>
                            &mdash; invited by <?php echo htmlspecialchars($inv['first_name'] . ' ' . $inv['surname']); ?>
                            <?php if (!empty($inv['start_at'])): ?>
                                <span class="friend-request-row__date">(<?php echo date('d.m.Y H:i', strtotime($inv['start_at'])); ?>)</span>
                            <?php endif; ?>
                        </span>
                        <div class="friend-request-row__actions">
                            <form action="<?php echo $domena; ?>/events/show" method="POST" class="friend-request-row__form">
                                <input type="hidden" name="registration_id" value="<?php echo $inv['registration_id']; ?>">
                                <button type="submit" name="submit_accept_invitation"
                                    class="btn-group__item btn-group__item--primary">Accept</button>
                            </form>
                            <form action="<?php echo $domena; ?>/events/show" method="POST" class="friend-request-row__form">
                                <input type="hidden" name="registration_id" value="<?php echo $inv['registration_id']; ?>">
                                <button type="submit" name="submit_decline_invitation"
                                    class="btn-group__item"
                                    data-confirm="Are you sure you want to decline this invitation?">Decline</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="events-table-wrapper">

            <div class="search-bar-row">
                <div class="events-search">
                    <input type="text" id="eventsSearch" class="form-layout__input" placeholder="Search events...">
                </div>
                <a href="events/create"
                    class="btn-group__item btn-group__item--primary btn--desktop-only">+ Add
                    Event</a>
            </div>

            <table class="events-table">
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_sections as $section): ?>
                        <?php if (!empty($section['events'])): ?>
                            <?php foreach ($section['events'] as $event): ?>
                                <tr class="events-table__row">
                                    <td class="events-table__name">
                                        <?php echo htmlspecialchars($event['event_name']); ?>
                                    </td>
                                    <td>
                                        <?php echo !empty($event['start_at'])
                                            ? date('d.m.Y H:i', strtotime($event['start_at']))
                                            : '<span class="events-table__tbd">To be decided</span>'; ?>
                                    </td>
                                    <td>
                                        <?php echo !empty($event['end_at'])
                                            ? date('d.m.Y H:i', strtotime($event['end_at']))
                                            : '<span class="events-table__tbd">To be decided</span>'; ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($event['location'] ?? '—'); ?>
                                    </td>
                                    <td>
                                        <span class="event-badge event-badge--<?php echo $section['badge']; ?>">
                                            <?php echo $section['label']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="icons-events-show">
                                            <?php if ($section['badge'] === 'planning'): ?>
                                                <a href="events/detail/<?php echo $event['id']; ?>">
                                                    <img class="nav-icon table-icon" src="images/vote-icon.png" title="Vote">
                                                </a>
                                            <?php else: ?>
                                                <a href="events/detail/<?php echo $event['id']; ?>">
                                                    <img class="nav-icon table-icon" src="images/view-icon.png" title="View event"></a>
                                            <?php endif; ?>

                                            <a href="events/edit/<?php echo $event['id']; ?>">
                                                <img class="nav-icon table-icon" src="images/edit-icon.png" title="Edit">
                                            </a>
                                            <form action="<?php echo $domena; ?>/events/edit/<?php echo $event['id']; ?>"
                                                method="POST"
                                                >
                                                <button type="submit" name="submit_delete_event" class="btn-icon" data-confirm="Are you sure you want to delete this event?">
                                                    <img class="nav-icon table-icon" src="images/delete-icon.png" title="Delete">
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php
                    $total = array_sum(array_map(fn($s) => count($s['events']), $all_sections));
                    if ($total === 0): ?>
                        <tr>
                            <td colspan="6" class="events-table__empty">No events found. <a href="events/create">Create
                                    one!</a></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <a href="events/create" class="fixed-btn fixed-btn--primary">+</a>

        </div>

    </div>
</main>