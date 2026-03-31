<main>
    <h1>Friends Details</h1>
    <div class="main-content">

        <!-- SEKCE: Hlavička -->
        <header class="profile-header-card">
            <?php if (!empty($friend_data['avatar_url']) && file_exists($friend_data['avatar_url'])): ?>
                <img src="<?php echo $domena . '/' . htmlspecialchars($friend_data['avatar_url']); ?>" alt="Avatar"
                    class="profile-header-card__avatar profile-header-card__avatar--img">
            <?php else: ?>
                <div class="profile-header-card__avatar">
                    <?php
                    $initials = strtoupper(substr($friend_data['first_name'], 0, 1) . substr($friend_data['surname'], 0, 1));
                    echo htmlspecialchars($initials);
                    ?>
                </div>
            <?php endif; ?>
            <div class="profile-header-card__info">
                <h2 class="profile-header-card__name">
                    <?php echo htmlspecialchars($friend_data['first_name'] . ' ' . $friend_data['surname']); ?>
                </h2>
            </div>
        </header>

        <!-- SEKCE: Osobní informace -->
        <section class="section-card">
            <h3 class="section-card__title">Personal Information</h3>

            <div class="grid--primary">
                <div class="grid--primary__item">
                    <span class="grid--primary__label">First Name</span>
                    <span
                        class="grid--primary__value"><?php echo htmlspecialchars($friend_data['first_name']); ?></span>
                </div>
                <div class="grid--primary__item">
                    <span class="grid--primary__label">Last Name</span>
                    <span
                        class="grid--primary__value"><?php echo htmlspecialchars($friend_data['surname']); ?></span>
                </div>
                <div class="grid--primary__item">
                    <span class="grid--primary__label">Email</span>
                    <span
                        class="grid--primary__value grid--primary__value--raw"><?php echo htmlspecialchars($friend_data['email']); ?></span>
                </div>
                <div class="grid--primary__item">
                    <span class="grid--primary__label">Gender</span>
                    <span
                        class="grid--primary__value"><?php echo htmlspecialchars($friend_data['gender']); ?></span>
                </div>
                <div class="grid--primary__item">
                    <span class="grid--primary__label">Birth Year</span>
                    <span
                        class="grid--primary__value"><?php echo htmlspecialchars($friend_data['birth_year']); ?></span>
                </div>
            </div>
        </section>

        <!-- SEKCE: Účet -->
        <section class="section-card">
            <h3 class="section-card__title">Account</h3>

            <div class="grid--primary">
                <div class="grid--primary__item">
                    <span class="grid--primary__label">Member since</span>
                    <span
                        class="grid--primary__value"><?php echo date('d.m.Y', strtotime($friend_data['created_at'])); ?></span>
                </div>
                <div class="grid--primary__item grid--primary__item--full">
                    <span class="grid--primary__label">Description</span>
                    <span class="grid--primary__value description-style">
                        <?php echo htmlspecialchars($friend_data['description_user'] ?? 'No description provided'); ?>
                    </span>
                </div>
            </div>
        </section>

        <!-- AKCE -->
        <div class="btn-group">
            
            <form action="<?php echo $domena; ?>/friends" method="POST" class="full-width">
                <input type="hidden" name="friend_id" value="<?php echo $friend_data['id']; ?>">
                <button type="submit" name="submit_remove_friend" class="btn-group__item btn-group__item--danger"
                    data-confirm="Are you sure you want to remove this friend?">
                    Remove Friend
                </button>
            </form>
            <a href="friends" class="btn-group__item">Back to Friends</a>
        </div>

    </div>
</main>