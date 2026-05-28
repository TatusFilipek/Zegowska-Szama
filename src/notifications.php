<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zegowska Szama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styl.css">
</head>
<body class="vh-100 d-flex flex-column">
<?php
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/db.php';

$announcements = [];
try {
    $stmt = $conn->query('SELECT id, title, content, created_at FROM announcements ORDER BY created_at DESC');
    $announcements = $stmt->fetchAll();
} catch (Exception $e) {
    $announcements = [];
}
?>

    <div class="flex-fill container-fluid px-4 py-3 overflow-auto">
        
        <div class="text-start my-4">
            <h3 class="display-6 border-bottom pb-2 fw-bold" style="color: #4a5568; border-color: #4a5568 !important; font-weight: 300;">Mailbox</h3>
        </div>

        <?php if (!empty($announcements)): ?>
            <?php foreach ($announcements as $announcement): ?>
        <div class="mb-4">
            <div class="d-flex justify-content-between mb-2">
                <span style="color: #8b8b9e; font-size: 1.20rem; font-weight: 500;"><?= htmlspecialchars($announcement['title']) ?></span>
                <span style="color: #a0a0b0;"><?= htmlspecialchars(date('d.m.Y', strtotime($announcement['created_at']))) ?></span>
            </div>
            <p style="color: #4a5568; line-height: 1.6; font-size: 1.25rem; font-weight: 500;"><?= htmlspecialchars($announcement['content']) ?></p>
        </div>
            <?php endforeach; ?>
        <?php else: ?>
        <div class="alert alert-info" role="alert">
            Brak ogłoszeń.
        </div>
        <?php endif; ?>
    </div>
    <div class="p-3 footer text-lowercase fs-5">School's website</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="skrypt.js"></script>
</body>
</html>