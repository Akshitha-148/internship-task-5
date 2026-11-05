```php
<?php
session_start();

require_once __DIR__ . '/config/database.php';

/* =====================================================
   CSRF TOKEN
===================================================== */

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


/* =====================================================
   GET COURSE ID
===================================================== */

$courseId = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$courseId || $courseId < 1) {
    header('Location: courses.php');
    exit;
}


/* =====================================================
   FETCH COURSE
===================================================== */

$stmt = $pdo->prepare(
    "SELECT id, title, description, category,
            instructor, image, duration
     FROM courses
     WHERE id = ?"
);

$stmt->execute([$courseId]);

$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    http_response_code(404);
    exit('Course not found. Please return to the courses page.');
}


/* =====================================================
   LOGIN STATUS
===================================================== */

$isLoggedIn = isset($_SESSION['user_id']);

$userRole = $_SESSION['role'] ?? 'student';

$enrolled = false;


/* =====================================================
   CHECK ENROLLMENT
===================================================== */

if ($isLoggedIn && $userRole === 'student') {

    $check = $pdo->prepare(
        "SELECT id
         FROM enrollments
         WHERE user_id = ?
         AND course_id = ?
         LIMIT 1"
    );

    $check->execute([
        (int) $_SESSION['user_id'],
        $courseId
    ]);

    $enrolled = (bool) $check->fetchColumn();
}


/* =====================================================
   PAGE TITLE
===================================================== */

$pageTitle = $course['title'] . ' | EduConnect';


/* =====================================================
   SAFE OUTPUT FUNCTION
===================================================== */

function courseText($value)
{
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}


/* =====================================================
   COURSE IMAGE PATH
===================================================== */

$image = trim($course['image'] ?? '');

if ($image === '') {

    $imagePath = 'images/course-placeholder.jpg';

} elseif (
    strpos($image, 'http://') === 0 ||
    strpos($image, 'https://') === 0
) {

    $imagePath = $image;

} elseif (
    strpos($image, 'images/') === 0
) {

    $imagePath = $image;

} else {

    $imagePath = 'images/' . $image;
}


require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<style>

/* =====================================================
   COURSE DETAILS PAGE
===================================================== */

body {
    background:
        linear-gradient(
            135deg,
            #faf8ff 0%,
            #f7f2ff 55%,
            #fff8f5 100%
        );

    color: #29223b;
}


/* =====================================================
   MAIN CONTAINER
===================================================== */

.ec-details-wrapper {
    padding: 55px 0 85px;
}


/* =====================================================
   BACK BUTTON
===================================================== */

.ec-back-btn {
    display: inline-flex;

    align-items: center;

    gap: 8px;

    text-decoration: none;

    color: #7c3aed !important;

    font-weight: 700;

    margin-bottom: 22px;

    transition: 0.3s;
}

.ec-back-btn:hover {
    color: #ec4899 !important;

    transform: translateX(-4px);
}


/* =====================================================
   MAIN CARD
===================================================== */

.ec-details-card {
    background: white;

    border: 1px solid #eee7f7;

    border-radius: 24px;

    overflow: hidden;

    box-shadow:
        0 20px 55px rgba(55, 35, 90, 0.12);
}


/* =====================================================
   COURSE IMAGE
===================================================== */

.ec-details-image-wrapper {
    height: 430px;

    position: relative;

    overflow: hidden;

    background:
        linear-gradient(
            135deg,
            #7c3aed,
            #ec4899
        );
}

.ec-details-image {
    width: 100%;

    height: 100%;

    object-fit: cover;

    display: block;

    transition: transform 0.6s ease;
}

.ec-details-card:hover .ec-details-image {
    transform: scale(1.02);
}


/* Image overlay */

.ec-details-image-wrapper::after {
    content: "";

    position: absolute;

    inset: 0;

    background:
        linear-gradient(
            to top,
            rgba(23, 19, 41, 0.45),
            transparent 55%
        );

    pointer-events: none;
}


/* =====================================================
   CONTENT
===================================================== */

.ec-details-content {
    padding: 42px;
}


/* =====================================================
   CATEGORY
===================================================== */

.ec-details-category {
    display: inline-flex;

    align-items: center;

    gap: 7px;

    padding: 8px 15px;

    border-radius: 50px;

    background:
        linear-gradient(
            135deg,
            #f3e8ff,
            #fce7f3
        );

    color: #7c3aed;

    font-size: 13px;

    font-weight: 800;

    margin-bottom: 18px;
}


/* =====================================================
   TITLE
===================================================== */

.ec-details-title {
    color: #171329;

    font-size: clamp(2.1rem, 5vw, 3.5rem);

    line-height: 1.1;

    font-weight: 900;

    margin-bottom: 22px;
}


/* =====================================================
   META INFORMATION
===================================================== */

.ec-meta-row {
    display: flex;

    flex-wrap: wrap;

    gap: 25px;

    margin-bottom: 28px;
}

.ec-meta {
    display: flex;

    align-items: center;

    gap: 10px;

    color: #756d8c;

    font-size: 14px;
}

.ec-meta-icon {
    width: 40px;

    height: 40px;

    border-radius: 12px;

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

    font-size: 17px;
}

.ec-meta strong {
    color: #29223b;

    display: block;

    margin-bottom: 2px;
}


/* =====================================================
   DIVIDER
===================================================== */

.ec-divider {
    border: 0;

    border-top: 1px solid #eee7f7;

    margin: 10px 0 30px;
}


/* =====================================================
   ABOUT
===================================================== */

.ec-about-title {
    color: #171329;

    font-size: 24px;

    font-weight: 800;

    margin-bottom: 14px;
}

.ec-about-title span {
    display: inline-block;

    width: 5px;

    height: 25px;

    background:
        linear-gradient(
            #7c3aed,
            #ec4899
        );

    border-radius: 5px;

    vertical-align: middle;

    margin-right: 10px;
}

.ec-description {
    color: #756d8c;

    font-size: 15px;

    line-height: 1.9;

    margin-bottom: 30px;
}


/* =====================================================
   ACTION AREA
===================================================== */

.ec-action-area {
    background:
        linear-gradient(
            135deg,
            #faf7ff,
            #fff7f9
        );

    border: 1px solid #eee7f7;

    border-radius: 16px;

    padding: 22px;

    margin-top: 30px;
}


/* =====================================================
   BUTTONS
===================================================== */

.ec-primary-btn {
    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 9px;

    padding: 12px 25px;

    border: none;

    border-radius: 10px;

    background:
        linear-gradient(
            135deg,
            #7c3aed,
            #ec4899
        );

    color: white !important;

    text-decoration: none;

    font-weight: 800;

    font-size: 15px;

    transition: 0.3s;
}

.ec-primary-btn:hover {
    color: white !important;

    transform: translateY(-3px);

    box-shadow:
        0 14px 28px rgba(124, 58, 237, 0.28);
}

.ec-outline-btn {
    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 8px;

    padding: 11px 23px;

    border: 2px solid #a855f7;

    border-radius: 10px;

    background: white;

    color: #7c3aed !important;

    text-decoration: none;

    font-weight: 800;

    font-size: 15px;

    transition: 0.3s;
}

.ec-outline-btn:hover {
    background: #7c3aed;

    color: white !important;

    transform: translateY(-2px);
}


/* =====================================================
   ENROLLED
===================================================== */

.ec-enrolled-btn {
    display: inline-flex;

    align-items: center;

    gap: 8px;

    padding: 12px 22px;

    border-radius: 10px;

    background:
        linear-gradient(
            135deg,
            #16a34a,
            #22c55e
        );

    color: white;

    font-weight: 800;

    border: none;
}


/* =====================================================
   ADMIN MESSAGE
===================================================== */

.ec-admin-box {
    padding: 15px 18px;

    border-radius: 12px;

    background: #eef6ff;

    border: 1px solid #bfdbfe;

    color: #1e40af;
}


/* =====================================================
   LOGIN MESSAGE
===================================================== */

.ec-login-message {
    color: #756d8c;

    font-size: 14px;

    margin-bottom: 15px;
}


/* =====================================================
   MOBILE
===================================================== */

@media (max-width: 768px) {

    .ec-details-wrapper {
        padding: 35px 15px 60px;
    }

    .ec-details-image-wrapper {
        height: 260px;
    }

    .ec-details-content {
        padding: 28px 22px;
    }

    .ec-details-title {
        font-size: 2.2rem;
    }

    .ec-meta-row {
        flex-direction: column;

        gap: 15px;
    }

    .ec-primary-btn,
    .ec-outline-btn {
        width: 100%;
    }

    .ec-action-area {
        padding: 18px;
    }
}

</style>


<!-- =====================================================
     COURSE DETAILS
===================================================== -->

<div class="container ec-details-wrapper">

    <!-- BACK -->

    <a
        href="courses.php"
        class="ec-back-btn"
    >

        <i class="bi bi-arrow-left"></i>

        Back to Courses

    </a>


    <!-- MAIN CARD -->

    <div class="ec-details-card">

        <!-- COURSE IMAGE -->

        <div class="ec-details-image-wrapper">

            <img
                src="<?= courseText($imagePath) ?>"
                class="ec-details-image"
                alt="<?= courseText($course['title']) ?>"
                onerror="
                    this.onerror=null;
                    this.src='images/course-placeholder.jpg';
                "
            >

        </div>


        <!-- CONTENT -->

        <div class="ec-details-content">


            <!-- CATEGORY -->

            <span class="ec-details-category">

                <i class="bi bi-bookmark-fill"></i>

                <?= courseText(
                    $course['category'] ?? 'General'
                ) ?>

            </span>


            <!-- TITLE -->

            <h1 class="ec-details-title">

                <?= courseText(
                    $course['title']
                ) ?>

            </h1>


            <!-- META -->

            <div class="ec-meta-row">


                <!-- INSTRUCTOR -->

                <div class="ec-meta">

                    <div class="ec-meta-icon">

                        <i class="bi bi-person-fill"></i>

                    </div>

                    <div>

                        <strong>
                            Instructor
                        </strong>

                        <?= courseText(
                            $course['instructor']
                                ?? 'EduConnect Team'
                        ) ?>

                    </div>

                </div>


                <!-- DURATION -->

                <?php if (!empty($course['duration'])): ?>

                    <div class="ec-meta">

                        <div class="ec-meta-icon">

                            <i class="bi bi-clock-fill"></i>

                        </div>

                        <div>

                            <strong>
                                Duration
                            </strong>

                            <?= courseText(
                                $course['duration']
                            ) ?>

                        </div>

                    </div>

                <?php endif; ?>

            </div>


            <hr class="ec-divider">


            <!-- ABOUT -->

            <h2 class="ec-about-title">

                <span></span>

                About This Course

            </h2>


            <p class="ec-description">

                <?= nl2br(
                    courseText(
                        $course['description']
                    )
                ) ?>

            </p>


            <!-- ACTION AREA -->

            <div class="ec-action-area">

                <?php if (!$isLoggedIn): ?>

                    <p class="ec-login-message">
                        Login or create an account to enroll in this course.
                    </p>

                    <div class="d-flex flex-wrap gap-2">

                        <a
                            href="auth/login.php?redirect=<?= urlencode('course-details.php?id=' . $courseId) ?>"
                            class="ec-primary-btn"
                        >

                            <i class="bi bi-box-arrow-in-right"></i>

                            Login to Enroll

                        </a>


                        <a
                            href="auth/register.php"
                            class="ec-outline-btn"
                        >

                            <i class="bi bi-person-plus"></i>

                            Register

                        </a>

                    </div>


                <?php elseif ($userRole === 'admin'): ?>

                    <div class="ec-admin-box">

                        <strong>
                            <i class="bi bi-shield-lock"></i>
                            Admin Account
                        </strong>

                        <br>

                        Student enrollment is not available
                        for admin accounts.

                    </div>


                <?php elseif ($enrolled): ?>

                    <div class="d-flex flex-wrap gap-2">

                        <button
                            class="ec-enrolled-btn"
                            disabled
                        >

                            <i class="bi bi-check-circle-fill"></i>

                            Already Enrolled

                        </button>


                        <a
                            href="enrollments.php"
                            class="ec-outline-btn"
                        >

                            <i class="bi bi-mortarboard-fill"></i>

                            My Enrollments

                        </a>

                    </div>


                <?php else: ?>

                    <form
                        action="enroll.php"
                        method="post"
                    >

                        <input
                            type="hidden"
                            name="course_id"
                            value="<?= (int)$course['id'] ?>"
                        >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= courseText(
                                $_SESSION['csrf_token']
                            ) ?>"
                        >

                        <button
                            type="submit"
                            class="ec-primary-btn"
                        >

                            <i class="bi bi-mortarboard-fill"></i>

                            Enroll Now

                            <i class="bi bi-arrow-right"></i>

                        </button>

                    </form>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>


<?php require_once __DIR__ . '/includes/footer.php'; ?>
```
