#! /bin/bash

APP_ENV=${APP_ENV:-development}

# Check and validate APP_ENV first
if [ "$APP_ENV" = "development" ]; then
	if [ ! -f compose.development.yml ]; then
		echo "\033[31mERROR: compose.development.yml not found. Run 'make d-init' first.\033[0m"
		exit 1
	fi
	echo "Starting development environment..."
	docker compose -f compose.development.yml up -d
elif [ "$APP_ENV" = "production" ]; then
	if [ ! -f compose.production.yml ]; then
		echo "\033[31mERROR: compose.production.yml not found. Run 'make d-init' first.\033[0m"
		exit 1
	fi
	echo "Starting production environment..."
	docker compose -f compose.production.yml up -d
else
	echo "\033[31mERROR: APP_ENV must be set to either 'development' or 'production'\033[0m"
	exit 1
fi