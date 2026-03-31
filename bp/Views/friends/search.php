<main>

    <h1>Find Friends</h1>

    <div class="main-content">
        <div class="events-table-wrapper">

            <!-- VYHLEDÁVACÍ FORMULÁŘ -->
            <form action="<?php echo $domena; ?>/friends/search" method="GET" class="search-bar-row">
                <input type="email" name="email" class="form-layout__input" placeholder="Search by email address..."
                    value="<?php echo htmlspecialchars($search_email ?? ''); ?>">
                <button type="submit"
                    class="btn-group__item btn-group__item--primary">Search</button>
            </form>

            <!-- VÝSLEDEK HLEDÁNÍ -->
            <?php if (!empty($search_email) && $search_result): ?>
                <table class="events-table">
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Birth Year</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="events-table__row">
                            <td><?php echo htmlspecialchars($search_result['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($search_result['surname']); ?></td>
                            <td><?php echo htmlspecialchars($search_result['birth_year']); ?></td>
                            <td>
                                <form action="<?php echo $domena; ?>/friends" method="POST">
                                    <input type="hidden" name="receiver_id" value="<?php echo $search_result['id']; ?>">
                                    <button type="submit" name="submit_send_request"
                                        class="btn-group__item btn-group__item--primary">
                                        Add Friend
                                    </button>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
            <?php endif; ?>

        </div>

        <div class="btn-group">
            <a href="friends" class="btn-group__item">Back to Friends</a>
        </div>

</main>