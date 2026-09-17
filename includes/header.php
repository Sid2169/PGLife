<a class="skip-link" href="#main-content">Skip to main content</a>
<header class="header sticky-top">
    <nav class="navbar navbar-expand-md navbar-light" aria-label="Main navigation">
        <a class="navbar-brand" href="index.php">
            <img src="img/logo.png" alt="PG Life home" />
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#my-navbar" aria-controls="my-navbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="my-navbar">
            <ul class="navbar-nav">
                <?php
                if (!isset($_SESSION["user_id"])) {
                ?>
                    <li class="nav-item">
                        <a class="nav-link nav-signup" href="#" data-toggle="modal" data-target="#signup-modal">
                            <i class="fas fa-user" aria-hidden="true"></i>Signup
                        </a>
                    </li>
                    <li class="nav-vl" aria-hidden="true"></li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-toggle="modal" data-target="#login-modal">
                            <i class="fas fa-sign-in-alt" aria-hidden="true"></i>Login
                        </a>
                    </li>
                <?php
                } else {
                ?>
                    <li class="nav-name">
                        Hi, <?php echo e($_SESSION["full_name"]); ?>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-user" aria-hidden="true"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-vl" aria-hidden="true"></li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt" aria-hidden="true"></i>Logout
                        </a>
                    </li>
                <?php
                }
                ?>
                <li class="nav-vl" aria-hidden="true"></li>
                <li class="nav-item">
                    <button type="button" class="nav-link theme-toggle" id="theme-toggle"
                        aria-pressed="false" aria-label="Toggle light or dark theme"
                        title="Toggle light or dark theme">
                        <i class="fas fa-moon" aria-hidden="true"></i>
                    </button>
                </li>
            </ul>
        </div>
    </nav>
</header>

<div id="loading" role="status" aria-live="polite">
    <span class="sr-only">Loading, please wait.</span>
</div>
