<?php
session_start();

// 1. Niszczymy sesję po stronie serwera (PHP)
session_unset();
session_destroy();

// 2. Zamiast nagłówka Location, serwujemy czysty HTML/JS, który wyczyści localStorage i przekieruje użytkownika
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Wylogowywanie...</title>
</head>
<body>
    <script>
        try {
            // Czyszczenie koszyka i pamięci lokalnej
            localStorage.clear();
            console.log("LocalStorage wyczyszczone pomyślnie.");
        } catch (e) {
            console.error("Błąd podczas czyszczenia LocalStorage:", e);
        }

        // Bezpieczne przekierowanie na stronę główną za pomocą JavaScript
        window.location.href = 'main.php';
    </script>
    <noscript>
        <meta http-equiv="refresh" content="0;url=main.php">
    </noscript>
</body>
</html>