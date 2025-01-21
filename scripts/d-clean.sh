#! /bin/bash

# Exit on error, undefined vars, and pipe failures
set -euo pipefail

# Determine APP_ENV, default to development if not set
APP_ENV=${APP_ENV:-development}
COMPOSE_FILE="compose.${APP_ENV}.yml"

echo "Starting cleanup process..."

# Docker cleanup
if docker info > /dev/null 2>&1; then
    if [ -f "${COMPOSE_FILE}" ]; then
        echo "Docker is running, cleaning up containers and images..."
        docker compose -f "${COMPOSE_FILE}" down -v --rmi all
    else
        echo "Docker is running, but ${COMPOSE_FILE} not found, cleanup config files only..."
    fi
else
    echo "Docker is not running, cleanup config files only..."
fi

# Compose files cleanup
for env in development production; do
    compose_file="compose.${env}.yml"
    if [ -f "${compose_file}" ]; then
        echo "Removing ${compose_file}..."
        rm -f "${compose_file}"
    fi
done

# Caddy cleanup
caddy_files=(
    "caddy/Caddyfile"
    "caddy/data"
    "caddy/config"
    "logs/caddy"
)

for item in "${caddy_files[@]}"; do
    if [ -e "${item}" ]; then
        echo "Removing ${item}..."
        rm -rf "${item}"
    fi
done

echo "Cleanup completed successfully!"
