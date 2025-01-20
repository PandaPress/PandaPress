#! /bin/bash

@if [ ! -f compose.yml ]; then \
	echo "\033[31mERROR: compose.yml not found. Run 'make d-setup' first.\033[0m"; \
	exit 1; \
fi

docker compose stop