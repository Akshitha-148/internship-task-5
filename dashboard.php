```php
<?php
session_start();

require_once __DIR__ . '/config/database.php';

// Only logged-in students can access this page
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

// Admin users go to admin dashboard
if ($_SESSION['role'] === 'admin') {
    header('Location: admin/dashboard.php');
    exit;
}

$user_id = $_SESSION['user_id'];

/* =====================================================
   GET TOTAL ENROLLED COURSES
===================================================== */

$stmt = $pdo->prepare(
    "SELECT COUNT(*)
     FROM enrollments
     WHERE user_id = ?"
);

$stmt->execute([$user_id]);

$total_enrollments = $stmt->fetchColumn();


/* =====================================================
   GET COMPLETED COURSES
===================================================== */

$stmt = $pdo->prepare(
    "SELECT COUNT(*)
     FROM enrollments
     WHERE user_id = ?
     AND status = 'completed'"
);

$stmt->execute([$user_id]);

$completed_courses = $stmt->fetchColumn();


/* =====================================================
   GET STUDENT'S ENROLLED COURSES
===================================================== */

$stmt = $pdo->prepare(
    "SELECT
        c.id,
        c.title,
        c.instructor,
        c.category,
        c.image,
        e.status,
        e.enrolled_at

     FROM enrollments e

     JOIN courses c
     ON e.course_id = c.id

     WHERE e.user_id = ?

     ORDER BY e.enrolled_at DESC"
);

$stmt->execute([$user_id]);

$enrolled_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);


include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<style>

/* =====================================================
   STUDENT DASHBOARD
===================================================== */

body {
    background:
        linear-gradient(
            135deg,
            #faf8ff 0%,
            #f7f2ff 50%,
            #fff8f5 100%
        );
}

/* Dashboard container */

.ec-dashboard {
    padding-top: 45px;
    padding-bottom: 70px;
}

/* Welcome */

.ec-welcome-box {
    background:
        linear-gradient(
            135deg,
            #171329,
            #4c2674
        );

    color: white;

    border-radius: 22px;

    padding: 35px;

    margin-bottom: 30px;

    box-shadow:
        0 18px 45px rgba(76, 38, 119, 0.20);
}

.ec-welcome-box h2 {
    font-weight: 800;
    margin-bottom: 8px;
}

.ec-welcome-box p {
    color: #ddd5ea;
    margin: 0;
}

/* Statistics */

.ec-stat-card {
    background: white;

    border: none;

    border-radius: 18px;

    padding: 25px;

    height: 100%;

    box-shadow:
        0 10px 30px rgba(55, 35, 90, 0.08);

    transition: 0.3s;
}

.ec-stat-card:hover {
    transform: translateY(-5px);

    box-shadow:
        0 18px 38px rgba(76, 38, 119, 0.13);
}

.ec-stat-icon {
    width: 55px;
    height: 55px;

    border-radius: 15px;

    display: flex;
    align-items: center;
    justify-content: center;

    background:
        linear-gradient(
            135deg,
            #f3e8ff,
            #fce7f3
        );

    color: #7c3aed;

    font-size: 22px;

    margin-bottom: 15px;
}

.ec-stat-card h5 {
    color: #756d8c;

    font-size: 14px;

    font-weight: 600;
}

.ec-stat-card h2 {
    color: #171329;

    font-size: 34px;

    font-weight: 900;

    margin: 0;
}

/* Section heading */

.ec-dashboard-title {
    color: #171329;

    font-size: 28px;

    font-weight: 800;

    margin-top: 45px;

    margin-bottom: 25px;
}

/* Course card */

.ec-course-card {
    background: white;

    border: none;

    border-radius: 18px;

    overflow: hidden;

    height: 100%;

    box-shadow:
        0 10px 30px rgba(55, 35, 90, 0.08);

    transition:
        transform 0.3s ease,
        box-shadow 0.3s ease;
}

.ec-course-card:hover {
    transform: translateY(-8px);

    box-shadow:
        0 20px 45px rgba(76, 38, 119, 0.16);
}

/* Course image */

.ec-course-image {
    width: 100%;

    height: 190px;

    object-fit: cover;

    display: block;

    transition: transform 0.5s ease;
}

.ec-course-card:hover .ec-course-image {
    transform: scale(1.05);
}

/* Image wrapper */

.ec-image-wrapper {
    height: 190px;

    overflow: hidden;

    position: relative;

    background:
        linear-gradient(
            135deg,
            #7c3aed,
            #ec4899
        );
}

/* Course content */

.ec-course-body {
    padding: 22px;
}

.ec-course-title {
    color: #171329;

    font-size: 20px;

    font-weight: 800;

    margin-bottom: 15px;
}

.ec-course-info {
    color: #756d8c;

    font-size: 14px;

    margin-bottom: 8px;
}

.ec-course-info strong {
    color: #29223b;
}

/* Status */

.ec-status {
    display: inline-block;

    padding: 6px 12px;

    border-radius: 50px;

    background: #dcfce7;

    color: #15803d;

    font-size: 12px;

    font-weight: 700;

    margin-top: 8px;
}

/* View course button */

.ec-view-btn {
    display: inline-flex;

    align-items: center;

    gap: 7px;

    background:
        linear-gradient(
            135deg,
            #7c3aed,
            #a855f7
        );

    border: none;

    color: white !important;

    border-radius: 9px;

    padding: 10px 18px;

    font-weight: 600;

    margin-top: 15px;

    transition: 0.3s;
}

.ec-view-btn:hover {
    transform: translateY(-2px);

    box-shadow:
        0 10px 25px rgba(124,58,237,0.30);
}

/* Explore button */

.ec-explore-btn {
    display: inline-flex;

    align-items: center;

    gap: 8px;

    background:
        linear-gradient(
            135deg,
            #ff6b5f,
            #ec4899
        );

    color: white !important;

    border: none;

    border-radius: 10px;

    padding: 12px 22px;

    font-weight: 700;

    margin-top: 15px;

    transition: 0.3s;
}

.ec-explore-btn:hover {
    transform: translateY(-2px);

    box-shadow:
        0 12px 25px rgba(236,72,153,0.25);
}

/* Empty state */

.ec-empty {
    background: white;

    border-radius: 18px;

    padding: 45px 25px;

    text-align: center;

    box-shadow:
        0 10px 30px rgba(55,35,90,0.07);
}

.ec-empty i {
    font-size: 45px;

    color: #a855f7;

    margin-bottom: 15px;
}

.ec-empty h4 {
    font-weight: 800;

    color: #171329;
}

.ec-empty p {
    color: #756d8c;
}

/* Mobile */

@media (max-width: 576px) {

    .ec-dashboard {
        padding-top: 25px;
    }

    .ec-welcome-box {
        padding: 25px;
    }

    .ec-dashboard-title {
        font-size: 24px;
    }

    .ec-course-image,
    .ec-image-wrapper {
        height: 180px;
    }
}

</style>


<!-- =====================================================
     DASHBOARD
===================================================== -->

<div class="container ec-dashboard">

    <!-- WELCOME -->

    <div class="ec-welcome-box">

        <h2>
            Welcome,
            <?= htmlspecialchars($_SESSION['name']) ?>! 👋
        </h2>

        <p>
            Continue your learning journey with EduConnect.
        </p>

    </div>


    <!-- =================================================
         STATISTICS
    ================================================= -->

    <div class="row g-4">

        <!-- TOTAL ENROLLMENTS -->

        <div class="c
```
