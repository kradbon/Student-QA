# Нақшаи мухтасари техникӣ: Student Q&A Platform — Вариант 24

## 1. Таъинот

Замимаи вебӣ барои саволу ҷавоби таълимӣ. Student саволро ба category мефиристад; дигар донишҷӯён ҷавоб медиҳанд ва овоз медиҳанд; муаллифи савол метавонад accepted answer-ро интихоб кунад. Moderator мундариҷа ва report-ро баррасӣ мекунад. Admin корбар, category, platform settings ва CSV-ро идора мекунад.

## 2. Корбарон

- **student** — ҳисоб месозад, профил ва саволи худро идора мекунад, ҷавоб медиҳад, овоз медиҳад ва ҷавоби беҳтаринро интихоб мекунад.
- **moderator** — савол, report ва мундариҷаро баррасӣ мекунад, history ва амалҳои account-и иҷозатдодаро мебинад.
- **admin** — корбар/role/status, category, setting ва export-ро идора мекунад.
- **Guest** — actor-и беруна барои дидани саҳифаҳои оммавӣ ва сабти ном; role-и база нест.

Дар Laravel role-ҳои student/moderator/admin мавҷуданд; тафсилот дар [таҳлили домен](../01-domain-analysis/01-domain-analysis.md).

## 3. Талаботи функсионалӣ

Афзалиятҳо бо MoSCoW дода шудаанд. Must/Should талаботанд, на ҳамаи онҳо далели пурраи санҷиши production мебошанд. FR-и нақшавӣ дар эзоҳ қайд шудааст.

| ID | Талабот | Нақш | MoSCoW |
|---|---|---|---|
| FR-01 | Сабти ном, login ва logout | Guest, user | Must |
| FR-02 | Таҳрири профил, password, locale ва avatar | user | Must |
| FR-03 | Эҷод ва идораи саволи худ; саволи нав pending мешавад | student | Must |
| FR-04 | Дидани саволҳои published/closed; ҷустуҷӯ ва category filter | ҳама | Must |
| FR-05 | Ҷобаҷогузории latest/popular/unanswered ва pagination | ҳама | Should |
| FR-06 | Ҷустуҷӯи саволи монанд пеш аз фиристодан | student | Should |
| FR-07 | Ҷавоб додан ба саволи published-и дигарон ва таҳрири ҷавоби худ | student | Must |
| FR-08 | Қабули ҷавоби беҳтарин ба саволи худ ва сабти reward | student | Must |
| FR-09 | Vote-и +1/-1 бо манъи self-vote ва тағйири reputation | student | Must |
| FR-10 | Upload-и хусусии файл ба савол/ҷавоб бо MIME/size validation | student | Must |
| FR-11 | Дидани notification ва read/read-all | user | Must |
| FR-12 | Интерфейси tj, ru, en | ҳама | Must |
| FR-13 | Approve/reject савол ва нигоҳ доштани moderation history | moderator, admin | Must |
| FR-14 | Идораи report ва hide/restore-и мундариҷа | moderator, admin | Must |
| FR-15 | Category CRUD | admin | Must |
| FR-16 | Идораи маълумоти user, role ва status | admin | Must |
| FR-17 | Platform settings ва upload limit | admin | Should |
| FR-18 | CSV-и users, questions, answers ва activity | admin | Must |
| FR-19 | Саҳифаҳои community ва reputation ranking | ҳама | Could |
| FR-20 | Идораи tag-и мустақил ва муносибати M:N ба questions | student/admin | Could; Not implemented — planned functionality дар migration-и Laravel |

Роҳҳои татбиқшаванда дар routes/web.php; модулҳо дар app/Http/Controllers ва app/Services. Route-и /tags ҳоло Category-ҳоро рӯйхат мекунад, на tags.

## 4. Ҳикояҳои корбарон

**US-01 (FR-03, FR-13).** Ҳамчун student мехоҳам саволро фиристам, то донишҷӯёни дигар ба он ҷавоб диҳанд.

- Дано: ман ҳамчун student ворид шудаам ва category фаъол ҳаст. Вақте: title, body ва category-ро бо қиматҳои дуруст фиристам. Пас: question сабт шуда, status-и он pending мешавад.
- Дано: title/body холӣ ё category ғайрифаъол аст. Вақте: form-ро мефиристам. Пас: validation error нишон дода мешавад ва question-и нодуруст сабт намешавад.

**US-02 (FR-07, FR-08).** Ҳамчун student мехоҳам ба саволи published-и дигарон ҷавоб диҳам ва ҷавоби муфидро қабул намоям.

- Дано: саволи published ба шахси дигар тааллуқ дорад. Вақте: ҷавоби мувофиқ мефиристам. Пас: ҷавоб сабт ва муаллифи савол огоҳ карда мешавад.
- Дано: ман муаллифи савол нестам. Вақте: кӯшиш мекунам ҷавобро accepted кунам. Пас: иҷозат рад мешавад.
- Дано: ман муаллифи савол ҳастам ва ҷавоб ба ҳамон савол тааллуқ дорад. Вақте: accepted-ро интихоб мекунам. Пас: accepted_answer_id нав ва reputation-и ҷавобдиҳанда мувофиқи қоида тағйир меёбад.

**US-03 (FR-09).** Ҳамчун иштирокчӣ мехоҳам ҷавобро vote кунам, то саҳми муфид арзёбӣ шавад.

- Дано: ҷавоб ба ман тааллуқ надорад. Вақте: +1 ё -1 интихоб мекунам. Пас: vote ва reputation-и соҳиб бо event сабт мешаванд.
- Дано: ман соҳиби ҷавоб ҳастам. Вақте: худам овоз медиҳам. Пас: self-vote рад мешавад.

**US-04 (FR-13, FR-14).** Ҳамчун moderator мехоҳам саволи pending ё report-ро баррасӣ кунам, то мундариҷаи номатлуб назорат шавад.

- Дано: ман moderator ҳастам. Вақте: саволро approve/reject мекунам. Пас: status ва moderation history нав мешаванд.
- Дано: ман student ҳастам. Вақте: ба moderation route дархост мефиристам. Пас: дастрасӣ рад мешавад.

**US-05 (FR-18).** Ҳамчун admin мехоҳам CSV-и ҳисоботро содир кунам.

- Дано: admin ворид шудааст. Вақте: яке аз users/questions/answers/activity-ро интихоб мекунам. Пас: файл бо Content-Type text/csv бармегардад.
- Дано: корбар admin нест. Вақте: export route-ро мехонад. Пас: дастрасӣ рад мешавад.

## 5. Талаботи ғайрифунксионалӣ

| ID | Талабот | Чӣ тавр санҷида мешавад |
|---|---|---|
| NFR-01 | Route-и маҳдуд танҳо барои нақши иҷозатдода дастрас бошад | Барои Student, Moderator ва Admin route access matrix-и санҷишӣ иҷро шавад. |
| NFR-02 | Замима танҳо MIME-и иҷозатдода ва то 3 файлро қабул кунад; default 5 MB, clamp 1–20 MB | Upload-и иҷозатшуда, 4 файл, MIME-и манъшуда ва файл аз ҳад калон санҷида шавад. |
| NFR-03 | Attachment аз storage-и хусусӣ танҳо баъди policy authorization дода шавад | Корбари дигар ба download муроҷиат карда, 403 гирад. |
| NFR-04 | CSV аз spreadsheet formula prefix муҳофизат шавад | Қиматҳои бо =, +, @, - оғозшаванда дар response prefix-и муҳофизатӣ гиранд. |
| NFR-05 | Unicode-и тоҷикӣ/русӣ бе вайроншавӣ нигоҳ дошта шавад | Матни кириллӣ save/read дар MySQL-и ҳадаф санҷида шавад; натиҷа ҳанӯз чен нашудааст. |
| NFR-06 | Performance-и /questions p95 ≤ 2s дар staging барои 100 дархости пайдарпай (ҳадафи пешниҳодӣ) | Борсанҷӣ бо версияи server, DB ва маълумоти тестӣ сабт шавад. |
| NFR-07 | Production secret ифшо нашавад ва debug хомӯш бошад | APP_DEBUG=false; хатои 500 stack trace ё .env нишон надиҳад. |
| NFR-08 | Runtime ба PHP ^8.3 ва Laravel ^13.17 мутобиқ бошад | composer install ва php artisan test дар runtime-и муайян сабт карда шавад. |

НФТ-05/06 ҳоло ҳамчун меъёр мебошанд, на натиҷаи ченшуда. MySQL production алоҳида бояд санҷида шавад; Feature tests SQLite memory истифода мекунанд.

## 6. MVP

MVP: FR-01–FR-04, FR-07–FR-14 ва FR-18. Яъне authentication, профил, савол/ҷавоб, search/category, accepted answer, vote/reputation, private attachment, notification, multilingual UI, moderation/report ва CSV. Ин рӯйхат scope-и қабулро муайян мекунад; иҷрои ҳар талабот бо санҷиш тасдиқ шавад.

## 7. Берун аз ҳудуд ё нақшавӣ

- Tag persistence ва M:N relationship; дар migration татбиқ нашудааст.
- Performance target то load-test натиҷаи тасдиқшуда надорад.
- Mail notification ва realtime/WebSocket дар route/service-и дидашуда исбот нашуданд.
- Sequence Diagram дар repository мавҷуд нест.
- Full production deployment, backup/restore ва browser compatibility report дар repository сабт нашудаанд.

## 8. Пайгирӣ «раванд → талабот»

| Раванд (таҳлили домен) | Талабот |
|---|---|
| Сабт, воридшавӣ ва профил | FR-01, FR-02 |
| Эҷод, ҷустуҷӯ ва баррасии савол | FR-03–FR-06, FR-13 |
| Ҷавоб, қабул ва овоз | FR-07–FR-09 |
| Замима ва огоҳинома | FR-10, FR-11 |
| Забон ва дастрасии ҷомеа | FR-12, FR-19, FR-20 |
| Report ва идораи мундариҷа | FR-14 |
| Идораи платформа ва ҳисобот | FR-15–FR-18 |

## 9. Манбаъҳои талабот ва татбиқ

- Таҳлили домен: [01-domain-analysis.md](../01-domain-analysis/01-domain-analysis.md).
- Муқоисаи аналогҳо: [02-comparison-matrix.md](../02-comparison-matrix/02-comparison-matrix.md).
- Татбиқ: [routes/web.php](../../routes/web.php), app/Http/Controllers, app/Models, app/Services.
- Схемаи runtime: [migration-и платформа](../../database/migrations/2026_11_09_220000_create_platform_tables.php).
- Санҷишҳои workflow: [PlatformWorkflowTest.php](../../tests/Feature/PlatformWorkflowTest.php).

