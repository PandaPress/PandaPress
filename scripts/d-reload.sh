#! /bin/bash

# Source .env file if it exists
if [ -f .env ]; then
    # Export all variables from .env
    export $(cat .env | grep -v '^#' | xargs)
fi

# Default to development if APP_ENV is not set
APP_ENV=${APP_ENV:-development}
COMPOSE_FILE="compose.${APP_ENV}.yml"

if [ ! -f "$COMPOSE_FILE" ]; then
    echo "\033[31mERROR: ${COMPOSE_FILE} not found.\033[0m"
    exit 1
fi

docker compose -f "$COMPOSE_FILE" restart