# Proxy

### Развернуть проект
```
make dev
```

### консоль проекта (php)
docker exec -it php bash

### URL's:
```
// Index page
http://localhost:8080
// check page
http://localhost:8080/task/{taskId}
```

### HealthCheck
```
http://localhost:8080/health/

Добавил проверку
необходимых расширений
версии php
доступности базы данных
доступности сервиса geo ip
```

### Swager
```
http://localhost:8080/doc
```

### Запуск команды для проверки проксей
```
symfony console app:proxy-check
```