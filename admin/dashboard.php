<?php
session_start();

require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../dashboard.php');
    exit;
}

/* Total students */
$stmt = $pdo->query("
    SELECT COUNT(*) 
    FROM users 
    WHERE role = 'student'
");
$totalStudents = (int) $stmt->fetchColumn();

/* Total courses */
$stmt = $pdo->query("
    SELECT COUNT(*) 
    FROM courses
");
$totalCourses = (int) $stmt->fetchColumn();

/* Total enrollments */
$stmt = $pdo->query("
    SELECT COUNT(*) 
    FROM enrollments
");
$totalEnrollments = (int) $stmt->fetchColumn();

/* Completed courses */
$stmt = $pdo->query("
    SELECT COUNT(*) 
    FROM enrollments
    WHERE status = 'completed'
");
$completedCourses = (int) $stmt->fetchColumn();

$pageTitle = 'Admin Dashboard | EduConnect';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-5">

    <h2 class="mb-2">Admin Dashboard</h2>

    <p class="text-muted mb-4">
        Welcome, <?= htmlspecialchars($_SESSION['name']) ?>
    </p>

    <!-- Statistics -->
    <div class="row g-4 mb-5">

        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="text-muted">Total Students</h6>
                    <h2><?= $totalStudents ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="text-muted">Total Courses</h6>
                    <h2><?= $totalCourses ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="text-muted">Total Enrollments</h6>
                    <h2><?= $totalEnrollments ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="text-muted">Completed Courses</h6>
                    <h2><?= $completedCourses ?></h2>
                </div>
            </div>
        </div>

    </div>

    <!-- Management -->
    <h4 class="mb-3">Admin Management</h4>

    <div class="row g-4">

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5>Manage Courses</h5>

                    <p class="text-muted">
                        Add, edit and delete courses.
                    </p>

                    <a href="courses.php" class="btn btn-primary">
                        Manage Courses
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5>Manage Users</h5>

                    <p class="text-muted">
                        View registered students and users.
                    </p>

                    <a href="users.php" class="btn btn-primary">
                        Manage Users
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5>Manage Enrollments</h5>

                    <p class="text-muted">
                        View student course enrollments.
                    </p>

                    <a href="enrollments.php" class="btn btn-primary">
                        Manage Enrollments
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>