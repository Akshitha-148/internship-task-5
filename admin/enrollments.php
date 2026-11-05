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

$message = '';
$error = '';

/* Update enrollment status */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {

    $enrollmentId = (int)($_POST['enrollment_id'] ?? 0);
    $status = $_POST['status'] ?? '';

    if (
        $enrollmentId <= 0 ||
        !in_array($status, ['enrolled', 'completed'], true)
    ) {
        $error = 'Invalid enrollment or status.';
    } else {

        try {

            $stmt = $pdo->prepare("
                UPDATE enrollments
                SET status = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $status,
                $enrollmentId
            ]);

            $message = 'Enrollment status updated successfully.';

        } catch (PDOException $e) {

            $error = 'Unable to update enrollment status.';
        }
    }
}


/* Fetch enrollments */
$stmt = $pdo->query("
    SELECT
        e.id,
        e.status,
        e.enrolled_at,
        u.name AS student_name,
        u.email AS student_email,
        c.title AS course_title,
        c.instructor
    FROM enrollments e

    INNER JOIN users u
        ON e.user_id = u.id

    INNER JOIN courses c
        ON e.course_id = c.id

    ORDER BY e.enrolled_at DESC
");

$enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Manage Enrollments | EduConnect';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-5">

    <div class="mb-4">

        <h2>Manage Enrollments</h2>

        <p class="text-muted">
            View and manage student course enrollments.
        </p>

    </div>


    <?php if ($message !== ''): ?>

        <div class="alert alert-success">
            <?= htmlspecialchars($message) ?>
        </div>

    <?php endif; ?>


    <?php if ($error !== ''): ?>

        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>


    <div class="table-responsive">

        <table class="table table-bordered table-hover align-middle">

            <thead class="table-dark">

                <tr>

                    <th>ID</th>
                    <th>Student</th>
                    <th>Email</th>
                    <th>Course</th>
                    <th>Instructor</th>
                    <th>Status</th>
                    <th>Enrolled On</th>
                    <th>Action</th>

                </tr>

            </thead>


            <tbody>

                <?php if (empty($enrollments)): ?>

                    <tr>

                        <td colspan="8" class="text-center">

                            No enrollments found.

                        </td>

                    </tr>

                <?php else: ?>

                    <?php foreach ($enrollments as $enrollment): ?>

                        <tr>

                            <td>
                                <?= (int)$enrollment['id'] ?>
                            </td>


                            <td>
                                <?= htmlspecialchars($enrollment['student_name']) ?>
                            </td>


                            <td>
                                <?= htmlspecialchars($enrollment['student_email']) ?>
                            </td>


                            <td>
                                <?= htmlspecialchars($enrollment['course_title']) ?>
                            </td>


                            <td>
                                <?= htmlspecialchars($enrollment['instructor']) ?>
                            </td>


                            <td>

                                <?php if ($enrollment['status'] === 'completed'): ?>

                                    <span class="badge bg-success">
                                        Completed
                                    </span>

                                <?php else: ?>

                                    <span class="badge bg-primary">
                                        Enrolled
                                    </span>

                                <?php endif; ?>

                            </td>


                            <td>
                                <?= htmlspecialchars($enrollment['enrolled_at']) ?>
                            </td>


                            <td>

                                <form method="post">

                                    <input
                                        type="hidden"
                                        name="enrollment_id"
                                        value="<?= (int)$enrollment['id'] ?>"
                                    >


                                    <select
                                        name="status"
                                        class="form-select form-select-sm mb-2">

                                        <option
                                            value="enrolled"
                                            <?= $enrollment['status'] === 'enrolled' ? 'selected' : '' ?>>
                                            Enrolled
                                        </option>

                                        <option
                                            value="completed"
                                            <?= $enrollment['status'] === 'completed' ? 'selected' : '' ?>>
                                            Completed
                                        </option>

                                    </select>


                                    <button
                                        type="submit"
                                        name="update_status"
                                        class="btn btn-success btn-sm">

                                        Update

                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>