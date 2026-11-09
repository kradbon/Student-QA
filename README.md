# Student Q&A — Variant 24

A standalone PHP 8.2+ / MySQL student question-and-answer platform. Backend frameworks are intentionally not used, in accordance with the course brief.

## Requirements
- PHP 8.2+ with PDO MySQL and mbstring
- MySQL 8.0+ or MariaDB 10.6+
- Composer (for PHPUnit development dependency)
- Apache/Nginx; document root should point to `public/`

## Local setup (XAMPP/Laragon)
1. Create a MySQL database by importing `database/schema.sql`.
2. Import `database/seed.sql`.
3. Copy `.env.example` to `.env` and set DB credentials.
4. Run `composer install`.
5. Configure Apache virtual host/document root to this project's `public` directory.
6. Open the local site in your browser.

For PHP's built-in development server, from the project root run:
`php -S 127.0.0.1:8000 -t public`
Then open `http://127.0.0.1:8000`.

## Development demo accounts
- student@example.com
- moderator@example.com
- admin@example.com
- Password: `password`

These accounts are for local development only. Replace demo credentials and remove seed users before deployment.

## Implemented foundation
- PDO database connection with native prepared statements
- Registration, login, logout and session ID regeneration
- Student, moderator and administrator roles
- Question creation, listing, detail view and pagination
- Answer submission
- Upvote/downvote with one vote per user per answer
- Reputation recalculation from answer votes
- Question author can select a best answer
- Live similar-question suggestions through a JSON endpoint
- CSRF token validation on POST requests and HTML output escaping
- Basic responsive Bootstrap 5 interface and language selector
- Initial PHPUnit test examples

## Course requirements still to complete before final submission
This repository is a working project foundation, not a claim that every rubric item is finished. Complete and verify:
- Full edit/delete CRUD for questions and answers, including ownership checks
- Full moderator report queue and admin user/role management
- Tag CRUD, tag filtering and search filters
- Secure image/document upload (MIME, extension, size, randomized name, storage outside executable paths)
- Notifications UI and read/unread workflow
- CSV export and printable reports using aggregate SQL
- Robust similar-question matching/ranking and AJAX UX
- Full multilingual translation coverage (all pages, validation and messages)
- 10+ documented manual test cases and 5+ business-rule PHPUnit tests
- Production HTTPS deployment, secure environment configuration, backups and deployment guide
- Figma screens and 30–40 page explanatory report

## Security notes
- Never deploy with `APP_DEBUG=true` or demo accounts.
- Use HTTPS, secure/HttpOnly/SameSite cookies, restrictive upload storage and a production web-server configuration.
- Add rate limiting for authentication, voting and report submission.
- Validate authorization on every state-changing endpoint.
- Review and test every SQL query and business rule before public deployment.
