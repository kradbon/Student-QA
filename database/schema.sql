
DROP DATABASE IF EXISTS student_qa_platform;

CREATE DATABASE student_qa_platform
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE student_qa_platform;


CREATE TABLE users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(120) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    role ENUM('student', 'moderator', 'admin')
        NOT NULL DEFAULT 'student',
    status ENUM('active', 'blocked')
        NOT NULL DEFAULT 'active',
    reputation INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_username (username),
    UNIQUE KEY uq_users_email (email),
    KEY idx_users_role_status (role, status),
    CONSTRAINT chk_users_reputation CHECK (reputation >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;


CREATE TABLE categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_categories_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;


CREATE TABLE questions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    author_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(200) NOT NULL,
    body TEXT NOT NULL,
    status ENUM('pending', 'published', 'rejected', 'closed')
        NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_questions_author (author_id),
    KEY idx_questions_category_status_created
        (category_id, status, created_at),
    KEY idx_questions_status_created (status, created_at),
    FULLTEXT KEY ft_questions_title_body (title, body),
    CONSTRAINT chk_questions_title CHECK (CHAR_LENGTH(title) >= 5),
    CONSTRAINT fk_questions_author FOREIGN KEY (author_id)
        REFERENCES users(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_questions_category FOREIGN KEY (category_id)
        REFERENCES categories(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;


CREATE TABLE answers (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    question_id BIGINT UNSIGNED NOT NULL,
    author_id BIGINT UNSIGNED NOT NULL,
    body TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_answers_id_question (id, question_id),
    KEY idx_answers_question_created (question_id, created_at),
    KEY idx_answers_author (author_id),
    CONSTRAINT chk_answers_body CHECK (CHAR_LENGTH(body) >= 2),
    CONSTRAINT fk_answers_question FOREIGN KEY (question_id)
        REFERENCES questions(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_answers_author FOREIGN KEY (author_id)
        REFERENCES users(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;


CREATE TABLE accepted_answers (
    question_id BIGINT UNSIGNED NOT NULL,
    answer_id BIGINT UNSIGNED NOT NULL,
    accepted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (question_id),
    UNIQUE KEY uq_accepted_answers_answer (answer_id),
    CONSTRAINT fk_accepted_question FOREIGN KEY (question_id)
        REFERENCES questions(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_accepted_answer_question
        FOREIGN KEY (answer_id, question_id)
        REFERENCES answers(id, question_id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;


CREATE TABLE tags (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_tags_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;


CREATE TABLE question_tags (
    question_id BIGINT UNSIGNED NOT NULL,
    tag_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (question_id, tag_id),
    KEY idx_question_tags_tag (tag_id),
    CONSTRAINT fk_question_tags_question FOREIGN KEY (question_id)
        REFERENCES questions(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_question_tags_tag FOREIGN KEY (tag_id)
        REFERENCES tags(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;


CREATE TABLE votes (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    voter_id BIGINT UNSIGNED NOT NULL,
    answer_id BIGINT UNSIGNED NOT NULL,
    vote_type ENUM('upvote', 'downvote') NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_votes_voter_answer (voter_id, answer_id),
    KEY idx_votes_answer_type (answer_id, vote_type),
    CONSTRAINT fk_votes_voter FOREIGN KEY (voter_id)
        REFERENCES users(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_votes_answer FOREIGN KEY (answer_id)
        REFERENCES answers(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;


CREATE TABLE reputation_transactions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    answer_id BIGINT UNSIGNED NULL,
    points INT NOT NULL,
    reason ENUM('best_answer', 'moderation', 'other') NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_reputation_user_created (user_id, created_at),
    KEY idx_reputation_answer (answer_id),
    CONSTRAINT chk_reputation_points CHECK (points <> 0),
    CONSTRAINT fk_reputation_user FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_reputation_answer FOREIGN KEY (answer_id)
        REFERENCES answers(id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- 10. File attachments.
CREATE TABLE attachments (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    question_id BIGINT UNSIGNED NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    uploaded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_attachments_question (question_id),
    CONSTRAINT fk_attachments_question FOREIGN KEY (question_id)
        REFERENCES questions(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- 11. Notifications.
CREATE TABLE notifications (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50) NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_notifications_user_read_created
        (user_id, is_read, created_at),
    CONSTRAINT chk_notifications_is_read CHECK (is_read IN (0, 1)),
    CONSTRAINT fk_notifications_user FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- 12. Moderation audit log.
CREATE TABLE moderation_logs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    moderator_id BIGINT UNSIGNED NOT NULL,
    question_id BIGINT UNSIGNED NULL,
    action ENUM('approve', 'reject', 'close', 'reopen') NOT NULL,
    reason TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_moderation_moderator_created (moderator_id, created_at),
    KEY idx_moderation_question_created (question_id, created_at),
    CONSTRAINT fk_moderation_moderator FOREIGN KEY (moderator_id)
        REFERENCES users(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_moderation_question FOREIGN KEY (question_id)
        REFERENCES questions(id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
