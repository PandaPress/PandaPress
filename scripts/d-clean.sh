#! /bin/bash

# Exit on error, undefined vars, and pipe failures
set -euo pipefail

echo "Starting cleanup process..."

# Docker cleanup
if docker info > /dev/null 2>&1; then
    if [ -f "compose.yml" ]; then
        echo "Docker is running, cleaning up containers and images..."
        docker compose down -v --rmi all
    else
        echo "Docker is running, but compose.yml not found, cleanup config files only..."
    fi
else
    echo "Docker is not running, cleanup config files only..."
fi

# Compose files cleanup
rm -f "compose.yml"

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
