
<?php
session_start();

require_once __DIR__ . '/../config/database.php';

$error = '';
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = 'Enter your name and a valid email address.';

    } elseif (strlen($password) < 8) {

        $error = 'Password must contain at least 8 characters.';

    } elseif ($password !== $confirmPassword) {

        $error = 'Passwords do not match.';

    } else {

        try {

            $check = $pdo->prepare(
                'SELECT id FROM users WHERE email = ?'
            );

            $check->execute([$email]);

            if ($check->fetchColumn()) {

                $error = 'This email is already registered. Please log in.';

            } else {

                $pdo->beginTransaction();

                $hash = password_hash($password, PASSWORD_DEFAULT);

                $insert = $pdo->prepare(
                    "INSERT INTO users
                    (name, email, password, role, email_verified)
                    VALUES (?, ?, ?, 'student', 0)"
                );

                $insert->execute([$name, $email, $hash]);

                // Generate a six-digit OTP
                $otp = (string) random_int(100000, 999999);

                $otpHash = password_hash($otp, PASSWORD_DEFAULT);

                // Save hashed OTP in database
                $saveOtp = $pdo->prepare(
                    "INSERT INTO otp_verifications
                    (email, otp_hash, expires_at, attempts, created_at)
                    VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE), 0, NOW())"
                );

                $saveOtp->execute([$email, $otpHash]);

                $pdo->commit();

                // LOCAL DEVELOPMENT TESTING ONLY
                $_SESSION['verification_email'] = $email;
                $_SESSION['dev_otp'] = $otp;

                header('Location: verify-otp.php?dev=1');
                exit;
            }

        } catch (Throwable $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log('Registration error: ' . $e->getMessage());

            $error = 'Registration failed. Please check your database configuration.';
        }
    }
}

$pageTitle = 'Register | EduConnect';

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-7 col-lg-5">

            <div class="card shadow border-0">

                <div class="card-body p-4">

                    <h2 class="text-center mb-4">Create Account</h2>

                    <?php if ($error !== ''): ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                        </div>

                    <?php endif; ?>

                    <form method="post">

                        <div class="mb-3">
                            <label class="form-label" for="name">
                                Full Name
                            </label>

                            <input
                                class="form-control"
                                id="name"
                                name="name"
                                value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>"
                                required
                                maxlength="100"
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="email">
                                Email Address
                            </label>

                            <input
                                class="form-control"
                                type="email"
                                id="email"
                                name="email"
                                value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="password">
                                Password
                            </label>

                            <input
                                class="form-control"
                                type="password"
                                id="password"
                                name="password"
                                minlength="8"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="confirm_password">
                                Confirm Password
                            </label>

                            <input
                                class="form-control"
                                type="password"
                                id="confirm_password"
                                name="confirm_password"
                                minlength="8"
                                required
                            >
                        </div>

                        <button class="btn btn-primary w-100" type="submit">
                            Register
                        </button>

                    </form>

                    <p class="text-center mt-3 mb-0">
                        Already registered?
                        <a href="login.php">Login here</a>
                    </p>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
