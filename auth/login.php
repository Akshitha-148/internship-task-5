
<?php
session_start();

require_once __DIR__ . '/../config/database.php';

$error = '';
$email = '';
$redirect = $_GET['redirect'] ?? $_POST['redirect'] ?? 'dashboard.php';

// Allow only safe, relative EduConnect page redirects
$allowedRedirects = [
    'dashboard.php',
    'enrollments.php'
];

if (
    !is_string($redirect) ||
    (
        !in_array($redirect, $allowedRedirects, true) &&
        !preg_match('/^course-details\.php\?id=[1-9][0-9]*$/', $redirect)
    )
) {
    $redirect = 'dashboard.php';
}

if (isset($_SESSION['user_id'])) {
    header('Location: ../' . $redirect);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Please enter a valid email address and password.';
    } else {
        try {
            $stmt = $pdo->prepare(
                'SELECT id, name, email, password, role, email_verified
                 FROM users
                 WHERE email = ?
                 LIMIT 1'
            );
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['password'])) {
                $error = 'Invalid email or password.';
            } elseif (
                isset($user['email_verified']) &&
                (int) $user['email_verified'] !== 1
            ) {
                $error = 'Please verify your email before logging in.';
            } else {
                session_regenerate_id(true);

                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'] ?? 'student';

                header('Location: ../' . $redirect);
                exit;
            }
        } catch (PDOException $e) {
            error_log('EduConnect login error: ' . $e->getMessage());
            $error = 'Unable to log in right now. Please try again.';
        }
    }
}

$pageTitle = 'Login | EduConnect';

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">

            <div class="card border-0 shadow">
                <div class="card-body p-4 p-md-5">

                    <h2 class="text-center mb-2">Welcome Back!</h2>
                    <p class="text-center text-muted mb-4">
                        Login to your EduConnect account
                    </p>

                    <?php if ($error !== ''): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="login.php">
                        <input
                            type="hidden"
                            name="redirect"
                            value="<?= htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8') ?>"
                        >

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                Email Address
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control"
                                placeholder="Enter your email"
                                value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                                autocomplete="email"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                Password
                            </label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                                required
                            >
                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            Login
                        </button>
                    </form>

                    <p class="text-center mt-4 mb-0">
                        Don't have an account?
                        <a href="register.php">Register here</a>
                    </p>

                    <p class="text-center mt-3 mb-0">
                        <a href="../courses.php">Back to Courses</a>
                    </p>

                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>