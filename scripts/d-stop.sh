#! /bin/bash

# Source .env file if it exists
if [ -f .env ]; then
    # Export all variables from .env
    export $(cat .env | grep -v '^#' | xargs)
fi

APP_ENV=${APP_ENV:-development}

# Check APP_ENV and use appropriate compose file
if [ -z "$APP_ENV" ]; then
	echo "\033[31mERROR: APP_ENV environment variable is not set.\033[0m"
	exit 1
elif [ "$APP_ENV" = "development" ]; then
	if [ ! -f compose.development.yml ]; then
		echo "\033[31mERROR: compose.development.yml not found.\033[0m"
		exit 1
	fi

	docker compose -f compose.development.yml stop
elif [ "$APP_ENV" = "production" ]; then
	if [ ! -f compose.production.yml ]; then
		echo "\033[31mERROR: compose.production.yml not found.\033[0m"
		exit 1
	fi
	docker compose -f compose.production.yml stop
else
	echo "\033[31mERROR: Invalid APP_ENV value. Must be 'development' or 'production'.\033[0m"
	exit 1
fi

