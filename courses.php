```php
<?php
session_start();

require_once __DIR__ . '/config/database.php';

$pageTitle = 'All Courses | EduConnect';

$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');

/* Fetch courses */
$sql = "SELECT id, title, description, category, instructor, image, duration
        FROM courses
        WHERE 1=1";

$params = [];

if ($search !== '') {
    $sql .= " AND (title LIKE :title OR instructor LIKE :instructor)";
    $params['title'] = "%{$search}%";
    $params['instructor'] = "%{$search}%";
}

if ($category !== '') {
    $sql .= " AND category = :category";
    $params['category'] = $category;
}

$sql .= " ORDER BY id ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Fetch categories */
$categories = $pdo->query(
    "SELECT DISTINCT category
     FROM courses
     WHERE category IS NOT NULL
     AND category <> ''
     ORDER BY category ASC"
)->fetchAll(PDO::FETCH_COLUMN);

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<style>

/* ===== PAGE ===== */

body {
    background: #faf8ff;
    color: #29223b;
}

/* ===== HERO ===== */

.ec-courses-hero {
    position: relative;
    overflow: hidden;
    background:
        radial-gradient(circle at 85% 20%,
            rgba(236, 72, 153, 0.25),
            transparent 30%),
        radial-gradient(circle at 10% 90%,
            rgba(168, 85, 247, 0.22),
            transparent 30%),
        linear-gradient(135deg,
            #171329 0%,
            #351b51 55%,
            #63378b 100%);

    color: white;
    padding: 75px 20px 85px;
    text-align: center;
}

.ec-courses-hero h1 {
    font-size: clamp(2.5rem, 6vw, 4rem);
    font-weight: 900;
    margin-bottom: 15px;
}

.ec-gradient-text {
    background: linear-gradient(
        90deg,
        #c084fc,
        #f472b6,
        #ff8b7e
    );

    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.ec-courses-hero p {
    max-width: 650px;
    margin: auto;
    color: #ddd5ea;
    font-size: 1.05rem;
    line-height: 1.8;
}

/* ===== CONTENT ===== */

.ec-courses-content {
    padding: 55px 0 80px;
}

/* ===== SEARCH ===== */

.ec-search-box {
    background: white;
    border-radius: 20px;
    padding: 25px;
    margin-top: -35px;
    position: relative;
    z-index: 5;

    box-shadow:
        0 15px 45px rgba(55, 35, 90, 0.12);

    border: 1px solid #eee7f7;
}

.ec-search-box input,
.ec-search-box select {
    height: 52px;
    border: 1px solid #e5def0;
    border-radius: 10px;
    color: #29223b;
}

.ec-search-box input:focus,
.ec-search-box select:focus {
    border-color: #a855f7;

    box-shadow:
        0 0 0 4px rgba(168, 85, 247, 0.10);
}

.ec-search-btn {
    height: 52px;
    width: 100%;
    border: none;
    border-radius: 10px;

    background:
        linear-gradient(
            135deg,
            #7c3aed,
            #ec4899
        );

    color: white;
    font-weight: 700;

    transition: 0.3s;
}

.ec-search-btn:hover {
    color: white;
    transform: translateY(-2px);

    box-shadow:
        0 12px 25px rgba(124, 58, 237, 0.28);
}

/* ===== RESULT ===== */

.ec-result {
    display: flex;
    justify-content: space-between;
    align-items: center;

    margin: 35px 0 22px;
}

.ec-result h3 {
    color: #171329;
    font-size: 20px;
    font-weight: 800;
    margin: 0;
}

.ec-result-count {
    color: #7c3aed;
}

/* ===== COURSE CARD ===== */

.ec-course-card {
    height: 100%;
    background: white;

    border: 1px solid #eee7f7;
    border-radius: 18px;

    overflow: hidden;

    box-shadow:
        0 10px 30px rgba(55, 35, 90, 0.07);

    transition:
        transform 0.3s ease,
        box-shadow 0.3s ease;
}

.ec-course-card:hover {
    transform: translateY(-8px);

    box-shadow:
        0 22px 45px rgba(76, 38, 119, 0.16);
}

/* ===== IMAGE ===== */

.ec-course-image-wrapper {
    height: 220px;
    overflow: hidden;
    background:
        linear-gradient(
            135deg,
            #7c3aed,
            #ec4899
        );
}

.ec-course-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;

    transition: transform 0.5s ease;
}

.ec-course-card:hover .ec-course-image {
    transform: scale(1.07);
}

/* ===== CARD BODY ===== */

.ec-course-body {
    padding: 23px;

    display: flex;
    flex-direction: column;

    min-height: 330px;
}

.ec-category {
    display: inline-flex;
    align-self: flex-start;

    padding: 6px 12px;

    border-radius: 50px;

    background:
        linear-gradient(
            135deg,
            #f3e8ff,
            #fce7f3
        );

    color: #7c3aed;

    font-size: 12px;
    font-weight: 800;

    margin-bottom: 14px;
}

.ec-course-title {
    color: #171329;

    font-size: 20px;
    font-weight: 800;

    line-height: 1.3;

    margin-bottom: 12px;
}

.ec-course-description {
    color: #756d8c;

    font-size: 14px;
    line-height: 1.7;

    margin-bottom: 18px;
}

.ec-course-info {
    color: #756d8c;

    font-size: 13px;

    margin-bottom: 8px;
}

.ec-course-info strong {
    color: #29223b;
}

.ec-duration {
    color: #7c3aed;

    font-size: 13px;
    font-weight: 700;

    margin-bottom: 18px;
}

/* ===== BUTTON ===== */

.ec-view-btn {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    gap: 8px;

    width: 100%;

    padding: 11px 18px;

    border: none;
    border-radius: 10px;

    background:
        linear-gradient(
            135deg,
            #7c3aed,
            #a855f7
        );

    color: white !important;

    font-weight: 700;
    text-decoration: none;

    margin-top: auto;

    transition: 0.3s;
}

.ec-view-btn:hover {
    color: white !important;

    transform: translateY(-2px);

    box-shadow:
        0 12px 25px rgba(124, 58, 237, 0.30);
}

/* ===== CLEAR FILTER ===== */

.ec-clear-btn {
    display: inline-flex;

    align-items: center;

    gap: 7px;

    padding: 9px 16px;

    border: 1px solid #a855f7;

    border-radius: 9px;

    color: #7c3aed !important;

    text-decoration: none;

    font-size: 14px;

    font-weight: 700;
}

.ec-clear-btn:hover {
    background: #7c3aed;
    color: white !important;
}

/* ===== NO COURSES ===== */

.ec-no-courses {
    background: white;

    border: 1px solid #eee7f7;

    border-radius: 20px;

    padding: 60px 25px;

    text-align: center;

    box-shadow:
        0 10px 30px rgba(55, 35, 90, 0.07);
}

.ec-no-courses i {
    font-size: 50px;
    color: #a855f7;
}

.ec-no-courses h3 {
    color: #171329;
    font-weight: 800;
}

.ec-no-courses p {
    color: #756d8c;
}

/* ===== MOBILE ===== */

@media (max-width: 768px) {

    .ec-courses-hero {
        padding: 55px 15px 70px;
    }

    .ec-courses-hero h1 {
        font-size: 2.6rem;
    }

    .ec-search-box {
        margin-top: -25px;
        padding: 18px;
    }

    .ec-result {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }

    .ec-course-image-wrapper {
        height: 200px;
    }

    .ec-course-body {
        min-height: auto;
    }
}

</style>


<!-- ===== HERO ===== -->

<section class="ec-courses-hero">

    <div class="container">

        <h1>
            Explore
            <span class="ec-gradient-text">
                All Courses
            </span>
        </h1>

        <p>
            Discover practical technology courses, build new skills,
            and take the next step in your learning journey with
            EduConnect.
        </p>

    </div>

</section>


<!-- ===== MAIN CONTENT ===== -->

<div class="container ec-courses-content">

    <!-- SEARCH -->

    <div class="ec-search-box">

        <form method="GET" action="courses.php">

            <div class="row g-3">

                <div class="col-lg-6">

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search course or instructor..."
                        value="<?= htmlspecialchars(
                            $search,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                </div>

                <div class="col-lg-4">

                    <select
                        name="category"
                        class="form-select"
                    >

                        <option value="">
                            All Categories
                        </option>

                        <?php foreach ($categories as $cat): ?>

                            <option
                                value="<?= htmlspecialchars(
                                    $cat,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                <?= ($category === $cat)
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= htmlspecialchars(
                                    $cat,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="col-lg-2 d-grid">

                    <button
                        type="submit"
                        class="ec-search-btn"
                    >
                        <i class="bi bi-search"></i>
                        Search
                    </button>

                </div>

            </div>

        </form>

    </div>


    <!-- RESULT -->

    <div class="ec-result">

        <h3>
            <span class="ec-result-count">
                <?= count($courses) ?>
            </span>

            Course<?= count($courses) == 1 ? '' : 's' ?>
            Found
        </h3>

        <?php if ($search !== '' || $category !== ''): ?>

            <a
                href="courses.php"
                class="ec-clear-btn"
            >
                <i class="bi bi-arrow-counterclockwise"></i>
                Clear Filters
            </a>

        <?php endif; ?>

    </div>


    <!-- COURSES -->

    <div class="row g-4">

        <?php if (!empty($courses)): ?>

            <?php foreach ($courses as $course): ?>

                <?php

                /* Fix image path automatically */

                $image = trim($course['image'] ?? '');

                if ($image === '') {

                    $image_path = 'images/course-placeholder.jpg';

                } elseif (
                    strpos($image, 'http://') === 0 ||
                    strpos($image, 'https://') === 0
                ) {

                    $image_path = $image;

                } elseif (
                    strpos($image, 'images/') === 0
                ) {

                    $image_path = $image;

                } else {

                    $image_path = 'images/' . $image;
                }

                ?>

                <div class="col-sm-6 col-lg-4">

                    <div class="ec-course-card">

                        <!-- IMAGE -->

                        <div class="ec-course-image-wrapper">

                            <img
                                src="<?= htmlspecialchars(
                                    $image_path,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                class="ec-course-image"
                                alt="<?= htmlspecialchars(
                                    $course['title'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                onerror="this.onerror=null;this.src='images/course-placeholder.jpg';"
                            >

                        </div>


                        <!-- DETAILS -->

                        <div class="ec-course-body">

                            <!-- CATEGORY -->

                            <span class="ec-category">

                                <i class="bi bi-bookmark-fill me-1"></i>

                                <?= htmlspecialchars(
                                    $course['category']
                                        ?? 'General',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </span>


                            <!-- TITLE -->

                            <h2 class="ec-course-title">

                                <?= htmlspecialchars(
                                    $course['title'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </h2>


                            <!-- DESCRIPTION -->

                            <p class="ec-course-description">

                                <?php

                                $description =
                                    $course['description'] ?? '';

                                if (function_exists('mb_strimwidth')) {

                                    echo htmlspecialchars(
                                        mb_strimwidth(
                                            $description,
                                            0,
                                            125,
                                            '...'
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                } else {

                                    echo htmlspecialchars(
                                        substr(
                                            $description,
                                            0,
                                            125
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                }

                                ?>

                            </p>


                            <!-- INSTRUCTOR -->

                            <div class="ec-course-info">

                                <strong>
                                    <i class="bi bi-person"></i>
                                    Instructor:
                                </strong>

                                <?= htmlspecialchars(
                                    $course['instructor']
                                        ?? 'EduConnect Team',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>


                            <!-- DURATION -->

                            <div class="ec-duration">

                                <i class="bi bi-clock"></i>

                                <?= htmlspecialchars(
                                    $course['duration']
                                        ?? 'Not specified',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>


                            <!-- VIEW COURSE -->

                            <a
                                href="course-details.php?id=<?= (int)$course['id'] ?>"
                                class="ec-view-btn"
                            >

                                View Course

                                <i class="bi bi-arrow-right"></i>

                            </a>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <!-- NO RESULTS -->

            <div class="col-12">

                <div class="ec-no-courses">

                    <i class="bi bi-search"></i>

                    <h3>
                        No Courses Found
                    </h3>

                    <p>
                        Try a different search term or category.
                    </p>

                    <a
                        href="courses.php"
                        class="ec-clear-btn"
                    >
                        View All Courses
                    </a>

                </div>

            </div>

        <?php endif; ?>

    </div>

</div>


<?php require_once __DIR__ . '/includes/footer.php'; ?>
```
