.PHONY: \
	vendors \
	data_dirs \
	init-dbs \
	init-db-app \
	tests \
	lint \
	deploy-prod \
	deploy-preprod \
	docker-bash \
	js-routes

.DEFAULT: vendors

CURRENT_UID=$(shell id -u)
CURRENT_GID=$(shell id -g)
-DI_JENKINS_CURL_OPTIONS=

-include .makerc

init:
	docker-compose run --rm cli /bin/bash -l -c "make vendors"
	docker-compose run --rm cli /bin/bash -l -c "make js-routes"
	docker-compose run --rm cli /bin/bash -l -c "grunt"
	docker-compose run --rm cli /bin/bash -l -c "php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration"

# vendors
vendors: node_modules vendor

composer.phar:
	$(eval EXPECTED_SIGNATURE = "$(shell wget -q -O - https://composer.github.io/installer.sig)")
	$(eval ACTUAL_SIGNATURE = "$(shell php -r "copy('https://getcomposer.org/installer', 'composer-setup.php'); echo hash_file('SHA384', 'composer-setup.php');")")
	@if [ "$(EXPECTED_SIGNATURE)" != "$(ACTUAL_SIGNATURE)" ]; then echo "Invalid signature"; exit 1; fi
	php composer-setup.php --version=1.10.16
	rm composer-setup.php


vendor: composer.phar config/services.yaml
	php$(CI_PHP_VERSION) composer.phar install

node_modules:
	npm install

# app/config/parameters.yml:
# 	cp app/config/parameters.yml.dist app/config/parameters.yml

# config/services.yaml:
# 	cp config/services.yaml.dist config/services.yaml

# docker
docker-up: var/log/.docker-build data_dirs
	COMPOSE_HTTP_TIMEOUT=120 docker-compose up

docker-build: var/log/.docker-build

var/log/.docker-build: docker-compose.yml docker-compose.override.yml $(shell find docker/conf -type f)
	docker-compose rm --force
	CURRENT_UID=$(CURRENT_UID) CURRENT_GID=$(CURRENT_GID) docker-compose build
	touch var/log/.docker-build

data_dirs: \
	docker/data \
	docker/data/composer \
	docker/data/mysql \

docker/data:
	mkdir -p docker/data

docker/data/composer: docker/data
	mkdir -p docker/data/composer

docker/data/mysql: docker/data
	mkdir -p docker/data/mysql

docker-compose.override.yml:
	cp docker-compose.override.yml-dist docker-compose.override.yml

# lance un bash sur l'env de dev
docker-bash:
	docker-compose run --user localUser --rm cli /bin/bash -l

# hooks
hooks: .git/hooks/pre-commit .git/hooks/post-checkout

hook-pre-commit:
	docker-compose run --rm cli make tests lint

hook-post-checkout:
	docker-compose run --rm cli make vendor

.git/hooks/pre-commit: Makefile
	echo "#!/bin/sh" > .git/hooks/pre-commit
	echo "make hook-pre-commit" >> .git/hooks/pre-commit
	chmod +x .git/hooks/pre-commit

.git/hooks/post-checkout: Makefile
	echo "#!/bin/sh" > .git/hooks/post-checkout
	echo "make hook-post-checkout" >> .git/hooks/post-checkout
	chmod +x .git/hooks/post-checkout


# tests
tests:
	php$(CI_PHP_VERSION) ./bin/atoum

lint:
	php$(CI_PHP_VERSION) ./bin/phpcs


# déploiements
define build_jenkins_job
	@curl $(DI_JENKINS_CURL_OPTIONS) -X POST "https://$(JENKINS_USER):$(JENKINS_API_KEY)@jenkins.pp.decitre-interactive.fr/job/$(1)/build?delay=0sec"
endef

deploy-preprod:
	$(call build_jenkins_job,gestion-deploy_preprod)

deploy-prod:
	$(call build_jenkins_job,gestion-deploy_prod)

composer-lock: composer.phar
	php$(CI_PHP_VERSION) composer.phar install 2>&1 | grep "not up to date with" | wc -l | grep "0"

local-php-security-checker:
	curl --location -o local-php-security-checker https://github.com/fabpot/local-php-security-checker/releases/download/v1.0.0/local-php-security-checker_1.0.0_linux_amd64 && chmod +x local-php-security-checker

composer-security: local-php-security-checker
	./local-php-security-checker --path=./composer.lock

phpstan:
	php$(CI_PHP_VERSION) bin/phpstan analyze --memory-limit=1G $(PHPSTAN_OPTIONS)

clean:
	rm -rf node_modules
	rm -rf vendor
	rm -f app/config/parameters.yml
	rm -f docker-compose.override.yml

force-build:
	CURRENT_UID=$(CURRENT_UID) CURRENT_GID=$(CURRENT_GID) docker-compose build --pull

js-routes:
	php bin/console fos:js-routing:dump --target="public/assets/js/fos_js_routes.js"
