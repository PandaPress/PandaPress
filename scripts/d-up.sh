#! /bin/bash

# Source .env file if it exists
if [ -f .env ]; then
    # Export all variables from .env
    export $(cat .env | grep -v '^#' | xargs)
fi

if [ "$APP_ENV" != "production" ] || [ ! -f compose.yml ]; then
	echo "\033[31mERROR: APP_ENV must be set to 'production' and compose.yml must exist.\033[0m"
	exit 1
fi

echo "Starting production environment..."
docker compose up -d