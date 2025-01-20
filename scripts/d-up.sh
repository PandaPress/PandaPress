#! /bin/bash

if [ ! -f compose.yml ]; then
	echo "\033[31mERROR: compose.yml not found. Run 'make d-init' first.\033[0m"
	exit 1
fi

# Source the .env file if it exists
if [ -f .env ]; then
	export $(cat .env | grep -v '^#' | xargs)
fi

if [ "$APP_ENV" = "development" ]; then
	docker compose up -d
elif [ "$APP_ENV" = "production" ]; then
	docker compose -f compose.yml -f compose.production.yml up
else
	echo "\033[31mERROR: APP_ENV must be set to either 'development' or 'production'\033[0m"
	exit 1
fi