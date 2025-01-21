#! /bin/bash

# Determine APP_ENV, default to development if not set
APP_ENV=${APP_ENV:-development}
COMPOSE_FILE="compose.${APP_ENV}.yml"

if docker info > /dev/null 2>&1; then
    echo "Docker is running, cleaning up containers and images and files..."
    if [ -f "$COMPOSE_FILE" ]; then
        docker compose -f "$COMPOSE_FILE" down -v --rmi all
    else
        echo "\033[31mERROR: $COMPOSE_FILE not found\033[0m"
    fi
else
    echo "Docker is not running, cleaning up files only..."
fi


if [ ! -f compose.development.yml ] && [ ! -f compose.production.yml ]; then
    echo "\033[31mERROR: No compose files found. Nothing to clean.\033[0m"
else
    if [ -f compose.development.yml ]; then
        echo "compose.development.yml found, cleaning up..."
        rm -rf compose.development.yml
    fi
    
    if [ -f compose.production.yml ]; then
        echo "compose.production.yml found, cleaning up..."
        rm -rf compose.production.yml
    fi
fi



# Remove Caddy files and directories if they exist
[ -f caddy/Caddyfile ] && rm -f caddy/Caddyfile
[ -d caddy/data ] && rm -rf caddy/data
[ -d caddy/config ] && rm -rf caddy/config
[ -d logs/caddy ] && rm -rf logs/caddy
