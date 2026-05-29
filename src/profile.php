<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zegowska Szama - Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styl.css">
</head>
<body class="vh-100 d-flex flex-column">
<?php
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare('
    SELECT u.id, u.name, u.email, u.created_at, r.name as role_name
    FROM users u
    LEFT JOIN roles r ON u.role_id = r.id
    WHERE u.id = ? LIMIT 1
');
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: login.php');
    exit;
}

$userName = htmlspecialchars($user['name']);
$userEmail = htmlspecialchars($user['email']);
$userRole = htmlspecialchars($user['role_name'] ?? 'N/A');
$userJoined = htmlspecialchars(date('d-m-Y', strtotime($user['created_at'])));
?>
    <div class="flex-fill container-fluid px-4 py-3 overflow-auto d-inline d-md-none">
        
        <div class="text-center my-4">
                <h1 class="display-4 mb-0" style="color: #8da382; font-weight: 300; font-family: sans-serif;">Witaj</h1>
                <h2 class="display-5 fw-bold offset-2" style="color: #2e3d52;"><?= $userName ?></h2>
        </div>

        <div class="mb-5">
            <h3 class="display-6 border-bottom pb-2" style="color: #4a5568; border-color: #4a5568 !important; font-weight: 300;">Informacje</h3>
            
            <div class="mt-3">
                <div class="row py-2 fs-5 align-items-center">
                    <div class="col-2 text-muted">Email:</div>
                    <div class="col-10 fw-bold" style="color: #2e3d52; word-break: break-all;"><?= $userEmail ?></div>
                </div>
                <div class="row py-2 fs-5 align-items-center">
                    <div class="col-2 text-muted">Rola:</div>
                    <div class="col-10 fw-bold" style="color: #2e3d52;"><?= $userRole ?></div>
                </div>
                <div class="row py-2 fs-5 align-items-center">
                    <div class="col-2 text-muted">Dołączono:</div>
                    <div class="col-10 fw-bold" style="color: #2e3d52;"><?= $userJoined ?></div>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <h3 class="display-6 border-bottom pb-2" style="color: #4a5568; border-color: #4a5568 !important; font-weight: 300;">Ustawienia</h3>
            
            <div class="row mt-3 py-2 fs-5 align-items-center">
                <div class="col-2 text-muted">Motyw:</div>
                <div class="col-10">
                    <button class="btn text-capitalize px-4 py-2 fw-semibold rounded-2" 
                            style="background-color: #3b4257; color: #a2a2bd; border: none; min-width: 140px;">
                        Zwykły
                    </button>
                </div>
            </div>

            <div class="row mt-4 py-2 fs-5 align-items-center">
                <div class="col-12">
                    <a href="logout.php" class="btn btn-danger w-100 py-2 fw-semibold rounded-2">
                        Wyloguj
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="flex-fill container-fluid overflow-auto d-none d-md-block m-0 p-0">
        <div class="promo-banner overflow-hidden position-relative">
            <div class="text-center mx-auto" style="width: 100%;">
                <span class="bubble bubble-dark" style="left: -50px; top: -30px; width: 160px; opacity: 1;"></span>
                <span class="bubble bubble-purple" style="left: 22%; top: -20px; width: 60px; opacity: 0.7;"></span>
                <span class="bubble bubble-purple" style="right: 18%; bottom: -30px; width: 110px; z-index: 3; opacity: 0.8;"></span>
                <span class="bubble bubble-dark" style="right: -30px; top: -20px; width: 90px; opacity: 0.6;"></span>
                <span class="bubble bubble-dark" style="right: 40%; top: 10px; width: 70px; opacity: 0.8;"></span>

                <div class="d-flex justify-content-center gap-5">
                    <div class="text-end my-5 mx-4">
                        <h1 class="display-4 mb-0" style="color: #8da382; font-weight: 300; font-family: sans-serif;">Witaj</h1>
                        <h2 class="display-5 fw-bold offset-4" style="color: #2e3d52;"><?= $userName ?></h2>
                    </div>

                    <h1 class="display-1 fw-bold promo-text py-4 m-0 mx-3">
                        O <span class="accent-text">tobie</span> ...
                    </h1>
                </div>
            </div>
        </div>

        <div class="p-4 d-flex justify-content-between">
            <div class="mx-4 w-50">
                <h3 class="display-6 border-bottom pb-2" style="color: #4a5568; border-color: #4a5568 !important; font-weight: 300;">Informacje</h3>
                
                <div class="mt-3">
                    <div class="row py-2 fs-5 align-items-center">
                        <div class="col-2 text-muted">Email:</div>
                        <div class="col-10 fw-bold" style="color: #2e3d52; word-break: break-all;"><?= $userEmail ?></div>
                    </div>
                    <div class="row py-2 fs-5 align-items-center">
                        <div class="col-2 text-muted">Rola:</div>
                        <div class="col-10 fw-bold" style="color: #2e3d52;"><?= $userRole ?></div>
                    </div>
                    <div class="row py-2 fs-5 align-items-center">
                        <div class="col-2 text-muted">Dołączono:</div>
                        <div class="col-10 fw-bold" style="color: #2e3d52;"><?= $userJoined ?></div>
                    </div>
                </div>
            </div>

            <div class="mx-4 w-50">
                <h3 class="display-6 border-bottom pb-2" style="color: #4a5568; border-color: #4a5568 !important; font-weight: 300;">Ustawienia</h3>
                
                <div class="row mt-3 py-2 fs-5 align-items-center">
                    <div class="col-2 text-muted">Motyw:</div>
                    <div class="col-10">
                        <button class="btn text-capitalize px-4 py-2 fw-semibold rounded-2" 
                                style="background-color: #3b4257; color: #a2a2bd; border: none; min-width: 140px;">
                            Zwykły
                        </button>
                    </div>
                </div>

                <div class="row mt-4 py-2 fs-5 align-items-center">
                    <div class="col-12">
                        <a href="logout.php" class="btn btn-danger w-100 py-2 fw-semibold rounded-2">
                            Wyloguj
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="p-3 footer text-lowercase fs-5">Szkolna strona</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="skrypt.js"></script>
</body>
</html>