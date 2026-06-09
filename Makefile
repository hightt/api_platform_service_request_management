init:
	docker compose -f docker/docker-compose.yml --env-file .env up -d --build --force-recreate
	docker compose -f docker/docker-compose.yml --env-file .env exec -T php composer install
	docker compose -f docker/docker-compose.yml --env-file .env exec -T php php bin/console doctrine:database:create --if-not-exists --no-interaction
	docker compose -f docker/docker-compose.yml --env-file .env exec -T php php bin/console doctrine:migrations:migrate --no-interaction
	docker compose -f docker/docker-compose.yml --env-file .env exec -T php php bin/console doctrine:fixtures:load --no-interaction
	
	# Automatyczne generowanie JWT passphrase i kluczy
	@if [ -z "$$(grep 'JWT_PASSPHRASE=' .env | cut -d '=' -f2)" ]; then \
		PASS=$$(openssl rand -base64 32); \
		echo "JWT_PASSPHRASE=$$PASS" >> .env; \
	fi
	docker compose -f docker/docker-compose.yml --env-file .env exec -T php mkdir -p config/jwt
	docker compose -f docker/docker-compose.yml --env-file .env exec -T php sh -c 'export JWT_PASSPHRASE=$$(grep "JWT_PASSPHRASE=" .env | cut -d "=" -f2); bin/console lexik:jwt:generate-keypair --overwrite'

stan:
	docker compose -f docker/docker-compose.yml exec -T php vendor/bin/phpstan analyse

stop:
	docker compose -f docker/docker-compose.yml down