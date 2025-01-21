#! /bin/bash

if docker info > /dev/null 2>&1; then
    echo "Docker is running, cleaning up containers and images and files..."
    docker compose down -v --rmi all
else
    echo "Docker is not running, cleaning up files only..."
fi


if [ ! -f compose.yml ] && [ ! -f compose.production.yml ]; then
    echo "\033[31mERROR: No compose files found. Nothing to clean.\033[0m"
else
    if [ -f compose.yml ]; then
        echo "compose.yml found, cleaning up..."
        rm -rf compose.yml
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
[ -d logs/caddylogs ] && rm -rf logs/caddylogs