#! /bin/bash

if [ ! -f compose.development.yml ]; then
	echo "\033[31mERROR: compose.development.yml not found. Run 'make d-init' first.\033[0m"
	exit 1
fi

if [ ! -f compose.production.yml ]; then
	echo "\033[31mERROR: compose.production.yml not found. Run 'make d-init' first.\033[0m"
	exit 1
fi

docker compose -f compose.development.yml stop
docker compose -f compose.production.yml stop