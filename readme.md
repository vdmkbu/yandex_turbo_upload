#### Сервис для загрузки новостей сайта lentachel.ru в Yandex Turbo

документация API https://yandex.ru/dev/turbo/doc/api/about-docpage/

##### Сценарии использования
1. Пакетная загрузка старых новостей в индекс Yandex Turbo
2. Обновление уже загруженных в индекс Yandex Turbo новостей
3. Удаление новостей из индекса


##### авторизационный HTTP-заголовок
```http request
Header X-API-TOKEN
```

##### Примеры
##### загрузка в Yandex Turbo:
```http request
POST /upload
```

параметры:
```text
messages[] - список ID новостей для добавления в индекс
prod - 1|0 - режим production/dev
batch - 1|0 - передаем, если это пакетная загрузка (используется для загрузки старых новостей)
```

##### удаление из Yandex Turbo:
```http request
POST /delete
```

параметры:
```text
messages[] - список ID новостей для удаления из индекса
prod - 1|0 - режим production/dev
```