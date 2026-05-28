<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$loggedIn = !empty($_SESSION['user_id']);
?>
<div class="header text-lowercase fs-4 d-flex align-items-center gap-4 dropdown p-2 ps-3">
    <div class="my-auto">
        <a href="main.php"><img src="../design/photos/zeg-icon.png" style="width: 55px;"></a>
    </div>
    <div class="my-auto flex-fill fs-3">
        <a href="main.php">Szama</a>
    </div>

    <div class="d-none d-md-flex gap-5 my-auto fs-3 me-3">
<?php if ($loggedIn): ?>
        <a class="text-capitalize" href="profile.php">Profile</a>
        <a class="text-capitalize" href="checkout.php">Checkout</a>
        <a class="text-capitalize" href="notifications.php">Notifications</a>
        <a class="text-capitalize" href="offers.php">Offers</a>
        <a class="text-capitalize" href="manage.php">Manage</a>
<?php else: ?>
        <a class="text-capitalize" href="login.php">Login</a>
        <a class="text-capitalize" href="register.php">Register</a>
<?php endif; ?>
    </div>
    
    <div class="my-auto d-md-none" data-bs-toggle="dropdown" aria-expanded="false">
        <img class="btn bi bi-list m-0 p-0" src="../design/photos/burger.png" style="width: 55px;">
    </div>
    <ul class="row dropdown-menu custom-dropdown-menu w-100 mt-1 p-0 d-md-none">
<?php if ($loggedIn): ?>
        <li><a class="dropdown-item custom-item" href="profile.php">Profile</a></li>
        <li><a class="dropdown-item custom-item" href="checkout.php">Checkout</a></li>
        <li><a class="dropdown-item custom-item" href="notifications.php">Notifications</a></li>
        <li><a class="dropdown-item custom-item" href="offers.php">Offers</a></li>
        <li><a class="dropdown-item custom-item" href="manage.php">Manage</a></li>
<?php else: ?>
        <li><a class="dropdown-item custom-item" href="login.php">Login</a></li>
        <li><a class="dropdown-item custom-item" href="register.php">Register</a></li>
<?php endif; ?>
    </ul>
</div>
