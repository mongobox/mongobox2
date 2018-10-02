FIG=docker-compose
RUN_WEB=$(FIG) run --rm web-php7

EXEC_WEB=$(FIG) exec web
EXEC_NODEJS=$(FIG) exec nodejs
EXEC_MYSQL=$(FIG) exec mysql

SITE_PATH=/var/www/mongobox

CONSOLE_SITE="$(SITE_PATH)/bin/console"

DATE=`date +%Y%m%d`
TARGET_FILE_PATH="dump_$(SITE_PROD_DB_NAME)_$(DATE).sql.gz"

.DEFAULT_GOAL := help
.PHONY: help

help:
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//'

##
## Project setup
##---------------------------------------------------------------------------

docker-init:          ## Init and start docker for relight front
docker-init: docker-cmd-up

docker-start:         ## Start docker
docker-start: docker-cmd-start

docker-stop:          ## Stop docker
docker-stop: docker-cmd-stop

docker-restart:       ## Restart project
docker-restart: docker-cmd-stop docker-cmd-start

apache-restart:       ## Restart apache of web container
apache-restart:
	$(EXEC_WEB) service apache2 restart

##
## Shell for each docker container
##---------------------------------------------------------------------------
web-shell:            ## Shell of  php 7.1
web-shell:
	-$(EXEC_WEB) /bin/bash

nodejs-shell:         ## Shell of nodejs container
nodejs-shell:
	$(EXEC_NODEJS) /bin/bash

mysql-shell:          ## Shell of mysql container
mysql-shell:
	$(EXEC_MYSQL) /bin/bash

##
## Symfony
##---------------------------------------------------------------------------

sf-list:            ## Get command list
sf-list:
	$(EXEC_WEB) $(CONSOLE_SITE)

sf-cc:              ## Clear the cache in dev env
sf-cc:
	$(EXEC_WEB) /bin/bash -c "cd $(SITE_PATH) && php -d memory_limit=-1 bin/console cache:clear --no-warmup"
    $(EXEC_WEB) /bin/bash -c "cd $(SITE_PATH) && php -d memory_limit=-1 bin/console cache:warmup"

sf-db-migrate:      ## doctrine:migrations:migrate
sf-db-migrate:
	$(EXEC_WEB) $(CONSOLE_SITE) doctrine:migrations:migrate

sf-db-diff:         ## doctrine:migrations:diff
sf-db-diff:
	$(EXEC_WEB) $(CONSOLE_SITE) doctrine:migrations:diff

composer-install:   ## Install vendor
composer-install:
	$(EXEC_WEB) /bin/bash -c "cd $(SITE_PATH) && php -d memory_limit=-1 /usr/local/bin/composer install -o"

composer-update:    ## Update vendor
composer-update:
	$(EXEC_WEB) /bin/bash -c "cd $(SITE_PATH) && php -d memory_limit=-1 /usr/local/bin/composer update -o"

##
## Assets
##---------------------------------------------------------------------------
yarn-install:        ## Install node_modules
yarn-install:
	$(EXEC_NODEJS) /bin/bash -c "cd $(SITE_PATH) && yarn install"

assets-compile-dev:         ## Compile assets for dev. Ex: make assets-compile-dev [CONFIG_NAME=dimipro|pulsat]
assets-compile-dev:
	$(EXEC_NODEJS) /bin/bash -c "cd $(SITE_PATH) && yarn run encore dev"

assets-compile-dev-watch:   ## Compile assets for dev with watch mode
assets-compile-dev-watch:
	$(EXEC_NODEJS) /bin/bash -c "cd $(SITE_PATH) && yarn run encore dev --watch"

assets-compile-prod:        ## Compile assets for prod
assets-compile-prod:
	$(EXEC_NODEJS) /bin/bash -c "cd $(SITE_PATH) && yarn run encore production"


##
## Database
##---------------------------------------------------------------------------
db-dev-update:      ## Import dump (gzip file) on dev database. Ex : make db-dev-update DUMP_FILE=dump_mongobox_20171208.sql.gz
db-dev-update:
	-@echo DROP DATABASE "$(SITE_DEV_DB_NAME)" on DEV
	-@mysql -u $(SITE_DEV_DB_USER) --port=$(SITE_DEV_DB_PORT) -h $(SITE_DEV_DB_HOST) -p --execute="DROP DATABASE IF EXISTS $(SITE_DEV_DB_NAME);"
	-@echo CREATE DATABASE "$(SITE_DEV_DB_NAME)" on DEV
	-@mysql -u $(SITE_DEV_DB_USER) --port=$(SITE_DEV_DB_PORT) -h $(SITE_DEV_DB_HOST) -p --execute="CREATE SCHEMA $(SITE_DEV_DB_NAME) DEFAULT CHARACTER SET utf8 ;"
	-@echo IMPORT DUMP "$(DUMP_FILE)" TO "$(SITE_DEV_DB_NAME)" on DEV
	-@zcat $(DUMP_FILE) | mysql -u $(SITE_DEV_DB_USER) --port=$(SITE_DEV_DB_PORT) -h $(SITE_DEV_DB_HOST) -p $(SITE_DEV_DB_NAME)

##
## Tests
##---------------------------------------------------------------------------
tests-init:         ## Init Database, fixtures for the PHP unit tests
tests-init:
	-$(EXEC_WEB) $(CONSOLE_SITE) doctrine:database:drop --force --env=test --connection=default
	-$(EXEC_WEB) $(CONSOLE_SITE) doctrine:database:create --env=test --connection=default
	-$(EXEC_WEB) $(CONSOLE_SITE) doctrine:schema:update --force --env=test --em=default
	-$(EXEC_WEB) $(CONSOLE_SITE) doctrine:fixtures:load --fixtures=$(SITE_PATH)/tests/DataFixtures --env=test --purge-with-truncate -n

tests-ut:           ## Run the phpunit on unit tests and exclude functional tests
tests-ut:
	$(EXEC_WEB) /bin/bash -c "cd $(SITE_PATH) && php -d memory_limit=-1 vendor/bin/phpunit --exclude-group functional"

tests-functional:   ## Run the phpunit on functionnal tests
tests-functional:
	$(EXEC_WEB) /bin/bash -c "cd $(SITE_PATH) && php -d memory_limit=-1 vendor/bin/phpunit --group functional"

##
## Tools
##---------------------------------------------------------------------------
#phpstan:		## PHPStan - PHP Static Analysis Tool
#phpstan:
#	$(EXEC_WEB) /bin/bash -c "cd $(SITE_PATH) && php -d memory_limit=-1 vendor/bin/phpstan.phar analyse src tests"

php-cs-fixer-dry-run:   ## PHP Code Style Fixer in dry-run mode
php-cs-fixer-dry-run:
	 $(EXEC_WEB) /bin/bash -c "cd $(SITE_PATH) && vendor/bin/php-cs-fixer fix --config=.php_cs -v --dry-run --allow-risky=yes"

php-cs-fixer:           ## PHP Code Style Fixer
php-cs-fixer:
	 $(EXEC_WEB) /bin/bash -c "cd $(SITE_PATH) && vendor/bin/php-cs-fixer fix --config=.php_cs -v --allow-risky=yes"

# Docker commands
build:
	$(FIG) build

docker-cmd-up:
	$(FIG) up -d && $(FIG) logs

docker-cmd-start:
	$(FIG) start

docker-cmd-stop:
	$(FIG) stop
