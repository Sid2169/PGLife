<div class="header sticky-top">
    <nav class="navbar navbar-expand-md navbar-light">
        <a class="navbar-brand" href="index.php">
            <img src="img/logo.png" alt="PG Life" />
            <span class="brand-name">PG Life</span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#my-navbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="my-navbar">
            <ul class="navbar-nav align-items-md-center">
                <?php
                if (!isset($_SESSION["user_id"])) {
                ?>
                    <li class="nav-item">
                        <a class="nav-link nav-ghost" href="#" data-toggle="modal" data-target="#signup-modal">
                            <i class="fas fa-user-plus"></i>Signup
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-cta" href="#" data-toggle="modal" data-target="#login-modal">
                            <i class="fas fa-sign-in-alt"></i>Login
                        </a>
                    </li>
                <?php
                } else {
                ?>
                    <div class="nav-name">
                        Hi, <?php echo e($_SESSION["full_name"]); ?>
                    </div>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-user"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i>Logout
                        </a>
                    </li>
                <?php
                }
                ?>
                <li class="nav-item nav-theme-item">
                    <button type="button" class="theme-toggle" id="theme-toggle"
                        aria-pressed="false" aria-label="Toggle light or dark theme"
                        title="Toggle light or dark theme">
                        <i class="fas fa-moon" aria-hidden="true"></i>
                    </button>
                </li>
            </ul>
        </div>
    </nav>
</div>

<div id="loading">
</div>
