DEV_CONFIG = ./dev-ops/ci/config
BIN = ./vendor/bin
SAIL = $(BIN)/sail
ASSETS_BUILD = npm install && npm run prod;

## —— 🐳 The -docker Makefile 🐳 ——————————————————————————————————
help:  ## Show available commands
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

# Docker 🐳 --------------------------------------------------------------------- #
up: ## Start Laravel Sail
	$(SAIL) up -d

down: ## Stop Laravel Sail
	$(SAIL) down

rebuild: ## Rebuild Docker containers
	make down;
	$(SAIL) build --no-cache;
	make restart;

restart: ## Restart Docker containers
	make down; make up;

# App ------------------------------------------------------------------------ #
local-setup: ## Setup local environment
	$(ASSETS_BUILD)
	$(SAIL) artisan key:generate;
	$(SAIL) artisan storage:link;
	make migrate;

migrate: ## Run database migrations
	$(SAIL) artisan migrate:fresh --seed;
	$(SAIL) artisan db:seed

clean: ## Clear Laravel cache
	$(SAIL) artisan view:clear;
	$(SAIL) artisan config:clear;
	$(SAIL) artisan optimize:clear;
	$(SAIL) artisan route:clear;

test: ## Run application tests
	$(SAIL) artisan test;

# Queue Management
horizon:  ## Start Laravel Horizon
	$(SAIL) artisan horizon

horizon-restart:  ## Restart Laravel Horizon
	$(SAIL) artisan horizon:terminate
	make horizon