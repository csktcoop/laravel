# Shell variables inside Make are written as $$ (e.g., $$res)

# Default action: list available targets
.DEFAULT_GOAL := help

.PHONY: help install dev be be-dev be-serve be-queue be-log fe fe-dev fe-build lint be-test init rtk-up

# Capture everything starting from the 2nd word (composer, require, -D, etc.)
ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))

# This prevents Make from crying about "No rule to make target" when passing args
%:
	@:

BACKEND_DIR ?= laravel
BACKEND_HOST ?= 127.0.0.1
BACKEND_PORT ?= 8000
FRONTEND_DIR ?= vue3
FRONTEND_HOST ?= 127.0.0.1
FRONTEND_PORT ?= 5173

help:
	@echo "Available commands:"
	@echo "  make install       - Set up both Backend and Frontend"
	@echo "  make dev           - Run full application concurrently"
	@echo "  make be-dev        - Run backend services only"
	@echo "  make fe-dev        - Run frontend dev server only"
	@echo "  make fe-build      - Build frontend assets"
	@echo "  make lint          - Run linters on both directories"
	@echo "  make be-test       - Run Laravel tests"
	@echo "  make be-active     - Switch between backend dirs. e.g. laravel12 vs laravel13"
	@echo "  make init          - Install pre-requisite packages"

install:
	@echo "Setting up Laravel..."
	@cd $(BACKEND_DIR) && composer install
	@cd $(BACKEND_DIR) && php -r "file_exists('.env') || copy('.env.example', '.env');"
#   Pure bash read validation with 'N' as default on Enter
	@read -r -p "Run first-time keys/seed? [y/N] " res; \
	res=$${res:-N}; \
	if [ "$$res" = "y" ] || [ "$$res" = "Y" ]; then \
		cd $(BACKEND_DIR) && php artisan key:generate && php artisan migrate --seed; \
	else \
		echo "Skipping key-gen and seeding."; \
	fi
	@echo "Setting up Vue3..."
	@$(MAKE) fe-i
	@cd $(FRONTEND_DIR) && cp -n .env.example .env || true

dev:
	$(MAKE) -j2 be-serve fe-dev
dev-full:
	$(MAKE) -j4 be-serve be-queue be-log fe-dev

be:
	@cd $(BACKEND_DIR) && $(ARGS)

be-dev:
	$(MAKE) -j3 be-serve be-queue be-log

be-serve:
	@cd $(BACKEND_DIR) && \
	if curl -fsS --max-time 2 http://$(BACKEND_HOST):$(BACKEND_PORT)/ >/dev/null 2>&1; then \
		PID=$$(lsof -t -i :$(BACKEND_PORT)); \
		echo "Laravel already running on http://$(BACKEND_HOST):$(BACKEND_PORT); Stop it with \"kill $$PID\""; \
	else \
		php artisan serve --host=$(BACKEND_HOST) --port=$(BACKEND_PORT); \
	fi
be-queue:
	@cd $(BACKEND_DIR) && php artisan queue:work --sleep=3 --tries=1 --max-time=3600
be-log:
	@cd $(BACKEND_DIR) && php artisan pail --timeout=0

be-test:
	@cd $(BACKEND_DIR) && php artisan config:clear --ansi
	@cd $(BACKEND_DIR) && ./vendor/bin/pint --parallel --test
	@cd $(BACKEND_DIR) && php artisan test

be-active:
	ln -sfn $(ARGS) laravel

fe:
	@cd $(FRONTEND_DIR) && $(ARGS)

fe-dev:
#   DEBUG=vite:* bun dev
	@cd $(FRONTEND_DIR) && \
	if curl -fsS --max-time 2 http://$(FRONTEND_HOST):$(FRONTEND_PORT)/ >/dev/null 2>&1; then \
		PID=$$(lsof -t -i :$(FRONTEND_PORT)); \
		echo "Vite already running on http://$(FRONTEND_HOST):$(FRONTEND_PORT); Stop it with \"kill $$PID\""; \
	else \
		bun run dev --host $(FRONTEND_HOST) --port $(FRONTEND_PORT); \
	fi

fe-build:
	@cd $(FRONTEND_DIR) && bun build

fe-i:
	@cd $(FRONTEND_DIR) && pnpm install

lint:
	@echo "Linting Backend..."
	@cd $(BACKEND_DIR) && ./vendor/bin/pint --parallel
	@echo "Linting Frontend..."
	@cd $(FRONTEND_DIR) && bun lint

init:
	@curl --proto '=https' --tlsv1.3 -sSf -L https://install.determinate.systems/nix | sh -s -- install
	@nix --extra-experimental-features "nix-command flakes" profile add nixpkgs#php83 nixpkgs#nodejs_24 nixpkgs#pnpm
	@pnpm setup
	@curl -fsSL https://bun.com/install | bash
	@source ~/.bashrc
