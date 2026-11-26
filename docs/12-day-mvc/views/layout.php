<?php
$pageTitle = isset($pageTitle) ? (string) $pageTitle : 'Student Q&A';
$activePage = isset($activePage) ? (string) $activePage : '';
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Student Q&A — платформа вопросов и ответов студентов">
    <meta name="theme-color" content="#101827">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<?php require __DIR__ . '/partials/navigation.php'; ?>
<main id="main"><?= $content ?></main>
<?php require __DIR__ . '/partials/footer.php'; ?>
<script src="/assets/js/app.js" defer></script>
</body>
</html>
