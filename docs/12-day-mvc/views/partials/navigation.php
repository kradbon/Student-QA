<?php
/** @var string $activePage */
$navItems = [
    'home' => ['label' => 'Главная', 'url' => '/'],
    'questions' => ['label' => 'Вопросы', 'url' => '/questions'],
    'reference' => ['label' => 'Справочник', 'url' => '/reference'],
];
?>
<nav class="navbar"><div class="container nav-inner"><a class="brand" href="/">Student<span>Q&amp;A</span></a><div class="nav-links" aria-label="Основная навигация">
<?php foreach ($navItems as $key => $item): ?>
<a href="<?= htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8') ?>" class="nav-link<?= $activePage === $key ? ' active' : '' ?>"<?= $activePage === $key ? ' aria-current="page"' : '' ?>><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></a>
<?php endforeach; ?>
</div><a class="nav-cta" href="/questions">Открыть вопросы <span aria-hidden="true">↗</span></a><button class="menu-toggle" type="button" aria-label="Открыть меню" aria-expanded="false">☰</button></div></nav>
