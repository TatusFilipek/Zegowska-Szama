<?php
session_start();
$loggedIn = !empty($_SESSION['user_id']);
require_once __DIR__ . '/db.php';

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Wypełnij adres e-mail i hasło.';
    } else {
        $stmt = $conn->prepare('SELECT id, password, role_id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && (password_verify($password, $user['password']) || $user['password'] === $password)) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role_id'] = $user['role_id'];
            header('Location: main.php');
            exit;
        }

        $error = 'Nieprawidłowy e-mail lub hasło.';
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zegowska Szama - Logowanie</title>
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
            <h3 class="display-6 fw-bold mb-4 border-bottom pb-3" style="color: #2e3d52; border-color: #4a5568 !important;">Zaloguj się</h3>

            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" action="login.php">
                <div class="mb-4">
                    <label style="color: #2e3d52; font-weight: 600; display: block; margin-bottom: 0.5rem;">Email</label>
                    <input name="email" type="email" value="<?= htmlspecialchars($email) ?>" class="form-control login-input" placeholder="nowyminecraft2@gmail.com" style="background-color: #3b4257; color: #a2a2bd; border: none; padding: 0.75rem;">
                </div>

                <div class="mb-5">
                    <label style="color: #2e3d52; font-weight: 600; display: block; margin-bottom: 0.5rem;">Hasło</label>
                    <input name="password" type="password" class="form-control login-input" placeholder="silneHaslo" style="background-color: #3b4257; color: #a2a2bd; border: none; padding: 0.75rem;">
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-4">Zaloguj się</button>
            </form>

            <div style="text-align: center;">
                <span style="color: #2e3d52; font-size: 1rem;">Nie masz konta?</span>
                <br>
                <a href="register.php" style="color: #2e3d52; font-weight: 600; text-decoration: none;">Zarejestruj <span style="font-weight: 400;">się!</span></a>
            </div>
        </div>
    </div>

    <div class="p-3 footer text-lowercase fs-5">Szkolna strona</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
