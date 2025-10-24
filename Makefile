init: docker-down-clear docker-pull docker-build docker-up
up: docker-up
down: docker-down
restart: down up
refresh: migrate-fresh

docker-up:
	docker compose up -d

docker-down:
	docker compose down --remove-orphans

docker-down-clear:
	docker compose down -v --remove-orphans

docker-pull:
	docker compose pull

docker-build:
	docker compose build --pull

cli:
	docker compose exec php-cli bash

migrate-fresh:
	docker compose exec php-cli php artisan migrate:fresh --seed

test:
	docker compose exec php-cli php artisan test

generate-phpdoc:
	docker compose exec php-cli php artisan ide-helper:generate
	docker compose exec php-cli php artisan ide-helper:models -RW
