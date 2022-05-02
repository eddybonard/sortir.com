COMPOSE=docker-compose
UID=$(shell id -u)
GID=$(shell id -g)
RUN=$(COMPOSE) run --rm php
EXEC=$(COMPOSE) exec --user $(UID) php
CONSOLE=bin/console

.DEFAULT_GOAL := help

help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ——————————————————————————————————————————————————————————————
start: ## Start the containers
	$(COMPOSE) up -d

start-build: ## Build and start the containers
	$(COMPOSE) up -d --build

stop: ## Stop the containers
	$(COMPOSE) down

restart: stop start ## Restart the containers

php-sh: ## Connect to the PHP FPM container
	@echo -----------------------Enter contener PHP-------------------------
	$(COMPOSE) exec php sh

## —— Database 🗃️ ————————————————————————————————————————————————————————————
db-update: ## Update database schema
	$(EXEC) $(CONSOLE) doctrine:schema:update --force

db-reset: ## Recreate database dev
	@echo ----------------- RESET DEV DB ------------------
	$(EXEC) $(CONSOLE) --env=dev doctrine:database:drop --force --if-exists
	$(EXEC) $(CONSOLE) --env=dev doctrine:database:create --if-not-exists
	$(EXEC) $(CONSOLE) --env=dev doctrine:schema:create -n

db-test: ## Recreate database test
	@echo ----------------- RESET TEST DB ------------------
	$(EXEC) $(CONSOLE) --env=test doctrine:database:drop --force --if-exists
	$(EXEC) $(CONSOLE) --env=test doctrine:database:create --if-not-exists
	$(EXEC) $(CONSOLE) --env=test doctrine:schema:create -n

## —— Tools 🛠️️ ———————————————————————————————————————————————————————————————
encode-password: ## Encore password
	$(EXEC) $(CONSOLE) security:encode-password

## —— Symfony 🎵 ————————————————————————————————————————————————————————————
cc: ## Cache clear
	@echo -----------------------Emptying symfony cache-------------------------
	$(EXEC) $(CONSOLE) cache:clear
router: ## Debug router
	@echo -----------------------Emptying symfony cache-------------------------
	$(EXEC) $(CONSOLE) debug:router

## —— Tests 📋 ———————————————————————————————————————————————————————————————
clean-tests: ## Clean output folder
	@echo -------------------- clean tests --------------------
	$(EXEC) vendor/bin/codecept clean

run-test-api: ## Run functional tests
	@echo ----------------- launch api tests ------------------

run-test-unit: ## Run unit tests
	@echo ----------------- launch unit tests ------------------
	$(EXEC) export SYMFONY_DEPRECATIONS_HELPER=weak
	$(EXEC) vendor/bin/codecept run unit --quiet

test: clean-tests db-test run-test-api run-test-unit ## Create test database and run all tests

## —— PHPCS 💇 ———————————————————————————————————————————————————————————————
run-phpcs: ## Run PHP CodeSniffer
	@echo ----------------- launch phpcs ------------------
	$(EXEC) vendor/bin/phpcs src/ tests/

run-phpcs-files: ## Run PHP CodeSniffer by files
	# Example : make run-phpcs-files FILES="path/to/class/ClassController.php path/to/class/ClassTwoController.php"
	$(EXEC) vendor/bin/phpcs $(FILES)
