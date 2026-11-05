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

/* Change user role */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {

    $userId = (int)($_POST['user_id'] ?? 0);
    $role = $_POST['role'] ?? '';

    if ($userId <= 0 || !in_array($role, ['student', 'admin'], true)) {

        $error = 'Invalid user or role.';

    } elseif ($userId === (int)$_SESSION['user_id'] && $role !== 'admin') {

        $error = 'You cannot remove your own admin role.';

    } else {

        try {

            $stmt = $pdo->prepare("
                UPDATE users
                SET role = ?
                WHERE id = ?
            ");

            $stmt->execute([$role, $userId]);

            $message = 'User role updated successfully.';

        } catch (PDOException $e) {

            $error = 'Unable to update user role.';
        }
    }
}


/* Delete user */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {

    $userId = (int)($_POST['user_id'] ?? 0);

    if ($userId === (int)$_SESSION['user_id']) {

        $error = 'You cannot delete your own admin account.';

    } elseif ($userId <= 0) {

        $error = 'Invalid user.';

    } else {

        try {

            $stmt = $pdo->prepare(
                'DELETE FROM users WHERE id = ?'
            );

            $stmt->execute([$userId]);

            $message = 'User deleted successfully.';

        } catch (PDOException $e) {

            $error = 'Unable to delete this user. The user may have related enrollments.';
        }
    }
}


/* Fetch users */
$stmt = $pdo->query("
    SELECT
        id,
        name,
        email,
        role,
        email_verified,
        created_at
    FROM users
    ORDER BY id DESC
");

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Manage Users | EduConnect';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-5">

    <div class="mb-4">

        <h2>Manage Users</h2>

        <p class="text-muted">
            View and manage registered EduConnect users.
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
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Email Verified</th>
                    <th>Registered On</th>
                    <th>Actions</th>

                </tr>

            </thead>


            <tbody>

                <?php if (empty($users)): ?>

                    <tr>

                        <td colspan="7" class="text-center">
                            No users found.
                        </td>

                    </tr>

                <?php else: ?>

                    <?php foreach ($users as $user): ?>

                        <tr>

                            <td>
                                <?= (int)$user['id'] ?>
                            </td>


                            <td>
                                <?= htmlspecialchars($user['name']) ?>
                            </td>


                            <td>
                                <?= htmlspecialchars($user['email']) ?>
                            </td>


                            <td>

                                <?php if ($user['role'] === 'admin'): ?>

                                    <span class="badge bg-danger">
                                        Admin
                                    </span>

                                <?php else: ?>

                                    <span class="badge bg-primary">
                                        Student
                                    </span>

                                <?php endif; ?>

                            </td>


                            <td>

                                <?php if ((int)$user['email_verified'] === 1): ?>

                                    <span class="badge bg-success">
                                        Verified
                                    </span>

                                <?php else: ?>

                                    <span class="badge bg-warning text-dark">
                                        Not Verified
                                    </span>

                                <?php endif; ?>

                            </td>


                            <td>
                                <?= htmlspecialchars($user['created_at']) ?>
                            </td>


                            <td>

                                <?php if ((int)$user['id'] !== (int)$_SESSION['user_id']): ?>

                                    <form method="post" class="mb-2">

                                        <input
                                            type="hidden"
                                            name="user_id"
                                            value="<?= (int)$user['id'] ?>"
                                        >

                                        <select
                                            name="role"
                                            class="form-select form-select-sm mb-2">

                                            <option
                                                value="student"
                                                <?= $user['role'] === 'student' ? 'selected' : '' ?>>
                                                Student
                                            </option>

                                            <option
                                                value="admin"
                                                <?= $user['role'] === 'admin' ? 'selected' : '' ?>>
                                                Admin
                                            </option>

                                        </select>


                                        <button
                                            type="submit"
                                            name="update_role"
                                            class="btn btn-success btn-sm">

                                            Update Role

                                        </button>

                                    </form>


                                    <form
                                        method="post"
                                        onsubmit="return confirm('Are you sure you want to delete this user?');">

                                        <input
                                            type="hidden"
                                            name="user_id"
                                            value="<?= (int)$user['id'] ?>"
                                        >

                                        <button
                                            type="submit"
                                            name="delete_user"
                                            class="btn btn-danger btn-sm">

                                            Delete User

                                        </button>

                                    </form>

                                <?php else: ?>

                                    <span class="text-muted">
                                        Current Admin
                                    </span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>