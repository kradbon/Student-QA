USE student_qa;
INSERT INTO roles(name) VALUES ('student'),('moderator'),('admin');
-- Demo password for all accounts: password (change immediately outside development).
INSERT INTO users(role_id,full_name,email,password_hash,reputation) VALUES
(1,'Demo Student','student@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',25),
(2,'Demo Moderator','moderator@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',100),
(3,'Demo Administrator','admin@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',200);
INSERT INTO tags(name,description) VALUES
('php','PHP programming'),('javascript','JavaScript'),('mysql','Database'),('html','HTML'),('css','CSS'),
('python','Python'),('java','Java'),('csharp','C#'),('networking','Computer networks'),('linux','Linux'),
('algorithms','Algorithms'),('web','Web development');
INSERT INTO questions(user_id,title,body) VALUES (1,"How do PHP sessions work?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"What is the difference between GET and POST?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"How can I connect PHP to MySQL using PDO?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"What is a primary key in a database?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"How do SQL joins work?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"What is normalization in database design?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"How do I validate an HTML form?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"What is the CSS box model?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"How does JavaScript fetch API work?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"What is the difference between let and const?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"How can I prevent SQL injection?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"What is cross-site scripting (XSS)?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"What is a CSRF token?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"How do I hash passwords in PHP?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"What is the purpose of an HTTP status code?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"How does DNS resolution work?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"What is the difference between TCP and UDP?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"How do I use Git branches?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"What is responsive web design?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"How can I create pagination in PHP?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"What is an index in MySQL?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"How do foreign keys maintain integrity?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"What is REST API architecture?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"How can I return JSON from PHP?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"What is the difference between frontend and backend?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"How do Linux file permissions work?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"What is an algorithm complexity?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"How do I upload a file securely in PHP?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"How can I debug JavaScript in a browser?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
INSERT INTO questions(user_id,title,body) VALUES (1,"What is the MVC design pattern?","I am studying this topic for my university course. Could someone explain the concept and provide a practical example?");
