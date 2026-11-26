# Day 12 — Student Q&A Mini MVC (Variant 24)

This is a **separate plain-PHP MVC coursework implementation**. The original Laravel project in the parent directory is intentionally unchanged. The lecture assignment asks for a small custom MVC, so this implementation does not use Laravel routing, controllers, Blade, or Eloquent.

## Requirements and start

- PHP 8.1+ (the parent Laravel project may require a newer PHP version)
- Composer 2
- Apache with `mod_rewrite` for XAMPP, or PHP's built-in development server

From this directory:

```bash
composer install
php -S localhost:8000 -t public
```

Open `http://localhost:8000`. Composer is used for PSR-4 autoloading; there are no third-party runtime packages. For XAMPP, configure an Apache VirtualHost (or Alias) so its document root points to this folder's `public/` directory. This is important because `public/` must be the web root; do not expose `src/` or `views/` directly. Enable `mod_rewrite` and allow `.htaccess` overrides.

## Route table

| Method | Path | Handler | Template |
|---|---|---|---|
| GET | `/` | `HomeController::index` | `views/home.php` |
| GET | `/questions` | `QuestionController::index` | `views/questions/index.php` |
| GET | `/questions/{id}` | `QuestionController::show` | `views/questions/show.php` |
| GET | `/reference` | `ReferenceController::index` | `views/reference.php` |

## Architecture

- `public/index.php` — front controller; loads Composer and registers routes.
- `src/Core/Router.php` — matches paths, extracts `{id}`, dispatches handlers, returns 404/405.
- `src/Core/View.php` — renders a page template and inserts it into one shared layout.
- `src/Core/Response.php` — sends HTML and HTTP status codes.
- `src/Controllers/Controller.php` — base controller with shared `render()` method.
- `src/Controllers/*Controller.php` — page-specific controllers.
- `views/layout.php` — the shared layout; navigation, footer and pagination are partials.
- `public/assets/` — CSS and JavaScript.

All PHP class files use `declare(strict_types=1);`. Dynamic values are escaped with `htmlspecialchars()`; question body text uses `nl2br(htmlspecialchars(...))`, so HTML supplied as data is displayed as text rather than executed.

## Demo data and database columns

This exercise uses demo arrays, not a database connection. Question rows preserve the names from the parent project's `database/schema.sql` `questions` table: `id`, `author_id`, `category_id`, `title`, `body`, `status`, `created_at`, and `updated_at`. `author_name`, `category_name`, `votes_count`, and `answers_count` are display-only values representing related/aggregated data. The reference page also documents the corresponding `users`, `categories`, and `answers` fields.

## HTTP checks

Start the server, then run:

```bash
curl -i http://localhost:8000/
curl -i http://localhost:8000/questions
curl -i http://localhost:8000/questions/1
curl -i http://localhost:8000/questions/999
curl -i http://localhost:8000/not-a-route
curl -i -X POST http://localhost:8000/questions
```

Expected: existing pages `200`, unknown ID/path `404`, and `POST` to a GET-only route `405` with `Allow: GET`.

To verify XSS escaping, temporarily change one demo question's `title` to `<b>Test</b>` and reload `/questions`: the browser must display the literal tag text, not bold markup.

## Oral-defense flow

1. The browser sends an HTTP request to the web server.
2. The web server forwards the request to `public/index.php` (front controller).
3. Composer's autoloader resolves `App\\` to `src/` using PSR-4.
4. Router compares HTTP method and URL path with registered routes; `{id}` becomes a route parameter.
5. The matching controller prepares data and calls the base controller's `render()` method.
6. View renders the page template, then inserts its output into the one shared `layout.php`.
7. Response sends generated HTML and the status code to the browser.
8. `htmlspecialchars()` prevents data from being interpreted as HTML/JavaScript (XSS protection).

## Git

Run these commands from the Student Q&A repository root after reviewing the result:

```bash
git add docs/12-day-mvc
git commit -m "Add Day 12 plain PHP MVC implementation"
git tag day-12
```
