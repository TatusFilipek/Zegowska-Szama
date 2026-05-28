<?php
require_once __DIR__ . '/db.php';
session_start();
$loggedIn = !empty($_SESSION['user_id']);

if ($loggedIn) {
    header('Location: main.php');
    exit;
}

$error = '';
$name = '';
$email = '';
$email_confirm = '';
$password = '';
$password_confirm = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $email_confirm = trim($_POST['email_confirm'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $password_confirm = trim($_POST['password_confirm'] ?? '');

    if ($name === '' || $email === '' || $email_confirm === '' || $password === '' || $password_confirm === '') {
        $error = 'Wszystkie pola są wymagane.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Podaj poprawny adres e-mail.';
    } elseif ($email !== $email_confirm) {
        $error = 'Adresy e-mail nie są zgodne.';
    } elseif ($password !== $password_confirm) {
        $error = 'Hasła nie są zgodne.';
    } elseif (strlen($password) < 6) {
        $error = 'Hasło musi mieć przynajmniej 6 znaków.';
    } else {
        $stmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Użytkownik z tym adresem e-mail już istnieje.';
        } else {
            $roleId = 2;
            $roleStmt = $conn->prepare('SELECT id FROM roles WHERE id = ? LIMIT 1');
            $roleStmt->execute([$roleId]);
            if (!$roleStmt->fetch()) {
                $insertRole = $conn->prepare('INSERT INTO roles (id, name) VALUES (?, ?)');
                $insertRole->execute([$roleId, 'customer']);
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare('INSERT INTO users (name, email, password, role_id) VALUES (?, ?, ?, ?)');
            $insert->execute([$name, $email, $hashedPassword, $roleId]);

            $_SESSION['user_id'] = $conn->lastInsertId();
            $_SESSION['user_role'] = 'customer';

            header('Location: main.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zegowska Szama - Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styl.css">
    <style>
        .login-input::placeholder {
            color: #a2a2bd;
            opacity: 1;
        }
    </style>
</head>
<body class="vh-100 d-flex flex-column">
<?php require_once __DIR__ . '/header.php'; ?>

    <div class="flex-fill container-fluid px-4 py-3 overflow-auto d-flex flex-column align-items-center">
        <div style="max-width: 500px; width: 100%;">
            <h3 class="display-6 fw-bold mb-4 border-bottom pb-3" style="color: #2e3d52; border-color: #4a5568 !important;">Register</h3>

            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" action="register.php">
                <div class="mb-4">
                    <label style="color: #2e3d52; font-weight: 600; display: block; margin-bottom: 0.5rem;">Email</label>
                    <input name="email" type="email" class="form-control darkColor login-input" value="<?= htmlspecialchars($email) ?>" placeholder="nowyminecraft2@gmail.com" style="background-color: #3b4257; color: #a2a2bd; border: none; padding: 0.75rem;">
                </div>

                <div class="mb-4">
                    <label style="color: #2e3d52; font-weight: 600; display: block; margin-bottom: 0.5rem;">Confirm email</label>
                    <input name="email_confirm" type="email" class="form-control darkColor login-input" value="<?= htmlspecialchars($email_confirm) ?>" placeholder="nowyminecraft2@gmail.com" style="background-color: #3b4257; color: #a2a2bd; border: none; padding: 0.75rem;">
                </div>

                <div class="mb-4">
                    <label style="color: #2e3d52; font-weight: 600; display: block; margin-bottom: 0.5rem;">Password</label>
                    <input name="password" type="password" class="form-control darkColor login-input" placeholder="eloelo320123" style="background-color: #3b4257; color: #a2a2bd; border: none; padding: 0.75rem;">
                </div>

                <div class="mb-4">
                    <label style="color: #2e3d52; font-weight: 600; display: block; margin-bottom: 0.5rem;">Confirm password</label>
                    <input name="password_confirm" type="password" class="form-control darkColor login-input" placeholder="eloelo320123" style="background-color: #3b4257; color: #a2a2bd; border: none; padding: 0.75rem;">
                </div>

                <div class="mb-5">
                    <label style="color: #2e3d52; font-weight: 600; display: block; margin-bottom: 0.5rem;">Username</label>
                    <input name="name" type="text" class="form-control darkColor login-input" value="<?= htmlspecialchars($name) ?>" placeholder="Krzysiek" style="background-color: #3b4257; color: #a2a2bd; border: none; padding: 0.75rem;">
                </div>

                <div class="d-grid mb-4">
                    <button type="submit" class="btn btn-primary py-2" style="background-color: #3b4257; border: none;">Register</button>
                </div>
            </form>

            <div style="text-align: center;">
                <span style="color: #2e3d52; font-size: 1rem;">Already have an <span style="color: #a0a0b0;">account?</span></span>
                <br>
                <a href="login.php" style="color: #2e3d52; font-weight: 600; text-decoration: none;">Login <span style="font-weight: 400;">instead!</span></a>
            </div>
        </div>
    </div>

    <div class="p-3 footer text-lowercase fs-5">School's website</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
