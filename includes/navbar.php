
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$baseUrl = '/Internship/TASK5';

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['name'] ?? 'Student';
$userRole = $_SESSION['role'] ?? 'student';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow">
    <div class="container">

        <a class="navbar-brand fw-bold" href="<?= $baseUrl ?>/index.php">
            <span class="text-info">Edu</span>Connect
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#eduConnectNavbar"
            aria-controls="eduConnectNavbar"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="eduConnectNavbar">

            <ul class="navbar-nav ms-auto align-items-lg-center">

                <li class="nav-item">
                    <a class="nav-link" href="<?= $baseUrl ?>/index.php">
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?= $baseUrl ?>/courses.php">
                        Courses
                    </a>
                </li>

                <?php if ($isLoggedIn): ?>

                    <?php if ($userRole === 'admin'): ?>

                        <li class="nav-item">
                            <a class="nav-link"
                               href="<?= $baseUrl ?>/admin/dashboard.php">
                                Admin Dashboard
                            </a>
                        </li>

                    <?php else: ?>

                        <li class="nav-item">
                            <a class="nav-link"
                               href="<?= $baseUrl ?>/dashboard.php">
                                Dashboard
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link"
                               href="<?= $baseUrl ?>/enrollments.php">
                                My Enrollments
                            </a>
                        </li>

                    <?php endif; ?>

                    <li class="nav-item">
                        <span class="nav-link text-info">
                            Hi, <?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </li>

                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-light btn-sm px-3"
                           href="<?= $baseUrl ?>/auth/logout.php">
                            Logout
                        </a>
                    </li>

                <?php else: ?>

                    <li class="nav-item">
                        <a class="nav-link"
                           href="<?= $baseUrl ?>/auth/login.php">
                            Login
                        </a>
                    </li>

                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-info btn-sm px-3"
                           href="<?= $baseUrl ?>/auth/register.php">
                            Register
                        </a>
                    </li>

                <?php endif; ?>

            </ul>

        </div>
    </div>
</nav>