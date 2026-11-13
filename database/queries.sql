
USE student_qa_platform;
SET NAMES utf8mb4;


SELECT
    q.id,
    q.title,
    q.status,
    q.created_at,
    u.username AS author,
    c.name AS category
FROM questions AS q
JOIN users AS u ON u.id = q.author_id
JOIN categories AS c ON c.id = q.category_id
WHERE q.status = 'published'
ORDER BY q.created_at DESC, q.id DESC
LIMIT 10 OFFSET 0;


SELECT
    c.id,
    c.name AS category,
    COUNT(q.id) AS published_questions
FROM categories AS c
LEFT JOIN questions AS q
    ON q.category_id = c.id
    AND q.status = 'published'
GROUP BY c.id, c.name
ORDER BY published_questions DESC, c.name;


SELECT
    q.id,
    q.title,
    q.body,
    u.username AS author,
    c.name AS category
FROM questions AS q
JOIN users AS u ON u.id = q.author_id
JOIN categories AS c ON c.id = q.category_id
WHERE q.status = 'published'
    AND MATCH(q.title, q.body)
        AGAINST ('Laravel' IN NATURAL LANGUAGE MODE)
ORDER BY q.created_at DESC
LIMIT 20;


SELECT
    q.id,
    q.title,
    q.status,
    q.created_at,
    u.username AS author
FROM questions AS q
JOIN users AS u ON u.id = q.author_id
WHERE q.status = 'pending'
ORDER BY q.created_at ASC
LIMIT 20;


SELECT
    name,
    CHAR_LENGTH(name) AS character_count,
    LENGTH(name) AS byte_count,
    HEX(name) AS utf8_bytes
FROM categories
WHERE id = 1;

SELECT
    @@character_set_database AS database_charset,
    @@collation_database AS database_collation,
    @@character_set_connection AS connection_charset,
    HEX('ҷ') AS tajik_letter_hex,
    HEX('я') AS cyrillic_letter_hex;
