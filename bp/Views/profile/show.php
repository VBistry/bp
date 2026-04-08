<main>
    <h1>Profile</h1>

    <div class="main-content">

        <!-- SEKCE: Hlavička profilu -->
        <div class="profile-header-card">
            <?php if (!empty($user_data['avatar_url']) && file_exists($user_data['avatar_url'])): ?>
                <a href="profile/edit" class="avatar-edit-wrapper">
                    <img src="<?php echo $domena . '/' . htmlspecialchars($user_data['avatar_url']); ?>" alt="Avatar"
                        class="profile-header-card__avatar profile-header-card__avatar--img">
                    <span class="avatar-edit-overlay">✎</span>
                </a>
            <?php else: ?>
                <a href="profile/edit" class="avatar-edit-wrapper">
                    <div class="profile-header-card__avatar">
                        <?php
                        $initials = strtoupper(substr($user_data['first_name'], 0, 1) . substr($user_data['surname'], 0, 1));
                        echo htmlspecialchars($initials);
                        ?>
                    </div>
                    <span class="avatar-edit-overlay">✎</span>
                </a>
            <?php endif; ?>
            <div class="profile-header-card__info">
                <h2 class="profile-header-card__name">
                    <?php echo htmlspecialchars($user_data['first_name'] . ' ' . $user_data['surname']); ?>
                </h2>
            </div>
            <a href="profile/edit" class="btn-group__item profile-header-card__edit-btn">Edit ✎</a>
        </div>

        <a href="profile/edit" class="fixed-btn fixed-btn--edit fixed-btn--mobile-only">✎</a>

        <!-- SEKCE: Osobní informace -->
        <section class="section-card">
            <h3 class="section-card__title">Personal Information</h3>
            
            <div class="grid--primary">
                <div class="grid--primary__item">
                    <span class="grid--primary__label">First Name</span>
                    <span class="grid--primary__value"><?php echo htmlspecialchars($user_data['first_name']); ?></span>
                </div>
                <div class="grid--primary__item">
                    <span class="grid--primary__label">Last Name</span>
                    <span class="grid--primary__value"><?php echo htmlspecialchars($user_data['surname']); ?></span>
                </div>
                <div class="grid--primary__item">
                    <span class="grid--primary__label">Email</span>
                    <span class="grid--primary__value grid--primary__value--raw">
                        <?php echo htmlspecialchars($user_data['email']); ?>
                    </span>
                </div>
                <div class="grid--primary__item">
                    <span class="grid--primary__label">Phone</span>
                    <span
                        class="grid--primary__value"><?php echo htmlspecialchars($user_data['tel'] ?? 'Not specified'); ?></span>
                </div>
                <div class="grid--primary__item">
                    <span class="grid--primary__label">Gender</span>
                    <span class="grid--primary__value"><?php echo htmlspecialchars($user_data['gender']); ?></span>
                </div>
                <div class="grid--primary__item">
                    <span class="grid--primary__label">Birth Year</span>
                    <span class="grid--primary__value"><?php echo htmlspecialchars($user_data['birth_year']); ?></span>
                </div>
            </div>
        </section>

        <!-- SEKCE: Účet -->
        <section class="section-card">
            <h3 class="section-card__title">Account</h3>

            <div class="grid--primary">
                <div class="grid--primary__item">
                    <span class="grid--primary__label">Role</span>
                    <span class="grid--primary__value"><?php echo htmlspecialchars($user_data['role']); ?></span>
                </div>
                <div class="grid--primary__item">
                    <span class="grid--primary__label">Member since</span>
                    <span
                        class="grid--primary__value"><?php echo date('d.m.Y', strtotime($user_data['created_at'])); ?></span>
                </div>
                <div class="grid--primary__item">
                    <span class="grid--primary__label">Email Status(verification)</span>
                    <span class="grid--primary__value">
                        <?php if ($user_data['verified_email']): ?>
                            <span class="email-badge email-badge--verified">Verified</span>
                        <?php else: ?>
                            <span class="email-badge email-badge--unverified">Not verified</span>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="grid--primary__item grid--primary__item--full">
                    <span class="grid--primary__label">Description</span>
                    <span
                        class="grid--primary__value description-style"><?php echo htmlspecialchars($user_data['description_user'] ?? 'No description provided'); ?></span>
                </div>
            </div>
        </section>

    </div>
</main>