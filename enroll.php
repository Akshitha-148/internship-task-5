
<?php
session_start();

require_once __DIR__ . '/config/database.php';

// Check whether the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

// Only students can enroll
if (($_SESSION['role'] ?? 'student') !== 'student') {
    http_response_code(403);
    exit('Only student accounts can enroll in courses.');
}

// Accept only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: courses.php');
    exit;
}

// Validate CSRF security token
$submittedToken = $_POST['csrf_token'] ?? '';
$sessionToken = $_SESSION['csrf_token'] ?? '';

if (
    !is_string($submittedToken) ||
    !is_string($sessionToken) ||
    $sessionToken === '' ||
    !hash_equals($sessionToken, $submittedToken)
) {
    exit('Invalid security token. Please open the course again and try.');
}

// Validate course ID
$courseId = filter_input(INPUT_POST, 'course_id', FILTER_VALIDATE_INT);

if (!$courseId || $courseId < 1) {
    header('Location: courses.php');
    exit;
}

// Verify that the course exists
$check = $pdo->prepare('SELECT id FROM courses WHERE id = ?');
$check->execute([$courseId]);

if (!$check->fetchColumn()) {
    http_response_code(404);
    exit('Course not found.');
}

try {
    // Create the enrollment
    $stmt = $pdo->prepare(
        "INSERT INTO enrollments (user_id, course_id, status)
         VALUES (?, ?, 'enrolled')"
    );

    $stmt->execute([
        (int) $_SESSION['user_id'],
        $courseId
    ]);

    header('Location: enrollments.php?success=1');
    exit;

} catch (PDOException $e) {
    // Handle an existing enrollment
    if (($e->errorInfo[1] ?? null) == 1062) {
        header('Location: enrollments.php?already_enrolled=1');
        exit;
    }

    error_log('Enrollment error: ' . $e->getMessage());
    http_response_code(500);
    exit('Enrollment failed. Please try again later.');
}