<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;

final class QuestionController extends Controller
{
    /** Demo rows use the same column names as database/schema.sql: questions, users and categories. */
    private array $questions = [
        [
            'id' => 1, 'author_id' => 2, 'category_id' => 1,
            'title' => 'Почему асинхронная функция возвращает Promise?',
            'body' => 'Разбираюсь с async/await в JavaScript. Почему результат функции нужно получать через await? Я ожидал, что функция вернёт готовое значение, но вижу объект Promise.',
            'status' => 'published', 'created_at' => '2026-11-22 10:15:00', 'updated_at' => '2026-11-24 09:30:00',
            'author_name' => 'Илья Смирнов', 'category_name' => 'Программирование', 'votes_count' => 12, 'answers_count' => 3,
        ],
        [
            'id' => 2, 'author_id' => 3, 'category_id' => 2,
            'title' => 'Как оптимизировать SQL-запрос с несколькими JOIN?',
            'body' => 'Запрос медленно работает на таблице с большим количеством записей. Как найти узкое место и понять, какие индексы нужны?',
            'status' => 'published', 'created_at' => '2026-11-23 08:40:00', 'updated_at' => '2026-11-23 08:40:00',
            'author_name' => 'Мадина Каримова', 'category_name' => 'Базы данных', 'votes_count' => 8, 'answers_count' => 2,
        ],
        [
            'id' => 3, 'author_id' => 4, 'category_id' => 3,
            'title' => 'В чём разница между Flexbox и CSS Grid?',
            'body' => 'Для адаптивной сетки карточек лучше использовать Grid или Flexbox? Интересует практическое правило выбора.',
            'status' => 'published', 'created_at' => '2026-11-24 07:20:00', 'updated_at' => '2026-11-24 07:20:00',
            'author_name' => 'Фарход Рахимов', 'category_name' => 'Веб-разработка', 'votes_count' => 5, 'answers_count' => 0,
        ],
        [
            'id' => 4, 'author_id' => 5, 'category_id' => 1,
            'title' => 'Как правильно настроить миграции Laravel?',
            'body' => 'Ищу удобный способ организовать миграции для нескольких связанных таблиц и не потерять целостность данных.',
            'status' => 'published', 'created_at' => '2026-11-24 06:10:00', 'updated_at' => '2026-11-24 06:10:00',
            'author_name' => 'Джамшед Гуломов', 'category_name' => 'Программирование', 'votes_count' => 3, 'answers_count' => 4,
        ],
        [
            'id' => 5, 'author_id' => 6, 'category_id' => 1,
            'title' => 'Почему кириллица некорректно отображается в JSON?',
            'body' => 'Как сохранить UTF-8 при обмене данными между PHP и браузером и не получить нечитаемые символы?',
            'status' => 'published', 'created_at' => '2026-11-24 05:00:00', 'updated_at' => '2026-11-24 05:00:00',
            'author_name' => 'Нигина Саидова', 'category_name' => 'Программирование', 'votes_count' => 2, 'answers_count' => 1,
        ],
    ];

    public function index(): void
    {
        $query = trim((string) ($_GET['q'] ?? ''));
        $questions = $this->questions;
        if ($query !== '') {
            $questions = array_values(array_filter($questions, static function (array $question) use ($query): bool {
                return preg_match('/' . preg_quote($query, '/') . '/iu', $question['title'] . ' ' . $question['body'] . ' ' . $question['category_name']) === 1;
            }));
        }

        $this->render('questions/index', [
            'pageTitle' => 'Саволҳои ҷомеа',
            'activePage' => 'questions',
            'questions' => $questions,
            'searchQuery' => $query,
        ]);
    }

    public function show(string $id): void
    {
        if (!ctype_digit($id)) {
            Response::error(404, '404 — Question Not Found', 'Саволи дархостшуда вуҷуд надорад.');
            return;
        }

        foreach ($this->questions as $question) {
            if ($question['id'] === (int) $id) {
                $answers = $question['id'] === 1 ? [
                    ['id' => 1, 'question_id' => 1, 'author_id' => 7, 'body' => 'Функция, объявленная с ключевым словом async, всегда возвращает Promise. Это позволяет использовать await внутри функции и удобно обрабатывать завершение асинхронной операции.', 'created_at' => '2026-11-24 08:00:00', 'author_name' => 'Мария Каримова', 'votes_count' => 18, 'is_accepted' => true],
                    ['id' => 2, 'question_id' => 1, 'author_id' => 8, 'body' => 'Ещё можно вызвать .then() у возвращаемого Promise. Оба варианта подходят, но await часто читается проще, если операций несколько.', 'created_at' => '2026-11-24 09:00:00', 'author_name' => 'Фарход Рахимов', 'votes_count' => 6, 'is_accepted' => false],
                ] : [];

                $this->render('questions/show', [
                    'pageTitle' => $question['title'],
                    'activePage' => 'questions',
                    'question' => $question,
                    'answers' => $answers,
                ]);
                return;
            }
        }

        Response::error(404, '404 — Question Not Found', 'Саволи бо ID-и ' . $id . ' ёфт нашуд.');
    }
}
