<main>

    <h1>Friends</h1>

    <div class="main-content">

    <!-- SECTION: Incoming requests -->
    <?php if (!empty($pending_requests)): ?>
        <section class="section-card">
            <h2 class="section-card__title">Pending Requests (<?php echo count($pending_requests); ?>)</h2>
            <?php foreach ($pending_requests as $request): ?>
                <div class="friend-request-row">
                    <span class="friend-request-row__name">
                        <?php echo htmlspecialchars($request['first_name'] . ' ' . $request['surname'] . ' (' . $request['email'] . ')'); ?>
                    </span>
                    <div class="friend-request-row__actions">
                        <form action="<?php echo $domena; ?>/friends" method="POST" class="friend-request-row__form">
                            <input type="hidden" name="friendship_id" value="<?php echo $request['friendship_id']; ?>">
                            <button type="submit" name="submit_accept_request"
                                class="btn-group__item btn-group__item--primary">Accept</button>
                        </form>
                        <form action="<?php echo $domena; ?>/friends" method="POST" class="friend-request-row__form">
                            <input type="hidden" name="friendship_id" value="<?php echo $request['friendship_id']; ?>">
                            <button type="submit" name="submit_decline_request"
                                class="btn-group__item"
                                data-confirm="Are you sure you want to decline this friend request?">Decline</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>
        <?php elseif (!empty($sent_requests)): ?>
            <section class="section-card">
            <h2 class="section-card__title">Sent Requests (<?php echo count($sent_requests); ?>)</h2>
            <?php foreach ($sent_requests as $request): ?>
                <div class="friend-request-row">
                    <span class="friend-request-row__name">
                        <?php echo htmlspecialchars($request['first_name'] . ' ' . $request['surname'] . ' (' . $request['email'] . ')'); ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </section>

    <?php endif; ?>

    <!-- SECTION: Friends list -->
    <div class="events-table-wrapper">

        <div class="search-bar-row">
            <div class="events-search">
                <input type="text" id="friendsSearch" class="form-layout__input" placeholder="Search friends...">
            </div>
            <a href="friends/search" class="btn-group__item btn-group__item--primary btn--desktop-only">+ Add Friend</a>
        </div>

        <table class="events-table">
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Friends Since</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($friends)): ?>
                    <?php foreach ($friends as $friend): ?>
                        <tr class="events-table__row">
                            <td><?php echo htmlspecialchars($friend['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($friend['surname']); ?></td>
                            <td><?php echo htmlspecialchars($friend['email']); ?></td>
                            <td><?php echo date('d.m.Y', strtotime($friend['friends_since'])); ?></td>
                            <td>
                                <div class="icons-events-show icons-events-show--left">
                                    <a href="friends/profile/<?php echo $friend['id']; ?>">
                                        <img class="nav-icon table-icon" src="images/view-icon.png" alt="View profile">
                                    </a>
                                    <form action="<?php echo $domena; ?>/friends" method="POST">
                                        <input type="hidden" name="friend_id" value="<?php echo $friend['id']; ?>">
                                        <button type="submit" name="submit_remove_friend" class="btn-icon"
                                            data-confirm="Are you sure you want to remove this friend?">
                                            <img class="nav-icon table-icon" src="images/delete-icon.png" alt="Remove">
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="events-table__empty">No friends yet. <a href="friends/search">Find
                                someone!</a></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <a href="friends/search" class="fixed-btn fixed-btn--primary fixed-btn--mobile-only">+</a>

    </div>

</main>