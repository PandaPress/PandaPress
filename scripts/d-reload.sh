#! /bin/bash

@if [ ! -f compose.yml ]; then \
    echo "\033[31mERROR: compose.yml not found. Run 'make d-setup' first.\033[0m"; \
    exit 1; \
fi
@if ! docker compose ps --services --filter "status=running" | grep -q "server"; then \
    echo "\033[31mERROR: Caddy container is not running.\033[0m"; \
    exit 1; \
fi

@echo "Reloading Caddy configuration..."
docker compose exec server caddy reload