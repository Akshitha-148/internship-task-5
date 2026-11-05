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

/* =========================
   DELETE COURSE
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_course'])) {

    $courseId = (int)($_POST['course_id'] ?? 0);

    if ($courseId > 0) {
        try {
            $stmt = $pdo->prepare(
                'DELETE FROM courses WHERE id = ?'
            );
            $stmt->execute([$courseId]);

            $message = 'Course deleted successfully.';

        } catch (PDOException $e) {
            $error = 'Unable to delete this course. It may already have enrollments.';
        }
    }
}


/* =========================
   ADD COURSE
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course'])) {

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $instructor = trim($_POST['instructor'] ?? '');
    $image = trim($_POST['image'] ?? '');
    $duration = trim($_POST['duration'] ?? '');

    if (
        $title === '' ||
        $description === '' ||
        $category === '' ||
        $instructor === ''
    ) {

        $error = 'Please fill in all required fields.';

    } else {

        try {

            $stmt = $pdo->prepare("
                INSERT INTO courses
                (title, description, category, instructor, image, duration)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $title,
                $description,
                $category,
                $instructor,
                $image,
                $duration
            ]);

            $message = 'Course added successfully.';

        } catch (PDOException $e) {

            $error = 'Unable to add the course.';
        }
    }
}


/* =========================
   EDIT COURSE
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_course'])) {

    $courseId = (int)($_POST['course_id'] ?? 0);

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $instructor = trim($_POST['instructor'] ?? '');
    $image = trim($_POST['image'] ?? '');
    $duration = trim($_POST['duration'] ?? '');

    if (
        $courseId <= 0 ||
        $title === '' ||
        $description === '' ||
        $category === '' ||
        $instructor === ''
    ) {

        $error = 'Please fill in all required fields.';

    } else {

        try {

            $stmt = $pdo->prepare("
                UPDATE courses
                SET title = ?,
                    description = ?,
                    category = ?,
                    instructor = ?,
                    image = ?,
                    duration = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $title,
                $description,
                $category,
                $instructor,
                $image,
                $duration,
                $courseId
            ]);

            $message = 'Course updated successfully.';

        } catch (PDOException $e) {

            $error = 'Unable to update the course.';
        }
    }
}


/* =========================
   FETCH COURSES
========================= */

$stmt = $pdo->query("
    SELECT id,
           title,
           description,
           category,
           instructor,
           image,
           duration,
           created_at
    FROM courses
    ORDER BY id DESC
");

$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Manage Courses | EduConnect';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-5">

    <div class="mb-4">

        <h2>Manage Courses</h2>

        <p class="text-muted">
            Add, edit and manage EduConnect courses.
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


    <!-- ADD COURSE -->

    <div class="card shadow-sm mb-5">

        <div class="card-body">

            <h4 class="mb-4">
                Add New Course
            </h4>

            <form method="post">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Course Title *
                        </label>

                        <input
                            type="text"
                            name="title"
                            class="form-control"
                            required
                        >

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Category *
                        </label>

                        <input
                            type="text"
                            name="category"
                            class="form-control"
                            placeholder="Programming"
                            required
                        >

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Instructor *
                        </label>

                        <input
                            type="text"
                            name="instructor"
                            class="form-control"
                            required
                        >

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Duration
                        </label>

                        <input
                            type="text"
                            name="duration"
                            class="form-control"
                            placeholder="8 Weeks"
                        >

                    </div>


                    <div class="col-md-12 mb-3">

                        <label class="form-label">
                            Course Description *
                        </label>

                        <textarea
                            name="description"
                            class="form-control"
                            rows="4"
                            required
                        ></textarea>

                    </div>


                    <div class="col-md-12 mb-3">

                        <label class="form-label">
                            Image URL
                        </label>

                        <input
                            type="text"
                            name="image"
                            class="form-control"
                            placeholder="images/python.jpg"
                        >

                    </div>

                </div>


                <button
                    type="submit"
                    name="add_course"
                    class="btn btn-primary">

                    Add Course

                </button>

            </form>

        </div>

    </div>


    <!-- COURSE LIST -->

    <h4 class="mb-3">
        Existing Courses
    </h4>


    <div class="table-responsive">

        <table class="table table-bordered table-hover align-middle">

            <thead class="table-dark">

                <tr>

                    <th>ID</th>
                    <th>Course</th>
                    <th>Category</th>
                    <th>Instructor</th>
                    <th>Duration</th>
                    <th>Actions</th>

                </tr>

            </thead>


            <tbody>

                <?php if (empty($courses)): ?>

                    <tr>

                        <td colspan="6" class="text-center">
                            No courses found.
                        </td>

                    </tr>

                <?php else: ?>

                    <?php foreach ($courses as $course): ?>

                        <tr>

                            <td>
                                <?= (int)$course['id'] ?>
                            </td>


                            <td>

                                <strong>
                                    <?= htmlspecialchars($course['title']) ?>
                                </strong>

                                <br>

                                <small class="text-muted">

                                    <?= htmlspecialchars(
                                        mb_strimwidth(
                                            $course['description'],
                                            0,
                                            80,
                                            '...'
                                        )
                                    ) ?>

                                </small>

                            </td>


                            <td>
                                <?= htmlspecialchars($course['category']) ?>
                            </td>


                            <td>
                                <?= htmlspecialchars($course['instructor']) ?>
                            </td>


                            <td>
                                <?= htmlspecialchars($course['duration']) ?>
                            </td>


                            <td>

                                <a
                                    href="?edit=<?= (int)$course['id'] ?>"
                                    class="btn btn-warning btn-sm">

                                    Edit

                                </a>


                                <form
                                    method="post"
                                    class="d-inline"
                                    onsubmit="return confirm('Are you sure you want to delete this course?');">

                                    <input
                                        type="hidden"
                                        name="course_id"
                                        value="<?= (int)$course['id'] ?>"
                                    >

                                    <button
                                        type="submit"
                                        name="delete_course"
                                        class="btn btn-danger btn-sm">

                                        Delete

                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </div>


    <?php
    /* =========================
       EDIT FORM
    ========================= */

    if (isset($_GET['edit'])):

        $editId = (int)$_GET['edit'];

        $stmt = $pdo->prepare("
            SELECT id,
                   title,
                   description,
                   category,
                   instructor,
                   image,
                   duration
            FROM courses
            WHERE id = ?
        ");

        $stmt->execute([$editId]);

        $editCourse = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($editCourse):
    ?>

        <div class="card shadow-sm mt-5">

            <div class="card-body">

                <h4 class="mb-4">
                    Edit Course
                </h4>


                <form method="post">

                    <input
                        type="hidden"
                        name="course_id"
                        value="<?= (int)$editCourse['id'] ?>"
                    >


                    <div class="mb-3">

                        <label class="form-label">
                            Course Title
                        </label>

                        <input
                            type="text"
                            name="title"
                            class="form-control"
                            value="<?= htmlspecialchars($editCourse['title']) ?>"
                            required
                        >

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Category
                        </label>

                        <input
                            type="text"
                            name="category"
                            class="form-control"
                            value="<?= htmlspecialchars($editCourse['category']) ?>"
                            required
                        >

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Instructor
                        </label>

                        <input
                            type="text"
                            name="instructor"
                            class="form-control"
                            value="<?= htmlspecialchars($editCourse['instructor']) ?>"
                            required
                        >

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Duration
                        </label>

                        <input
                            type="text"
                            name="duration"
                            class="form-control"
                            value="<?= htmlspecialchars($editCourse['duration']) ?>"
                        >

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Description
                        </label>

                        <textarea
                            name="description"
                            class="form-control"
                            rows="5"
                            required
                        ><?= htmlspecialchars($editCourse['description']) ?></textarea>

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Image URL
                        </label>

                        <input
                            type="text"
                            name="image"
                            class="form-control"
                            value="<?= htmlspecialchars($editCourse['image']) ?>"
                        >

                    </div>


                    <button
                        type="submit"
                        name="update_course"
                        class="btn btn-success">

                        Update Course

                    </button>


                    <a
                        href="courses.php"
                        class="btn btn-secondary">

                        Cancel

                    </a>

                </form>

            </div>

        </div>

    <?php
        endif;
    endif;
    ?>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>