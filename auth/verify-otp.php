<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$error = '';
$email = $_SESSION['verification_email'] ?? '';

if (!is_string($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: register.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp'] ?? '');

    if (!preg_match('/^[0-9]{6}$/', $otp)) {
        $error = 'Enter the six-digit verification code.';
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                "SELECT id, otp_hash, attempts
                 FROM otp_verifications
                 WHERE email = ? AND expires_at > NOW()
                 ORDER BY id DESC
                 LIMIT 1
                 FOR UPDATE"
            );
            $stmt->execute([$email]);
            $record = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$record) {
                $error = 'The code has expired or was not found. Please register again.';
            } elseif ((int)$record['attempts'] >= 5) {
                $error = 'Too many attempts. Please register again.';
            } elseif (!password_verify($otp, $record['otp_hash'])) {

                $update = $pdo->prepare(
                    'UPDATE otp_verifications
                     SET attempts = attempts + 1
                     WHERE id = ?'
                );
                $update->execute([$record['id']]);

                $error = 'Incorrect verification code. Please try again.';

            } else {

                $verify = $pdo->prepare(
                    'UPDATE users SET email_verified = 1 WHERE email = ?'
                );
                $verify->execute([$email]);

                $delete = $pdo->prepare(
                    'DELETE FROM otp_verifications WHERE email = ?'
                );
                $delete->execute([$email]);

                $pdo->commit();

                unset($_SESSION['verification_email']);
                unset($_SESSION['dev_otp']);

                header('Location: login.php?verified=1');
                exit;
            }

            if ($pdo->inTransaction()) {
                $pdo->commit();
            }

        } catch (PDOException $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log('OTP verification error: ' . $e->getMessage());
            $error = 'Verification failed. Please try again.';
        }
    }
}

$pageTitle = 'Verify Email | EduConnect';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">

            <div class="card shadow border-0">
                <div class="card-body p-4">

                    <h2 class="text-center mb-3">
                        Verify Your Email
                    </h2>

                    <?php if (
                        isset($_GET['dev']) &&
                        $_GET['dev'] === '1' &&
                        isset($_SESSION['dev_otp'])
                    ): ?>

                        <div class="alert alert-warning text-center">
                            <strong>LOCAL TESTING OTP</strong>
                            <br>

                            Your OTP is:

                            <h3 class="mt-2 mb-1">
                                <?= htmlspecialchars($_SESSION['dev_otp']) ?>
                            </h3>

                            <small>
                                This OTP is for local project testing only.
                            </small>
                        </div>

                    <?php endif; ?>

                    <p class="text-center text-muted">
                        Enter the six-digit code for
                        <?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>.
                    </p>

                    <?php if ($error !== ''): ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                        </div>

                    <?php endif; ?>

                    <form method="post">

                        <div class="mb-3">

                            <label for="otp" class="form-label">
                                Verification Code
                            </label>

                            <input
                                type="text"
                                id="otp"
                                name="otp"
                                class="form-control text-center"
                                inputmode="numeric"
                                pattern="[0-9]{6}"
                                maxlength="6"
                                minlength="6"
                                autocomplete="one-time-code"
                                placeholder="Enter 6-digit code"
                                required
                            >

                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary w-100">
                            Verify Email
                        </button>

                    </form>

                    <p class="text-center mt-3 mb-0">
                        <a href="login.php">Back to Login</a>
                    </p>

                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>