DOCKER_COMPOSE_FILE = docker-compose.yml
PHP_CONTAINER = php
USER_ID = 0
USER_GROUP = 0
DOCKER_COMPOSE = docker-compose -f $(DOCKER_COMPOSE_FILE) exec -T --user $(USER_ID):$(USER_GROUP)

dev: down up composer-install env-dev fresh-db migrate

docker-build: build-base-image
	docker-compose -f $(DOCKER_COMPOSE_FILE) build

up:
	docker-compose -f $(DOCKER_COMPOSE_FILE) up -d

down:
	docker-compose -f $(DOCKER_COMPOSE_FILE) down

composer-install:
	$(DOCKER_COMPOSE) php composer install

env-dev:
	$(DOCKER_COMPOSE) php composer symfony:dump-env dev

fresh-db: drop-db create-db

drop-db:
	$(DOCKER_COMPOSE) php bin/console doctrine:database:drop --force --if-exists

create-db:
	$(DOCKER_COMPOSE) php bin/console doctrine:database:create

migrate:
	$(DOCKER_COMPOSE) php bin/console --no-interaction doctrine:migrations:migrate
