<main class="content--wide">
    <h1>Your Planner Dashboard</h1>
    <span>Your upcoming events today:</span>
    <?php if (empty($events_today)): ?>
        <p>You have no events scheduled for today.</p>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events_today as $event): ?>
                    <tr>
                        <td>
                            <a href="events/detail/<?php echo $event['id']; ?>"><?php echo htmlspecialchars($event['event_name']); ?></a>
                        </td>
                        <td><?php echo !empty($event['start_at']) ? date('H:i', strtotime($event['start_at'])) : 'To be decided'; ?>
                            -
                            <?php echo !empty($event['end_at']) ? date('H:i', strtotime($event['end_at'])) : 'To be decided'; ?>
                        </td>
                        <td>
                            <?php if ($event['status'] === 'confirmed'): ?>
                                <img src="images/check-icon.png" alt="Confirmed" title="Confirmed" class="icon--primary">
                            <?php elseif ($event['status'] === 'planning'): ?>
                                <img src="images/planning-icon.png" alt="Planning" title="Planning" class="icon--primary">
                            <?php else: ?>
                                <img src="images/cancel-icon.png" alt="Cancelled" title="Cancelled" class="icon--primary">
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="events/detail/<?php echo $event['id']; ?>">
                                <img class="nav-icon table-icon" src="images/view-icon.png" title="View event"></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div id='calendar'>
        <!-- Calendar will be rendered here using FullCalendar -->
    </div>
</main>