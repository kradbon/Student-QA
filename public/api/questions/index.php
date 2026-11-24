<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$questions = [
    [
        'id' => 1, 'title' => 'Почему асинхронная функция возвращает Promise?',
        'excerpt' => 'Разбираюсь с async/await в JavaScript. Почему результат функции нужно получать через await?',
        'tags' => ['javascript', 'async/await'], 'category' => 'Программирование',
        'votes' => 12, 'answer_count' => 3, 'views' => 246,
        'author' => 'Илья Смирнов', 'created_label' => '12 мин назад',
    ],
    [
        'id' => 2, 'title' => 'Как оптимизировать SQL-запрос с несколькими JOIN?',
        'excerpt' => 'Запрос медленно работает на таблице с большим количеством записей. Как найти узкое место?',
        'tags' => ['sql', 'база данных'], 'category' => 'Базы данных',
        'votes' => 8, 'answer_count' => 2, 'views' => 118,
        'author' => 'Мадина Каримова', 'created_label' => '34 мин назад',
    ],
    [
        'id' => 3, 'title' => 'В чём разница между Flexbox и CSS Grid?',
        'excerpt' => 'Для адаптивной сетки карточек лучше использовать Grid или Flexbox?',
        'tags' => ['css', 'вёрстка'], 'category' => 'Веб-разработка',
        'votes' => 5, 'answer_count' => 0, 'views' => 67,
        'author' => 'Фарход Рахимов', 'created_label' => '1 час назад',
    ],
    [
        'id' => 4, 'title' => 'Как правильно настроить миграции Laravel?',
        'excerpt' => 'Ищу удобный способ организовать миграции для нескольких связанных таблиц.',
        'tags' => ['laravel', 'php'], 'category' => 'Программирование',
        'votes' => 3, 'answer_count' => 4, 'views' => 92,
        'author' => 'Джамшед Гуломов', 'created_label' => '2 часа назад',
    ],
    [
        'id' => 5, 'title' => 'Почему кириллица некорректно отображается в JSON?',
        'excerpt' => 'Как сохранить UTF-8 при обмене данными между PHP и браузером?',
        'tags' => ['php', 'unicode'], 'category' => 'Программирование',
        'votes' => 2, 'answer_count' => 1, 'views' => 41,
        'author' => 'Нигина Саидова', 'created_label' => '3 часа назад',
    ],
    [
        'id' => 6, 'title' => '<img src=x onerror="alert(1)"> Проверка безопасного вывода',
        'excerpt' => 'Строка с HTML-разметкой должна показываться как обычный текст, а не выполняться.',
        'tags' => ['безопасность', 'javascript'], 'category' => 'Программирование',
        'votes' => 1, 'answer_count' => 0, 'views' => 9,
        'author' => 'Тестовый пользователь', 'created_label' => 'только что',
    ],
];

$lower = static function (string $value): string {
    return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
};
$query = $lower(trim((string) ($_GET['q'] ?? '')));
$category = $lower(trim((string) ($_GET['category'] ?? '')));

if ($query !== '' || $category !== '') {
    $questions = array_values(array_filter($questions, static function (array $question) use ($query, $category, $lower): bool {
        $searchable = $lower(implode(' ', [
            $question['title'], $question['excerpt'], $question['author'],
            $question['category'], implode(' ', $question['tags']),
        ]));
        return ($query === '' || str_contains($searchable, $query))
            && ($category === '' || $lower($question['category']) === $category);
    }));
}

echo json_encode($questions, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
