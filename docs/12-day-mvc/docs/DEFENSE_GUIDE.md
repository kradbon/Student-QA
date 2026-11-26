# Day 12 — тавзеҳ барои ҳимоя

Ин ҳуҷҷат барои фаҳмондани коди mini-MVC ҳангоми ҳимоя аст. Ин қисми лоиҳа аз Laravel ҷудо аст, зеро супориши лексия MVC-и хурдро дар PHP талаб мекунад.

## 1. `composer.json` ва PSR-4

- `name` номи бастаи Composer аст; он барои муайян кардани лоиҳа истифода мешавад.
- `require.php` версияи ҳадди ақали PHP-ро муайян мекунад.
- `autoload.psr-4` мегӯяд, ки namespace-и `App\\` ба папкаи `src/` мувофиқат мекунад.
- Вақте ки код `new App\\Core\\Router()`-ро истифода мебарад, Composer файли `src/Core/Router.php`-ро меёбад.
- `composer install` autoloader-и Composer-ро дар `vendor/autoload.php` месозад.
- `vendor/` ба `.gitignore` дохил шудааст, зеро вобастагиҳо аз рӯи `composer.json` дубора насб мешаванд.

## 2. `public/index.php` — Front Controller

1. `declare(strict_types=1)` санҷиши қатъии навъҳоро барои ин файл фаъол мекунад.
2. `use` синфҳоро бо namespace-и кӯтоҳ дастрас мекунад.
3. `require dirname(__DIR__) . '/vendor/autoload.php'` autoloader-и Composer-ро бор мекунад.
4. `new Router()` объекти маршрутизатор месозад.
5. `$router->get(...)` чор масири GET-ро сабт мекунад: саҳифаи асосӣ, рӯйхати саволҳо, тафсилоти савол бо `{id}`, ва маълумотнома.
6. `dispatch()` method ва URI-и воқеии HTTP-запросро мегирад ва handler-и мувофиқро иҷро мекунад.

Front Controller нуқтаи ягонаи воридшавӣ барои ҳамаи URL-ҳои динамикӣ мебошад.

## 3. `src/Core/Router.php`

- `$routes` массиви масирҳои сабтшуда аст.
- `get()` масири GET-ро илова мекунад ва `add()` қисми умумии сабти масирҳост.
- `preg_quote()` аломатҳои махсуси URL-ро барои regex бехатар мекунад.
- `preg_replace_callback()` қисмати `{id}`-ро ба гурӯҳи номдор табдил медиҳад, ки як сегменти URL-ро мегирад.
- `dispatch()` query string-ро аз URI ҷудо мекунад, то `/questions/1?q=php` ҳамчун роҳи `/questions/1` санҷида шавад.
- Аввал path муқоиса мешавад. Агар path бошад, вале HTTP method мувофиқат накунад, ҷавоби `405 Method Not Allowed` бо header-и `Allow: GET` фиристода мешавад.
- Агар path умуман сабт нашуда бошад, ҷавоби `404 Not Found` бармегардад.
- Агар масир ёфт шавад, параметри `{id}` гирифта шуда, controller ва method-и он иҷро мешаванд.

## 4. `src/Core/View.php`

- `render()` номи template ва массиви маълумотро мегирад.
- Роҳи template ва layout аз папкаи `views/` сохта мешавад.
- `is_file()` месанҷад, ки ҳар ду файл вуҷуд доранд.
- `extract($data, EXTR_SKIP)` калидҳои массивро ҳамчун тағйирёбандаҳои PHP дастрас мекунад.
- `ob_start()` output buffering-ро оғоз мекунад; HTML-и template муваққатан дар буфер нигоҳ дошта мешавад.
- `ob_get_clean()` HTML-ро мегирад ва буферро мебандад.
- Баъд layout бор мешавад ва `$content` дар дохили он ҷойгир мегардад. Ҳамин тавр navbar/footer дар ҳар саҳифа такрор намешаванд.

## 5. `src/Core/Response.php`

- `html()` HTTP status code-ро муқаррар мекунад.
- `Content-Type: text/html; charset=UTF-8` ба браузер мегӯяд, ки ҷавоб HTML ва рамзгузории он UTF-8 аст.
- Header-ҳои иловагӣ, аз ҷумла `Allow: GET`, фиристода мешаванд.
- `error()` унвон ва паёми хатогиро бо `htmlspecialchars()` escape мекунад ва саҳифаи ҷавобии хато месозад.

## 6. Controller ва MVC

- `Controller` синфи асосӣ аст; ҳамаи controller-ҳои саҳифа аз он мерос мегиранд.
- `render()` дар controller ба `View::render()` муроҷиат мекунад ва натиҷаро тавассути `Response::html()` мефиристад.
- `HomeController` маълумоти омории намоиширо ба template-и асосӣ медиҳад.
- `QuestionController` рӯйхати саволҳоро месозад, ҷустуҷӯро коркард мекунад ва саволи мушаххасро аз рӯи ID меёбад.
- `ReferenceController` саҳифаи маълумотномаро мекушояд.
- Дар ин машқи лексия маълумот аз массивҳои demo меояд; пайвасти MySQL талаб нашудааст. Номҳои асосии майдонҳои савол бо ҷадвали `questions` дар `database/schema.sql` мувофиқанд.

## 7. XSS ва escaping

Ҳангоми нишон додани маълумоти динамикӣ `htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')` истифода мешавад. Агар унвони савол ` <b>Test</b> ` бошад, браузер онро ҳамчун матн нишон медиҳад, на ҳамчун HTML. Барои матни чандсатрӣ аввал `htmlspecialchars()` ва баъд `nl2br()` истифода мешавад.

## 8. HTTP status code-ҳо барои санҷиш

| Дархост | Натиҷаи интизоршаванда | Сабаб |
|---|---:|---|
| `GET /` | 200 | Саҳифаи асосӣ вуҷуд дорад |
| `GET /questions` | 200 | Рӯйхати саволҳо вуҷуд дорад |
| `GET /questions/1` | 200 | Саволи ID 1 вуҷуд дорад |
| `GET /questions/999` | 404 | Савол бо чунин ID вуҷуд надорад |
| `GET /wrong-url` | 404 | Масир сабт нашудааст |
| `POST /questions` | 405 | Масир ҳаст, аммо танҳо GET иҷозат дорад |

## 9. Ҷавоби кӯтоҳ ба саволи «чаро Laravel не?»

Лоиҳаи асосӣ аллакай Laravel аст. Аммо ин супориши алоҳидаи лексияи HTTP/PHP/MVC мебошад ва талаб мекунад, ки донишҷӯ худаш front controller, router, controller ва view-ро фаҳмад ва нишон диҳад. Аз ин сабаб mini-MVC дар `day12-mvc/` ҷудо сохта шудааст; он ба Laravel-и асосӣ халал намерасонад.
