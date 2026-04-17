<main>
    <h1>Edit Profile</h1>

    <div class="main-content">

        <form id="editProfileForm" action="<?php echo $domena . '/profile'; ?>" method="POST"
            enctype="multipart/form-data">

            <!-- SECTION: Avatar upload -->
            <div class="section-card">
                <div class="profile-header-card--secondary">
                    <?php if (!empty($user_data['avatar_url']) && file_exists($user_data['avatar_url'])): ?>
                        <img src="<?php echo $domena . '/' . htmlspecialchars($user_data['avatar_url']); ?>" alt="Avatar"
                            class="profile-header-card__avatar profile-header-card__avatar--img">
                    <?php else: ?>
                        <div class="profile-header-card__avatar">
                            <?php
                            $initials = strtoupper(substr($user_data['first_name'], 0, 1) . substr($user_data['surname'], 0, 1));
                            echo htmlspecialchars($initials);
                            ?>
                        </div>
                    <?php endif; ?>
                    <div class="profile-header-card__info">
                        <h2 class="profile-header-card__name">
                            <?php echo htmlspecialchars($user_data['first_name'] . ' ' . $user_data['surname']); ?>
                        </h2>
                    </div>
                    <div class="grid--primary">
                        <label for="avatar" class="grid--primary__label">Upload new photo:</label>
                        <div class="grid--primary__item">
                            <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp">
                            <span class="text--muted">Max 2MB, JPEG, PNG, WebP</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION: Personal information -->
            <section class="section-card">
                <h3 class="section-card__title">Personal Information</h3>

                <div class="grid--primary">
                    <div class="grid--primary__item">
                        <label for="first_name" class="grid--primary__label">First name:</label>
                        <input type="text" id="first_name" name="first_name" class="grid--primary__input"
                            value="<?php echo htmlspecialchars($user_data['first_name']); ?>">
                    </div>
                    <div class="grid--primary__item">
                        <label for="surname" class="grid--primary__label">Last name:</label>
                        <input type="text" id="surname" name="surname" class="grid--primary__input"
                            value="<?php echo htmlspecialchars($user_data['surname']); ?>">
                    </div>
                    <div class="grid--primary__item">
                        <label for="email" class="grid--primary__label">Email:</label>
                        <input type="email" id="email" name="email" class="grid--primary__input"
                            value="<?php echo htmlspecialchars($user_data['email']); ?>">
                    </div>
                    <div class="grid--primary__item">
                        <label for="tel" class="grid--primary__label">Phone:</label>
                        <input type="text" id="tel" name="tel" class="grid--primary__input"
                            value="<?php echo htmlspecialchars($user_data['tel'] ?? ''); ?>">
                    </div>
                    <div class="grid--primary__item">
                        <label for="gender" class="grid--primary__label">Gender:</label>
                        <select name="gender" id="gender" class="grid--primary__input">
                            <option value="male" <?php echo $user_data['gender'] === 'male' ? 'selected' : ''; ?>>Male
                            </option>
                            <option value="female" <?php echo $user_data['gender'] === 'female' ? 'selected' : ''; ?>>
                                Female</option>
                            <option value="other" <?php echo $user_data['gender'] === 'other' ? 'selected' : ''; ?>>Other
                            </option>
                        </select>
                    </div>
                    <div class="grid--primary__item">
                        <label for="birth_year" class="grid--primary__label">Birth Year:</label>
                        <input type="number" id="birth_year" name="birth_year" class="grid--primary__input"
                            value="<?php echo htmlspecialchars($user_data['birth_year']); ?>">
                    </div>
                </div>
            </section>

            <!-- SECTION: Account -->
            <section class="section-card">
                <h3 class="section-card__title">Account</h3>

                <div class="grid--primary">
                    <div class="grid--primary__item">
                        <span class="grid--primary__label">Role:</span>
                        <span class="text-color--primary">
                            <?php echo htmlspecialchars($user_data['role']); ?>
                            <span class="text--muted">(Cannot be changed)</span>
                        </span>
                    </div>
                    <div class="grid--primary__item">
                        <span class="grid--primary__label">Member     since:</span>
                        <span class="text-color--primary">
                            <?php echo date('d.m.Y', strtotime($user_data['created_at'])); ?>
                        </span>
                    </div>
                    <div class="grid--primary__item">
                        <label for="description_user" class="grid--primary__label">Description:</label>
                        <textarea id="description_user" name="description_user" class="grid--primary__input"
                            rows="3"><?php echo htmlspecialchars($user_data['description_user'] ?? ''); ?></textarea>
                    </div>
                </div>
            </section>

            <!-- SECTION: Password -->
            <section class="section-card">
                <h3 class="section-card__title">Change Password</h3>

                <div class="grid--primary">
                    <div class="grid--primary__item">
                        <label for="passwd" class="grid--primary__label">New Password:</label>
                        <input type="password" id="passwd" name="passwd" class="grid--primary__input"
                            placeholder="Leave blank to keep current password">
                    </div>
                </div>
            </section>

            <!-- Buttons -->
            <div class="btn-group">
                <button type="submit" name="submit_update_profile" class="btn-group__item btn-group__item--primary">Save
                    Changes</button>
                <button type="submit" name="submit_delete_profile" class="btn-group__item btn-group__item--danger"
                    data-confirm="Are you sure you want to delete your account? This cannot be undone.">Delete
                    Account</button>
                <a href="profile/show" class="btn-group__item">Cancel</a>
            </div>
        </form>
    </div>

</main>