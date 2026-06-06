init:
	docker compose -f docker/docker-compose.yml --env-file .env up -d --build --force-recreate
	docker compose -f docker/docker-compose.yml --env-file .env exec -T php composer install
	docker compose -f docker/docker-compose.yml --env-file .env exec -T php php bin/console doctrine:database:create --if-not-exists --no-interaction
	docker compose -f docker/docker-compose.yml --env-file .env exec -T php php bin/console doctrine:migrations:migrate --no-interaction
	docker compose -f docker/docker-compose.yml --env-file .env exec -T php php bin/console doctrine:fixtures:load --no-interaction

stan:
	docker compose -f docker/docker-compose.yml exec -T php vendor/bin/phpstan analyse

stop:
	docker compose -f docker/docker-compose.yml down