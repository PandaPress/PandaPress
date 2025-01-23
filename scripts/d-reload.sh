#! /bin/bash

if [ ! -f compose.development.yml ]; then
    echo "\033[31mERROR: compose.development.yml not found.\033[0m"
    exit 1
fi


docker compose -f compose.development.yml restart 