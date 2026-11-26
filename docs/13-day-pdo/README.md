# Рӯзи 13 — PDO, дархостҳои бехатар ва транзаксияҳо

Ин ҳуҷҷат идомаи намунаи алоҳидаи MVC аз [рӯзи 12](../12-day-mvc/README.md) мебошад. Версияи рӯзи 12 аз массивҳои намоишӣ истифода мекард; дар ин машқ маълумот бояд аз пойгоҳи додаҳои лоиҳа `student_qa_platform` гирифта шавад. Моҳияти асосӣ — савол (`questions`), маълумотнома — категория (`categories`), соҳиби сабт — корбар (`users`).

## Ҳадафҳо

- Пайвастшавӣ ба MySQL бо PDO, DSN-и `charset=utf8mb4`, истисноҳо ва `FETCH_ASSOC`.
- Ҳамаи қиматҳои корбарро ба SQL танҳо prepared statements-ээр дамжуулан додан.
- Гузаронидани SQL ба `QuestionRepository` ва маълумотномаҳо ба `CategoryRepository`.
- Илова кардани ҷустуҷӯ, филтри категория/ҳолат, JOIN ва саҳифабандӣ.
- CRUD барои саволҳо бо санҷиши серверӣ, PRG ва flash-паёмҳо.
- Як амалиёти дуҷадвала дар транзаксия.

## 1. Танзимоти махфӣ ва пайвасти PDO

Дар `config/config.example.php` танҳо намунаи танзимотро нигоҳ доред. `config/config.php` бояд дар `.gitignore` бошад. Қиматҳои муҳити сервер аз намуна бартарӣ доранд:

```php
<?php
return [
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => (int) (getenv('DB_PORT') ?: 3306),
        'name' => getenv('DB_NAME') ?: 'student_qa_platform',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: '',
    ],
    'debug' => (getenv('APP_DEBUG') ?: '1') === '1',
];
```

`App\Core\Database` пайвастшавиро танбалона месозад ва ҳамон PDO-ро ба ҳамаи репозиторияҳо медиҳад:

```php
final class Database
{
    private static ?PDO $pdo = null;
    private static array $config = [];

    public static function configure(array $config): void
    {
        self::$config = $config;
    }

    public static function connection(): PDO
    {
        if (self::$pdo === null) {
            $c = self::$config['db'];
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                $c['host'], $c['port'], $c['name']
            );
            self::$pdo = new PDO($dsn, $c['user'], $c['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return self::$pdo;
    }
}
```

`utf8mb4` барои нигоҳ доштани тамоми Unicode зарур аст. `ERRMODE_EXCEPTION` хатои SQL-ро ошкор мекунад; коркардкунандаи умумии истисноҳо бояд тафсилотро ба `error_log()` нависад ва дар ҳолати production танҳо саҳифаи умумии 500 диҳад. Парол ва матни SQL ба корбар нишон дода намешаванд.

## 2. Дархостҳои параметрнок ва пешгирии SQL injection

`query()` танҳо барои SQL-и доимӣ ва бе ягон додаи беруна мувофиқ аст. Барои ҳар қимате, ки аз URL ё шакл меояд, `prepare()` ва `execute()` истифода баред:

```php
$stmt = $this->db->prepare(
    'SELECT id, title FROM questions WHERE id = :id'
);
$stmt->execute(['id' => $id]);
$question = $stmt->fetch() ?: null;
```

Ин тарзи нодуруст осебпазир аст:

```php
$sql = "SELECT id, title FROM questions WHERE title LIKE '%$q%'";
```

Вуруди `' OR 1=1 -- ` метавонад шартро ҳамеша ҳақиқӣ гардонад. Дар шакли логин ё CRUD ин метавонад маълумоти дигарро ошкор кунад ё тағйир диҳад. Ислоҳ — параметр:

```php
$stmt = $this->db->prepare(
    'SELECT id, title FROM questions WHERE title LIKE :q'
);
$stmt->execute(['q' => '%' . addcslashes($q, '%_\\') . '%']);
```

Prepared statement қиматро аз сохтори SQL ҷудо мекунад. `addcslashes()` дар ин мисол `LIKE`-и махсуси `%` ва `_`-ро ҳамчун аломатҳои ҷустуҷӯ escape мекунад; ин нь SQL injection-ий ҳимояи асосӣ нест.

Ҷойнишин танҳо **қимат** иваз мекунад, на номи сутун, ҷадвал ё самти `ORDER BY`. Барои тартибдиҳӣ рӯйхати сафед истифода кунед:

```php
$sorts = ['newest' => 'q.created_at DESC', 'title' => 'q.title ASC'];
$orderBy = $sorts[$requestedSort] ?? $sorts['newest'];
```

## 3. Repository ва JOIN

SQL дар `src/Repositories/QuestionRepository.php` ҷойгир мешавад, на дар контроллер. Конструктор PDO-и ихтиёрӣ қабул мекунад, то дар машқҳои баъдӣ пайвастшавии тестиро ворид кардан мумкин бошад.

Намунаи рӯйхати саволҳо бо JOIN ба корбар ва категория:

```sql
SELECT q.*, u.full_name AS author_name, c.name AS category_name
FROM questions q
JOIN users u ON u.id = q.author_id
JOIN categories c ON c.id = q.category_id
```

`JOIN` барои ин калидҳои берунии ҳатмӣ мувофиқ аст. Агар алоқа ихтиёрӣ бошад ва сатри асосӣ бояд ҳатман дар натиҷа монад, `LEFT JOIN` истифода мешавад.

Методҳои ҳадди ақали репозитория: `paginate($filters, $page, $perPage)`, `find($id)`, `create($data)`, `update($id, $data)`, `delete($id)`. Майдонҳои иҷозатдодашуда ҳамчун доимӣ муайян карда мешаванд, масалан:

```php
public const FIELDS = ['category_id', 'title', 'body', 'status'];
```

`onlyFields()` аз маълумоти POST танҳо ҳамин майдонҳоро мегузаронад ва аз mass assignment пешгирӣ мекунад. `author_id` бояд аз сессияи корбар гирифта шавад, на аз шакл.

## 4. Ҷустуҷӯ, филтр ва саҳифабандӣ

Филтрҳои `q`, `category_id`, `status` бо шартҳои доимии SQL ҷамъ мешаванд; қиматҳо дар массиви параметрҳо мемонанд. Ҳолатро бо allowlist-и `pending`, `published`, `rejected`, `closed` санҷед. ID-и категорияро адади мусбат кунед ва мавҷудияти онро дар пойгоҳи додаҳо тасдиқ намоед.

Саҳифабандӣ ду дархост дорад: `COUNT(*)` бо ҳамон `WHERE`, сипас `SELECT ... ORDER BY ... LIMIT :limit OFFSET :offset`. `LIMIT` ва `OFFSET`-ро бо `bindValue(..., PDO::PARAM_INT)` бандед. Саҳифа чунин ҳисоб мешавад: `pages = max(1, ceil(total / perPage))`; `offset = (page - 1) * perPage`. `ORDER BY`-д илова кунед, ки ID-и ягона ҳамчун охирин tie-breaker дошта бошад. Истинодҳои саҳифавӣ бояд query-филтрҳоро нигоҳ доранд.

## 5. CRUD, валидатсия ва PRG

Маршрутҳои пешниҳодшуда:

| Усул | Масир | Амал |
|---|---|---|
| GET | `/questions` | рӯйхат, ҷустуҷӯ ва саҳифабандӣ |
| GET | `/questions/create` | шакли эҷод |
| POST | `/questions/create` | эҷод |
| GET | `/questions/{id}` | намоиши сабт |
| GET | `/questions/{id}/edit` | шакли таҳрир |
| POST | `/questions/{id}/edit` | навсозӣ |
| POST | `/questions/{id}/delete` | нест кардан |

Дар сервер ҳадди ақал инҳоро санҷед: сарлавҳа ҳатмӣ ва дарозии 5–200, матн ҳадди ақал 2 аломат, категория аз ID-ҳои воқеии `CategoryRepository::ids()`, ҳолат аз allowlist. Ҳангоми хатоҳо шаклро бо HTTP 422 ва арзишҳои воридшуда боз нишон диҳед.

Пас аз POST-и муваффақ, контроллер сабтро нигоҳ дошта ба GET redirect мекунад (PRG — Post/Redirect/Get). Ин F5-ро аз такрори POST нигоҳ медорад. Паёми flash дар сессия танҳо як бор нишон дода мешавад. Нест кардан танҳо POST route аст; GET дархост ҳеҷ гоҳ маълумотро тағйир надиҳад.

## 6. Транзаксия: қабул кардани ҷавоб ва обрӯ

Барои мисоли дуҷадвалаи ин лоиҳа қабули ҷавоби беҳтарин (`accepted_answers`) ва илова кардани сабти хол ба `reputation_transactions` як воҳид мебошанд. Агар ворид кардани хол ноком шавад, қабул низ бояд бекор шавад. Ҳамаи амалҳо бо як PDO иҷро мешаванд:

```php
$this->db->beginTransaction();
try {
    // UPDATE-и шартӣ: танҳо ҷавоби саволи кушода/нашуда қабул мешавад.
    $stmt = $this->db->prepare(
        'INSERT INTO accepted_answers (question_id, answer_id) '
        . 'SELECT :question_id, a.id FROM answers a '
        . 'WHERE a.id = :answer_id AND a.question_id = :same_question '
        . 'AND NOT EXISTS (SELECT 1 FROM accepted_answers x '
        . 'WHERE x.question_id = a.question_id)'
    );
    $stmt->execute([
        'question_id' => $questionId,
        'answer_id' => $answerId,
        'same_question' => $questionId,
    ]);
    if ($stmt->rowCount() !== 1) {
        $this->db->rollBack();
        return false;
    }

    $stmt = $this->db->prepare(
        'INSERT INTO reputation_transactions (user_id, answer_id, points, reason) '
        . 'SELECT author_id, id, 10, \'best_answer\' FROM answers WHERE id = :id'
    );
    $stmt->execute(['id' => $answerId]);
    $this->db->commit();
    return true;
} catch (Throwable $e) {
    if ($this->db->inTransaction()) {
        $this->db->rollBack();
    }
    throw $e;
}
```

Дар истифодаи воқеӣ, қабули ҷавобро танҳо муаллифи савол ё нақши иҷозатдодашуда иҷро мекунад; ин иҷозатро контроллер/қабати хизматрасонӣ месанҷад. Калиди аввалияи `accepted_answers.question_id` қабули ҷавоби дуюмро ҳамзамон манъ мекунад. Агар insert-и дуюм хато диҳад, `rollBack()` ҳар ду тағйиротро бармегардонад. Амалҳои беруна, мисли фиристодани почта, набояд дар дохили транзаксия бошанд.

## 7. Нақшаи сохтани машқ дар ин репозитория

1. `config/config.example.php`, `config/config.php`-и маҳаллӣ ва `.gitignore`-ро омода кунед.
2. `Database`, `Validator`, `QuestionRepository` ва `CategoryRepository` созед.
3. Масирҳо ва `QuestionController`-ро аз demo array ба PDO гузаронед.
4. JOIN, ҷустуҷӯ, филтрҳо ва саҳифабандии устуворро илова кунед.
5. Шаклҳои CRUD, POST-only delete, санҷиши серверӣ, PRG ва flash-ро амалӣ кунед.
6. Амалиёти дуҷадвалаи транзаксияро дар repository ё қабати service илова кунед և դաշտերի թույլատրելիությունը ստուգեք։
7. README-и худро լրացրեք՝ փորձելով որոնում `'`, `' OR 1=1 -- `, `%` և кириллии `Ғ`, `Ӯ`, `Ҷ`; замима кунед натиҷаҳо ё скриншотҳо.

## Санҷиши амният ва қабули кор

- SQL injection: воридоти `' OR 1=1 -- ` ҳамаи сабтҳоро барнагардонад; ҷустуҷӯ параметрнок аст.
- `%` ҳамчун аломати ҷустуҷӯ кор намекунад, агар корбар онро айнан ҷустуҷӯ кунад.
- Матни дорои `Ғ`, `Ӯ`, `Ҷ` бе вайроншавӣ нигоҳ дошта ва ҷустуҷӯ мешавад (`utf8mb4`).
- Парол дар Git нест; корбари SQL дар истеҳсолот `root` нест.
- SQL дар репозиторияҳо аст, маълумоти шакл дар контроллер/валидатор санҷида мешавад.
- Пагинатсия филтрҳоро нигоҳ медорад; эҷод/таҳрир/несткунӣ бо PRG/POST мувофиқанд.
- Хатои қадами дуюми транзаксия тағйироти қадами аввалро ҳам бекор мекунад.

Ин директория дастури татбиқ аст; файли мустақили рӯзи 12 бо массивҳои demo барои муқоиса нигоҳ дошта шудааст.
