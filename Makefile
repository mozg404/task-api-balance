init: copy-env docker-down-clear docker-pull docker-build docker-up composer-install generate-key refresh
up: docker-up
down: docker-down
restart: down up
refresh: migrate-fresh-seed

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

composer-install:
	docker compose exec php-cli composer install

composer-update:
	docker compose exec php-cli composer update --no-interaction --prefer-dist

copy-env:
	cp .env.example .env

generate-key:
	docker compose exec php-cli php artisan key:generate

clear-cache:
	docker compose exec php-cli php artisan config:clear
	docker compose exec php-cli php artisan cache:clear
	docker compose exec php-cli php artisan route:clear

cli:
	docker compose exec php-cli bash

migrate-fresh-seed:
	docker compose exec php-cli php artisan migrate:fresh --seed

test:
	docker compose exec php-cli php artisan test

generate-phpdoc:
	docker compose exec php-cli php artisan ide-helper:generate
	docker compose exec php-cli php artisan ide-helper:models -RW
