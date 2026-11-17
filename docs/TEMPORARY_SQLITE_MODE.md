# Temporary Railway database mode

Railway currently starts this Laravel app with a local SQLite database while the hosted MySQL service is unavailable. The MySQL connection, migrations, and SQL schema remain in the project. The Railway start command selects SQLite, runs pending migrations and the existing seeder, then starts the web server. Sessions and cache use files, and queued work runs synchronously, so those features do not need MySQL either.

The existing seeder creates these accounts with the password `password`:

| Role | Email |
| --- | --- |
| Student | `student@example.com` |
| Student | `peer@example.com` |
| Moderator | `moderator@example.com` |
| Admin | `admin@example.com` |

All normal app forms, server-side validation, and role-based screens remain available. Registration also creates student accounts. Seeder execution does not clear tables; it creates or updates only its demo records.

## Return to MySQL

When the Railway MySQL service is ready again, remove the temporary `startCommand` in `railway.toml` (or replace it with the normal production start command), set Railway's `DB_CONNECTION=mysql` and MySQL connection variables, and deploy. The MySQL connection config and migration files are unchanged.

SQLite data created during the temporary period is separate from MySQL and is not merged automatically. Export or migrate any user-created data you need before switching back. The SQLite file lives in the deployment filesystem, so it can be lost when Railway replaces that instance; use a Railway volume mounted for the database path if that data must survive instance replacement.
