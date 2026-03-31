<header class="navbar__brand">
    <span class="navbar__brand-text">ONLINE EVENT PLANNER</span>
</header>

<nav class="navbar__menu">

    <!-- DESKTOP: původní lišta -->
    <ul class="navbar__list">
        <li class="navbar__item--left">
            <img class="nav-icon" src="images/today-icon1.png" alt="Today">
            <span><?php echo date('d.m.Y'); ?></span>
        </li>

        <li class="navbar__item">
            <a href="dashboard"
                class="navbar__link <?php echo $router['controller'] === 'dashboard' ? 'navbar__link--active' : ''; ?>">Dashboard</a>
        </li>
        <li class="navbar__item">
            <a href="events"
                class="navbar__link <?php echo $router['controller'] === 'events' ? 'navbar__link--active' : ''; ?>">Events</a>
        </li>
        <li class="navbar__item">
            <a href="profile"
                class="navbar__link <?php echo $router['controller'] === 'profile' ? 'navbar__link--active' : ''; ?>">Profile</a>
        </li>
        <li class="navbar__item">
            <a href="friends"
                class="navbar__link <?php echo $router['controller'] === 'friends' ? 'navbar__link--active' : ''; ?>">Friends</a>
        </li>

        <li class="navbar__item--right">
            <a href="auth/logout" class="navbar__link navbar__link--danger">
                <img class="nav-icon" src="images/logout-icon1.png" alt="Logout">
            </a>
        </li>
    </ul>

    <!-- MOBIL: lišta s datem + hamburger -->
    <div class="navbar__mobile-bar">
        <div style="display:flex; align-items:center; gap:0.5em; font-weight:bold;">
            <img class="nav-icon" src="images/today-icon1.png" alt="Today">
            <span><?php echo date('d.m.Y'); ?></span>
        </div>

        <button class="navbar__hamburger" id="hamburgerBtn" aria-label="Toggle menu">
            <span class="navbar__hamburger-line"></span>
            <span class="navbar__hamburger-line"></span>
            <span class="navbar__hamburger-line"></span>
        </button>
    </div>

    <!-- MOBIL: dropdown menu -->
    <div class="navbar__dropdown" id="navDropdown">
        <a href="dashboard"
            class="navbar__dropdown-item <?php echo $router['controller'] === 'dashboard' ? 'navbar__dropdown-item--active' : ''; ?>">Dashboard</a>
        <a href="events"
            class="navbar__dropdown-item <?php echo $router['controller'] === 'events' ? 'navbar__dropdown-item--active' : ''; ?>">Events</a>
        <a href="profile"
            class="navbar__dropdown-item <?php echo $router['controller'] === 'profile' ? 'navbar__dropdown-item--active' : ''; ?>">Profile</a>
        <a href="friends"
            class="navbar__dropdown-item <?php echo $router['controller'] === 'friends' ? 'navbar__dropdown-item--active' : ''; ?>">Friends</a>
        <a href="auth/logout" class="navbar__dropdown-item navbar__dropdown-item--danger">Logout</a>
    </div>

</nav>