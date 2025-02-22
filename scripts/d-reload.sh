#! /bin/bash

if [ ! -f "compose.yml" ]; then
    echo "\033[31mERROR: compose.yml not found.\033[0m"
    exit 1
fi

docker compose restart