<?php
session_start();

require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

if ($_SESSION['role'] === 'admin') {
    header('Location: admin/dashboard.php');
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT 
        e.id,
        e.status,
        e.enrolled_at,
        c.id AS course_id,
        c.title,
        c.description,
        c.category,
        c.instructor,
        c.image,
        c.duration
    FROM enrollments e
    INNER JOIN courses c ON e.course_id = c.id
    WHERE e.user_id = ?
    ORDER BY e.enrolled_at DESC
");

$stmt->execute([$userId]);
$enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'My Enrollments | EduConnect';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container py-5">

    <h2 class="mb-4">My Enrolled Courses</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            Course enrolled successfully!
        </div>
    <?php endif; ?>

    <?php if (empty($enrollments)): ?>

        <div class="alert alert-info">
            You have not enrolled in any courses yet.
        </div>

        <a href="courses.php" class="btn btn-primary">
            Explore Courses
        </a>

    <?php else: ?>

        <div class="row g-4">

            <?php foreach ($enrollments as $course): ?>

                <div class="col-md-6 col-lg-4">

                    <div class="card h-100 shadow-sm">

                        <?php if (!empty($course['image'])): ?>

                            <img
                                src="<?= htmlspecialchars($course['image']) ?>"
                                class="card-img-top"
                                alt="<?= htmlspecialchars($course['title']) ?>"
                                style="height: 180px; object-fit: cover;"
                            >

                        <?php endif; ?>

                        <div class="card-body">

                            <h5 class="card-title">
                                <?= htmlspecialchars($course['title']) ?>
                            </h5>

                            <p class="card-text">
                                <?= htmlspecialchars($course['description']) ?>
                            </p>

                            <p class="mb-1">
                                <strong>Category:</strong>
                                <?= htmlspecialchars($course['category']) ?>
                            </p>

                            <p class="mb-1">
                                <strong>Instructor:</strong>
                                <?= htmlspecialchars($course['instructor']) ?>
                            </p>

                            <p class="mb-2">
                                <strong>Duration:</strong>
                                <?= htmlspecialchars($course['duration']) ?>
                            </p>

                            <span class="badge bg-success">
                                <?= htmlspecialchars(ucfirst($course['status'])) ?>
                            </span>

                        </div>

                        <div class="card-footer bg-white">

                            <a
                                href="course-details.php?id=<?= (int)$course['course_id'] ?>"
                                class="btn btn-outline-primary btn-sm">
                                View Course
                            </a>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>