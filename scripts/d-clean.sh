#! /bin/bash

@if [ ! -f compose.yml ]; then \
    echo "\033[31mERROR: compose.yml not found. Nothing to clean.\033[0m"; \
    exit 1; \
fi
@if docker info > /dev/null 2>&1; then \
    echo "Docker is running, cleaning up containers and images and files..."; \
    docker compose down -v --rmi all; \
else \
    echo "Docker is not running, cleaning up files only..."; \
fi

rm -rf compose.yml caddy/Caddyfile caddy/data caddy/config logs/caddylogs