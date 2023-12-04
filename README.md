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
http://localhost:8080/check/{taskId}
```

### HealthCheck
```
http://localhost/dp/v1/monitor/health/

Добавил проверку
необходимых расширений
версии php
доступности базы данных
доступности сервиса geo ip
```