
-- Student Q&A Platform — test data
USE student_qa_platform;
SET NAMES utf8mb4;

-- All accounts are fictional.
-- Generate real password hashes with PHP password_hash()
-- before using these accounts to log in.

INSERT INTO users
(id, username, email, password_hash, full_name, role, status, reputation)
VALUES
(1, 'student_ali', 'ali@example.tj', 'REPLACE_WITH_BCRYPT_HASH', 'Али Раҳимов', 'student', 'active', 15),
(2, 'student_madina', 'madina@example.tj', 'REPLACE_WITH_BCRYPT_HASH', 'Мадина Саидова', 'student', 'active', 30),
(3, 'student_farrukh', 'farrukh@example.tj', 'REPLACE_WITH_BCRYPT_HASH', 'Фаррух Назаров', 'student', 'active', 5),
(4, 'moderator_sara', 'sara@example.tj', 'REPLACE_WITH_BCRYPT_HASH', 'Сара Каримова', 'moderator', 'active', 0),
(5, 'admin_umar', 'umar@example.tj', 'REPLACE_WITH_BCRYPT_HASH', 'Умар Мирзоев', 'admin', 'active', 0),
(6, 'blocked_user', 'blocked@example.tj', 'REPLACE_WITH_BCRYPT_HASH', 'Бахтиёр Ҷӯраев', 'student', 'blocked', 0);

INSERT INTO categories (id, name, description) VALUES
(1, 'PHP', 'Саволҳо дар бораи PHP ва Laravel'),
(2, 'Frontend', 'HTML, CSS, JavaScript ва React'),
(3, 'Databases', 'MySQL, SQL ва тарҳрезии база'),
(4, 'Networking', 'Шабакаҳо ва протоколҳо'),
(5, 'DevOps', 'Docker, Linux ва ҷойгиркунии барнома');

INSERT INTO questions
(id, author_id, category_id, title, body, status, created_at)
VALUES
(1, 1, 1, 'Laravel чист ва барои чӣ истифода мешавад?',
 'Лутфан фаҳмонед, ки Laravel кадом вазифаҳоро дар таҳияи веб иҷро мекунад.',
 'published', '2026-10-01 09:00:00'),
(2, 2, 2, 'Фарқи React ва Vue дар чист?',
 'Барои лоиҳаи донишҷӯӣ кадом framework мувофиқтар аст?',
 'published', '2026-10-02 10:30:00'),
(3, 3, 3, 'Индекси MySQL чӣ гуна кор мекунад?',
 'Мехоҳам фаҳмам, ки кай индекс суръати SELECT-ро зиёд мекунад.',
 'published', '2026-10-03 11:15:00'),
(4, 1, 5, 'Чӣ тавр Laravel-ро бо Docker оғоз кунем?',
 'Барои муҳити маҳаллӣ кадом контейнерҳо лозиманд?',
 'pending', '2026-10-04 12:00:00'),
(5, 6, 4, 'Масъалаи шабака дар компютер',
 'Ин савол барои санҷиши ҳолати модератсия мебошад.',
 'rejected', '2026-10-05 08:45:00');

INSERT INTO answers (id, question_id, author_id, body, created_at)
VALUES
(1, 1, 2, 'Laravel framework-и PHP мебошад. Он routing, middleware, ORM ва воситаҳои амниятиро пешниҳод мекунад.', '2026-10-01 10:00:00'),
(2, 1, 3, 'Laravel барои сохтани барномаҳои веб бо сохтори MVC ва абзорҳои тайёр истифода мешавад.', '2026-10-01 11:00:00'),
(3, 2, 1, 'Ҳар ду framework-и компонентӣ мебошанд. Интихоб аз таҷрибаи гурӯҳ ва талаботи лоиҳа вобаста аст.', '2026-10-02 12:00:00'),
(4, 2, 3, 'React китобхона аст, Vue бошад framework-и прогрессивӣ мебошад.', '2026-10-02 13:00:00'),
(5, 3, 2, 'Индекс сохтори иловагӣ месозад, то база сатрҳоро зудтар пайдо кунад.', '2026-10-03 12:30:00'),
(6, 4, 2, 'Одатан PHP-FPM, веб-сервер ва MySQL-ро дар контейнерҳои алоҳида ҷойгир мекунанд.', '2026-10-04 13:00:00');

INSERT INTO accepted_answers (question_id, answer_id, accepted_at)
VALUES
(1, 1, '2026-10-01 12:00:00'),
(2, 4, '2026-10-02 14:00:00'),
(3, 5, '2026-10-03 13:00:00');

INSERT INTO tags (id, name) VALUES
(1, 'laravel'),
(2, 'php'),
(3, 'react'),
(4, 'mysql'),
(5, 'docker'),
(6, 'sql');

INSERT INTO question_tags (question_id, tag_id) VALUES
(1, 1), (1, 2),
(2, 3),
(3, 4), (3, 6),
(4, 1), (4, 5);

INSERT INTO votes (id, voter_id, answer_id, vote_type, created_at)
VALUES
(1, 1, 1, 'upvote', '2026-10-01 12:10:00'),
(2, 3, 1, 'upvote', '2026-10-01 12:15:00'),
(3, 1, 2, 'downvote', '2026-10-01 12:20:00'),
(4, 2, 3, 'upvote', '2026-10-02 14:10:00'),
(5, 1, 5, 'upvote', '2026-10-03 13:10:00');

INSERT INTO reputation_transactions
(id, user_id, answer_id, points, reason, created_at)
VALUES
(1, 2, 1, 10, 'best_answer', '2026-10-01 12:00:00'),
(2, 3, 2, 5, 'other', '2026-10-01 12:30:00'),
(3, 3, NULL, -2, 'moderation', '2026-10-02 09:00:00'),
(4, 1, 5, 10, 'best_answer', '2026-10-03 13:00:00'),
(5, 2, 5, 3, 'other', '2026-10-03 13:30:00');

INSERT INTO attachments
(id, question_id, file_name, file_path, mime_type)
VALUES
(1, 1, 'laravel-notes.pdf', 'questions/1/laravel-notes.pdf', 'application/pdf'),
(2, 3, 'mysql-index.png', 'questions/3/mysql-index.png', 'image/png'),
(3, 4, 'docker-compose.yml', 'questions/4/docker-compose.yml', 'text/yaml');

INSERT INTO notifications
(id, user_id, message, type, is_read)
VALUES
(1, 1, 'Ҷавоби шумо қабул карда шуд.', 'answer_accepted', 1),
(2, 2, 'Ҷавоби шумо ҳамчун ҷавоби беҳтарин интихоб шуд.', 'best_answer', 0),
(3, 3, 'Ба саволи шумо ҷавоби нав илова шуд.', 'new_answer', 0),
(4, 4, 'Саволи нав барои санҷиши модератор омода аст.', 'moderation_queue', 1),
(5, 6, 'Ҳисоби шумо маҳдуд карда шудааст.', 'account_status', 0);

INSERT INTO moderation_logs
(id, moderator_id, question_id, action, reason, created_at)
VALUES
(1, 4, 1, 'approve', 'Савол ба қоидаҳои ҷомеа мувофиқ аст.', '2026-10-01 08:55:00'),
(2, 4, 2, 'approve', 'Мавзӯъ равшан ва мувофиқ аст.', '2026-10-02 10:25:00'),
(3, 4, 4, 'approve', 'Савол баъд аз санҷиш нашр карда мешавад.', '2026-10-04 12:30:00'),
(4, 4, 5, 'reject', 'Матни савол маълумоти кофӣ надорад.', '2026-10-05 09:00:00'),
(5, 4, 3, 'close', 'Савол барои ҷавобҳои нав баста шуд.', '2026-10-06 09:30:00');
