#!/bin/bash

if [ ! -f .env ]; then
    touch .env
    echo ".env file created"
fi

read -p "Enter your domain name: " domain
echo -e "\033[36mSetting domain to: $domain\033[0m"

if  [ ! -f compose.yml ]; then
    echo -e "\033[36m compose.yml not found, creating it...\033[0m"
    
    cp compose.yml.template compose.yml
    echo "SITE_ADDRESS=${domain}" >> .env
    cp caddy/Caddyfile.template caddy/Caddyfile
    awk '{gsub(/{SITE_ADDRESS}/,"'"$domain"'")}1' caddy/Caddyfile > caddy/Caddyfile.tmp && mv caddy/Caddyfile.tmp caddy/Caddyfile
    echo -e "\033[36mAdded SITE_ADDRESS=${domain} to .env \033[0m"

    echo -e "\033[32mCreated compose file from template.\033[0m"
    
    # Create necessary directories
    mkdir -p caddy/data
    mkdir -p caddy/config
    mkdir -p logs/caddy
    touch logs/caddy/access.log
    touch logs/caddy/error.log
    echo -e "\033[36mCreated necessary directories for Caddy\033[0m"

    echo -e "\033[32mConfiguration complete! You can now run 'make d-up' to start the servers.\033[0m"
else
    echo -e "\033[33mcompose.yml already exists. If you want to reset it, delete it first and run this command again.\033[0m"
fi 