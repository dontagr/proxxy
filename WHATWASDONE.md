# Что было сделано:

Приложение умеет:
1) Есть главный экран с текстовым инпутом куда надо разместить список проксей в указанном формате.
2) По кнопке идет ajax запрос на url POST /task где идет валидация и сохранения информации в базу в ответ приходит или сообщение об ошибке или id task
3) Если все ок мы делаем переход на GET /task/taskID где отслеживаем состояние проверки проксей
4) Далее у нас есть консольная команда

symfony console app:proxy-check

Которая проверяет прокси и может быть горизонтально масштабирована


Какие технологии применял, что вообще делал:
1) Поднял докер php + mysql + nginx
2) Взял последнюю версию symfony 6.4 (для меня это новый опыт)
3) Для отрисовки фронта использовал twig + bootstrap + jquery написал класс app/public/js/ProxyFormService.js для работы с ручкой создания задачи
4) На стороне бэка использовал
   "guzzlehttp/guzzle": - обертка над курлом
   "jms/serializer-bundle": - сериализатор полноценно все валидации и группы я не прописывал поскольку времени не так много было
   "liip/monitor-bundle": - добавил мониторинг "жизни" минимальный для определения, что все завилось и есть доступы требуемые для работы
   "nelmio/api-doc-bundle": - генератор openapi + страницы swager вообще хотелось бы сделать фронт отдельным приложением но увы время
   Сущьности описал крайне минималистично
   Добавил обработчики событий на респонс кернел для удобства управления форматами данных на выходе
   Добавил paramconverter дабы общаться с портами/адапторами по средствам DTO и конвертацию и валидацию скрыть в десиреализаторе, объявляется данная конвертацию посредствам анотаций перед контроллером

Что не получилось:
нормально реализовать скрипт проверки прокси, он работает частично и его можно было бы распаралелить еще сильнее
нормализовать базу и оптимизировать базу, фактически я ее накидал на скорую руку и так и не дошел до нее
не получилось подцепить кафку, я потратил немного времени прикрутил кафку и интервейс, скомпелировать расширение для php (через pecl)
описал сообщения + сериалайзер, использовал koco/messenger-kafka для транспорта в общем фактически полностью все сделал, но сталкнулся с тем что вендер не работает с данной версией симфони корректно, вернее с ним конфликтует доктрина, не стал тратить времени, но реализация была бы не плохая ((
консьюмер очереди я бы подцепил на супервизор, ну уже в другой раз)

Все же я надеюсь что в общем этого достаточно для оценки, фронт и докер конечно не самые мои сильные стороны )))