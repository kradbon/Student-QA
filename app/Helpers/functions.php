<?php
function db(): PDO { return \App\Database::connection(); }
function e(?string $value): string { return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function redirect(string $path): never { header('Location: ' . $path); exit; }
function csrf_token(): string {
    if (empty($_SESSION['_csrf'])) $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['_csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="_csrf" value="'.e(csrf_token()).'">'; }
function verify_csrf(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
        !hash_equals($_SESSION['_csrf'] ?? '', $_POST['_csrf'] ?? '')) {
        http_response_code(419); exit('Invalid CSRF token');
    }
}
function current_user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(): array {
    $user = current_user();
    if (!$user) redirect('?page=login');
    return $user;
}
function require_role(array $roles): array {
    $user = require_login();
    if (!in_array($user['role'], $roles, true)) { http_response_code(403); exit('Forbidden'); }
    return $user;
}
function flash(string $message, string $type='success'): void { $_SESSION['flash'] = [$message,$type]; }
function t(string $key): string {
    $lang = $_SESSION['lang'] ?? 'ru';
    $dict = [
      'ru'=>['home'=>'Вопросы','ask'=>'Задать вопрос','login'=>'Войти','register'=>'Регистрация','search'=>'Поиск вопросов','answers'=>'Ответы','submit'=>'Отправить','logout'=>'Выйти','welcome'=>'Сообщество вопросов и ответов студентов'],
      'tj'=>['home'=>'Саволҳо','ask'=>'Савол додан','login'=>'Воридшавӣ','register'=>'Бақайдгирӣ','search'=>'Ҷустуҷӯи саволҳо','answers'=>'Ҷавобҳо','submit'=>'Ирсол','logout'=>'Баромадан','welcome'=>'Ҷомеаи саволу ҷавоби донишҷӯён'],
      'en'=>['home'=>'Questions','ask'=>'Ask question','login'=>'Login','register'=>'Register','search'=>'Search questions','answers'=>'Answers','submit'=>'Submit','logout'=>'Logout','welcome'=>'Student question and answer community']
    ];
    return $dict[$lang][$key] ?? $dict['en'][$key] ?? $key;
}
function layout(string $title, string $content): void {
    $user = current_user(); $flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>'.e($title).' · Student Q&A</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link href="assets/css/app.css" rel="stylesheet"></head><body>';
    echo '<nav class="navbar navbar-expand-lg navbar-dark bg-navy"><div class="container"><a class="navbar-brand fw-bold" href="?">Student<span class="brand-accent">Q&A</span></a><div class="d-flex gap-2 align-items-center ms-auto">';
    echo '<form method="post" action="?page=language">'.csrf_field().'<select class="form-select form-select-sm" name="lang" onchange="this.form.submit()"><option value="ru">Русский</option><option value="tj">Тоҷикӣ</option><option value="en">English</option></select></form>';
    echo '<a class="nav-link text-white" href="?">'.e(t('home')).'</a>';
    if ($user) { echo '<a class="btn btn-sm btn-accent" href="?page=ask">'.e(t('ask')).'</a><span class="text-white small">'.e($user['name']).'</span><a class="nav-link text-white" href="?page=logout">'.e(t('logout')).'</a>'; }
    else { echo '<a class="nav-link text-white" href="?page=login">'.e(t('login')).'</a><a class="btn btn-sm btn-outline-light" href="?page=register">'.e(t('register')).'</a>'; }
    echo '</div></div></nav><main class="container py-4">';
    if ($flash) echo '<div class="alert alert-'.e($flash[1]).'">'.e($flash[0]).'</div>';
    echo $content;
    echo '</main><footer class="container py-4 text-secondary small border-top">Student Q&A · Variant 24 · PHP 8.2 + MySQL</footer><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script><script src="assets/js/app.js"></script></body></html>';
}
