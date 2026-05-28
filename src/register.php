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

            <div class="mb-4">
                <label style="color: #2e3d52; font-weight: 600; display: block; margin-bottom: 0.5rem;">Email</label>
                <input type="email" class="form-control darkColor login-input" placeholder="nowyminecraft2@gmail.com" style="background-color: #3b4257; color: #a2a2bd; border: none; padding: 0.75rem;">
            </div>

            <div class="mb-4">
                <label style="color: #2e3d52; font-weight: 600; display: block; margin-bottom: 0.5rem;">Confirm email</label>
                <input type="email" class="form-control darkColor login-input" placeholder="nowyminecraft2@gmail.com" style="background-color: #3b4257; color: #a2a2bd; border: none; padding: 0.75rem;">
            </div>

            <div class="mb-4">
                <label style="color: #2e3d52; font-weight: 600; display: block; margin-bottom: 0.5rem;">Password</label>
                <input type="password" class="form-control darkColor login-input" placeholder="eloelo320123" style="background-color: #3b4257; color: #a2a2bd; border: none; padding: 0.75rem;">
            </div>

            <div class="mb-4">
                <label style="color: #2e3d52; font-weight: 600; display: block; margin-bottom: 0.5rem;">Confirm password</label>
                <input type="password" class="form-control darkColor login-input" placeholder="eloelo320123" style="background-color: #3b4257; color: #a2a2bd; border: none; padding: 0.75rem;">
            </div>

            <div class="mb-5">
                <label style="color: #2e3d52; font-weight: 600; display: block; margin-bottom: 0.5rem;">Username</label>
                <input type="text" class="form-control darkColor login-input" placeholder="Krzysiek" style="background-color: #3b4257; color: #a2a2bd; border: none; padding: 0.75rem;">
            </div>

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
