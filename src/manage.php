<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jeśli użytkownik nie jest adminem, przekieruj go na stronę główną
if (!isset($_SESSION['role_id']) || (int)$_SESSION['role_id'] !== 1) {
    header("Location: main.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zegowska Szama - Manage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styl.css">
</head>
<body class="vh-100 d-flex flex-column">
<?php require_once __DIR__ . '/header.php'; ?>

    <div class="flex-fill container-fluid px-4 py-3 overflow-auto">
        <div class="d-flex gap-3 mb-4 border-bottom pb-3" style="border-color: #4a5568 !important;">
            <button class="tab-btn fw-bold text-muted" data-tab="orders" style="background: none; border: none; cursor: pointer; font-size: 1rem;">Orders</button>
            <button class="tab-btn fw-bold text-muted" data-tab="products" style="background: none; border: none; cursor: pointer; font-size: 1rem;">Products</button>
            <button class="tab-btn fw-bold text-muted" data-tab="users" style="background: none; border: none; cursor: pointer; font-size: 1rem;">Users</button>
            <button class="tab-btn fw-bold text-muted" data-tab="mail" style="background: none; border: none; cursor: pointer; font-size: 1rem;">Mail</button>
        </div>

        <!-- ORDERS TAB -->
        <div id="orders" class="tab-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="display-6 fw-bold mb-0" style="color: #2e3d52;">orders</h3>
                <button class="btn fw-semibold px-4 py-2 rounded-2" style="background-color: #3b4257; color: #a2a2bd; border: none;">Complete</button>
            </div>

            <div style="overflow-x: auto;">
                <table class="table table-borderless">
                    <thead style="color: #a0a0b0;">
                        <tr>
                            <th>User</th>
                            <th>Number</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="color: #2e3d52;">
                            <td>Krzysiek</td>
                            <td style="color: #5a8e7a; font-weight: 600;">01</td>
                            <td>
                                <button class="btn btn-sm rounded-2 me-2" style="background-color: #5a8e7a; color: white; border: none; width: 30px; height: 30px;"><span style="font-size: 0.8rem;">✓</span></button>
                                <button class="btn btn-sm rounded-2" style="background-color: #3b4257; color: white; border: none; width: 30px; height: 30px;"><span style="font-size: 0.8rem;">×</span></button>
                            </td>
                        </tr>
                        <tr style="color: #2e3d52;">
                            <td>Jan Paweł II</td>
                            <td style="color: #5a8e7a; font-weight: 600;">02</td>
                            <td>
                                <button class="btn btn-sm rounded-2 me-2" style="background-color: #5a8e7a; color: white; border: none; width: 30px; height: 30px;"><span style="font-size: 0.8rem;">✓</span></button>
                                <button class="btn btn-sm rounded-2" style="background-color: #3b4257; color: white; border: none; width: 30px; height: 30px;"><span style="font-size: 0.8rem;">×</span></button>
                            </td>
                        </tr>
                        <tr style="color: #2e3d52;">
                            <td>Obama</td>
                            <td style="color: #5a8e7a; font-weight: 600;">67</td>
                            <td>
                                <button class="btn btn-sm rounded-2 me-2" style="background-color: #5a8e7a; color: white; border: none; width: 30px; height: 30px;"><span style="font-size: 0.8rem;">✓</span></button>
                                <button class="btn btn-sm rounded-2" style="background-color: #3b4257; color: white; border: none; width: 30px; height: 30px;"><span style="font-size: 0.8rem;">×</span></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-5" style="color: #2e3d52;">
                <h5>Authorize <span style="color: #5a8e7a;">collected orders</span></h5>
            </div>
        </div>

        <!-- PRODUCTS TAB -->
        <div id="products" class="tab-content d-none">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="display-6 fw-bold mb-0" style="color: #2e3d52;">products</h3>
                <button class="btn fw-semibold px-4 py-2 rounded-2" style="background-color: #3b4257; color: #a2a2bd; border: none;">Create</button>
            </div>

            <div class="row mb-5">
                <div class="col-md-6">
                    <div style="width: 120px; height: 120px; background-color: #3b4257; border-radius: 8px; margin-bottom: 1rem;"></div>
                    <div class="mb-3">
                        <label style="color: #2e3d52; font-weight: 600;">Name</label>
                        <input type="text" class="form-control darkColor" placeholder="text..." style="background-color: #3b4257; color: #a2a2bd; border: none;">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label style="color: #2e3d52; font-weight: 600;">Category</label>
                            <input type="text" class="form-control darkColor" placeholder="text..." style="background-color: #3b4257; color: #a2a2bd; border: none;">
                        </div>
                        <div class="col-6 mb-3">
                            <label style="color: #2e3d52; font-weight: 600;">Stock</label>
                            <input type="text" class="form-control darkColor" placeholder="0" style="background-color: #3b4257; color: #a2a2bd; border: none;">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label style="color: #2e3d52; font-weight: 600;">Price (cents)</label>
                            <input type="text" class="form-control darkColor" placeholder="0" style="background-color: #3b4257; color: #a2a2bd; border: none;">
                        </div>
                        <div class="col-6 mb-3">
                            <label style="color: #2e3d52; font-weight: 600;">discount %</label>
                            <input type="text" class="form-control darkColor" placeholder="0-100%" style="background-color: #3b4257; color: #a2a2bd; border: none;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- USERS TAB -->
        <div id="users" class="tab-content d-none">
            <h3 class="display-6 fw-bold mb-4" style="color: #2e3d52;">Users</h3>

            <div style="overflow-x: auto;">
                <table class="table table-borderless">
                    <thead style="color: #a0a0b0;">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Change</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="color: #2e3d52;">
                            <td>Krzysiek</td>
                            <td style="color: #8b8b9e; font-size: 0.9rem;">nowymineraft2@gmail.com</td>
                            <td style="color: #5a8e7a;">Admin</td>
                            <td><button class="btn btn-sm fw-semibold px-3 py-1 rounded-2" style="background-color: #5a8e7a; color: white; border: none;">Select</button></td>
                        </tr>
                        <tr style="color: #2e3d52;">
                            <td>User 1</td>
                            <td style="color: #8b8b9e; font-size: 0.9rem;">example@gmail.com</td>
                            <td style="color: #a0a0b0;">User</td>
                            <td><button class="btn btn-sm fw-semibold px-3 py-1 rounded-2" style="background-color: #5a8e7a; color: white; border: none;">Select</button></td>
                        </tr>
                        <tr style="color: #2e3d52;">
                            <td>User 2</td>
                            <td style="color: #8b8b9e; font-size: 0.9rem;">szefitlamaga@gmail.com</td>
                            <td style="color: #a0a0b0;">Worker</td>
                            <td><button class="btn btn-sm fw-semibold px-3 py-1 rounded-2" style="background-color: #5a8e7a; color: white; border: none;">Select</button></td>
                        </tr>
                        <tr style="color: #2e3d52;">
                            <td>User 3</td>
                            <td style="color: #8b8b9e; font-size: 0.9rem;">silent_heart@gmail.com</td>
                            <td style="color: #a0a0b0;">User</td>
                            <td><button class="btn btn-sm fw-semibold px-3 py-1 rounded-2" style="background-color: #5a8e7a; color: white; border: none;">Select</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                <div class="d-flex gap-3 mb-4" style="border-bottom: 1px solid #4a5568; padding-bottom: 1rem;">
                    <input type="text" class="form-control darkColor" placeholder="Search..." style="background-color: #3b4257; color: #a2a2bd; border: none; max-width: 300px;">
                    <button class="btn fw-semibold px-4 py-2 rounded-2" style="background-color: #3b4257; color: #a2a2bd; border: none;">Name</button>
                </div>
            </div>
        </div>

        <!-- MAIL TAB -->
        <div id="mail" class="tab-content d-none">
            <h3 class="display-6 fw-bold mb-4" style="color: #2e3d52;">Mail</h3>

            <div class="mb-4">
                <label style="color: #2e3d52; font-weight: 600; display: block; margin-bottom: 0.5rem;">Title</label>
                <input type="text" class="form-control darkColor" placeholder="uwaga !!!" style="background-color: #3b4257; color: #a2a2bd; border: none; font-size: 1rem;">
            </div>

            <div class="mb-4">
                <label style="color: #2e3d52; font-weight: 600; display: block; margin-bottom: 0.5rem;">Content</label>
                <textarea class="form-control darkColor" placeholder="UWAGA!!! michałek 16 lat MA OB!LED w oczach i JEZELI GO ZOBACZYCIE to OD RAZU UCIEKAJCIE!!! Mowie na powaznie, MA CZERWONE OCZY I NÓŻ!!!" style="background-color: #3b4257; color: #a2a2bd; border: none; font-size: 1rem; min-height: 200px;"></textarea>
            </div>

            <button class="btn fw-semibold px-6 py-2 rounded-2" style="background-color: #5a8e7a; color: white; border: none;">Push</button>
        </div>
    </div>

    <div class="p-3 footer text-lowercase fs-5">School's website</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                
                // Hide all tabs
                document.querySelectorAll('.tab-content').forEach(tab => {
                    tab.classList.add('d-none');
                });
                
                // Show selected tab
                document.getElementById(tabName).classList.remove('d-none');
                
                // Update button styles
                document.querySelectorAll('.tab-btn').forEach(b => {
                    b.style.color = '#a0a0b0';
                });
                this.style.color = '#2e3d52';
                this.style.fontWeight = '700';
            });
        });
        
        // Set first tab as active
        document.querySelector('.tab-btn').style.color = '#2e3d52';
        document.querySelector('.tab-btn').style.fontWeight = '700';
    </script>
</body>
</html>
