<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$loggedIn = !empty($_SESSION['user_id']);

// Sprawdzamy, czy użytkownik jest zalogowany i czy jego role_id odpowiada administratorowi (np. 1)
$isAdmin = $loggedIn && isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 1;
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<div class="header text-lowercase fs-4 d-flex align-items-center gap-4 dropdown p-2 ps-3">
    <div class="my-auto">
        <a href="main.php"><img src="./photos/zeg-icon.png" style="width: 55px;"></a>
    </div>
    <div class="my-auto flex-fill fs-3">
        <a href="main.php">Szama</a>
    </div>

    <div class="d-none d-md-flex gap-5 my-auto fs-3 me-3" style="overflow: hidden;">
<?php if ($loggedIn): ?>
        <a class="text-capitalize" href="profile.php">Profil</a>
        <a class="text-capitalize" href="checkout.php">Koszyk</a>
        <a class="text-capitalize" href="notifications.php">Powiadomienia</a>
        <a class="text-capitalize" href="offers.php">Oferty</a>
        <?php if ($isAdmin): ?>
            <a class="text-capitalize" href="manage.php">Zarządzaj</a>
        <?php endif; ?>
<?php else: ?>
        <a class="text-capitalize" href="login.php">Zaloguj się</a>
        <a class="text-capitalize" href="register.php">Rejestracja</a>
<?php endif; ?>
    </div>
    
    <div class="my-auto d-md-none" data-bs-toggle="dropdown" aria-expanded="false">
        <img class="btn bi bi-list m-0 p-0" src="./photos/burger.png" style="width: 55px;">
    </div>
    <ul class="row dropdown-menu custom-dropdown-menu w-100 mt-1 p-0 d-md-none">
<?php if ($loggedIn): ?>
        <li><a class="dropdown-item custom-item" href="profile.php">Profil</a></li>
        <li><a class="dropdown-item custom-item" href="checkout.php">Koszyk</a></li>
        <li><a class="dropdown-item custom-item" href="notifications.php">Powiadomienia</a></li>
        <li><a class="dropdown-item custom-item" href="offers.php">Oferty</a></li>
        <?php if ($isAdmin): ?>
            <li><a class="dropdown-item custom-item" href="manage.php">Zarządzaj</a></li>
        <?php endif; ?>
<?php else: ?>
        <li><a class="dropdown-item custom-item" href="login.php">Zaloguj się</a></li>
        <li><a class="dropdown-item custom-item" href="register.php">Rejestracja</a></li>
<?php endif; ?>
    </ul>
</div>

<script>
    window.isUserLoggedIn = <?php echo $loggedIn ? 'true' : 'false' ?>;
</script>