# Модели рафтор: Student Q&A Platform — Вариант 24

## 1. Мақсад

Модели рафтор actor-ҳо ва ҷараёни муҳимтарини платформаро нишон медиҳад: Student савол мефиристад, Moderator онро баррасӣ мекунад, дигар донишҷӯ ҷавоб медиҳад ва муаллиф accepted answer-ро интихоб мекунад. Ҳуҷҷати reference барои Day 4 папкаи 04-behavior дорад ва диаграммаҳои Use Case, Activity ва Sequence-ро ҳамчун PlantUML/PNG нигоҳ медорад; дар repository-и Variant 24 ду диаграммаи рафторӣ мавҷуданд, аммо Sequence Diagram нест.

## 2. Actor ва use case

| Actor | Use case |
|---|---|
| Guest | Дидани саволҳои умумӣ, ҷустуҷӯ, бақайдгирӣ ва login. |
| Student | Савол сохтан/идора кардан, ҷавоб додан, овоз додан, accepted answer интихоб кардан, attachment ва report фиристодан. |
| Moderator | Баррасии навбат, муҳтаво ва report; дидани moderation history. |
| Admin | Идораи moderation, корбар, category, settings ва CSV. |

Use Case-и мавҷуда: [docs/04/use-case.puml](../04/use-case.puml). Дар он Guest, Student, Moderator ва Admin нишон дода шудаанд. Admin дар диаграмма аз Moderator мерос мегирад; дар route-и Laravel низ admin ба гурӯҳи moderator иҷозат дорад, вале admin route-ҳои алоҳида дорад.

## 3. Activity: ҷараёни lifecycle-и савол

Activity Diagram-и мавҷуда [docs/04/activity-question-lifecycle.puml](../04/activity-question-lifecycle.puml) чунин қадамҳоро нишон медиҳад:

1. Student title/body-ро ворид мекунад ва server validation мекунад.
2. Система саволҳои монандро пешниҳод карда метавонад.
3. Саволи дуруст pending сабт шуда, ба навбати Moderator меравад.
4. Moderator approve ё reject мекунад.
5. Пас аз approve донишҷӯён ҷавоб медиҳанд; муаллифи савол як ҷавобро қабул карда метавонад.
6. VotingService/ReputationService vote ва reputation-ро коркард мекунанд.
7. Муаллиф метавонад саволро пӯшад.

Диаграмма ба workflow-и controller/service мувофиқ аст, аммо ҳар field/таблицаи дар диаграмма зикршуда бояд бо migration-и ҷорӣ санҷида шавад; schema-и SQL-и Lab 6 дигар аст.

## 4. Sequence Diagram

Дар reference Day 4 сеюм диаграмма Sequence аст. Дар repository-и Student Q&A Platform чунин диаграмма вуҷуд надорад ва ин ҳуҷҷат онро ҳамчун мавҷуда муаррифӣ намекунад. Барои анҷом додани deliverable-и пурраи Day 4 бояд sequence-и воқеии scenario (масалан, қабул кардани ҷавоб) аз routes/web.php, AnswerController, ReputationService, Eloquent ва database таҳия ва бо code санҷида шавад.

## 5. Мувофиқат ва нуқтаҳои санҷиш

| Санҷиш | Далели code ё ҳуҷҷат |
|---|---|
| Нақшҳо | routes/web.php, RequireRole middleware ва User::isRole() се role-и student/moderator/admin доранд. |
| Саволи pending | QuestionController@store status-и pending сабт мекунад. |
| Review | ModerationController ва moderator routes қарорҳоро коркард мекунанд. |
| Ҷавоб/accepted answer | AnswerController ва AnswerPolicy амалҳои ҷавоб ва қабулро маҳдуд мекунанд. |
| Vote/reputation | VotingService ва ReputationService transaction/event key истифода мебаранд. |
| Sequence | Файли Sequence Diagram ёфт нашуд; илова кардан лозим аст. |

## 6. Файлҳои лоиҳа

- Use Case: [docs/04/use-case.puml](../04/use-case.puml)
- Activity: [docs/04/activity-question-lifecycle.puml](../04/activity-question-lifecycle.puml)
- Тасвири Activity: [docs/04/student_qa_activity_diagram.png](../04/student_qa_activity_diagram.png)
- Route: [routes/web.php](../../routes/web.php)
- Feature tests: [tests/Feature/PlatformWorkflowTest.php](../../tests/Feature/PlatformWorkflowTest.php)

## Хулоса

Use Case ва Activity Diagram-и Variant 24 дар docs/04 мавҷуданд. Модели рафтор нақшҳои student, moderator ва admin ва lifecycle-и саволро фаро мегирад. Sequence Diagram, ки дар reference барои Day 4 пешбинӣ шудааст, дар repository-и лоиҳа ҳанӯз вуҷуд надорад ва бояд пеш аз эълони иҷрои пурраи ин deliverable сохта шавад.

