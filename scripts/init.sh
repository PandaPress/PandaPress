#!/bin/bash

if [ ! -f .env ]; then
    touch .env
    echo ".env file created"
fi


if [ ! -f compose.yml ]; then
    echo -e "\033[36mcompose.yml not found, creating it from template...\033[0m"
    cp compose.yml.template compose.yml
    cp compose.production.yml.template compose.production.yml
    echo -e "\033[32mCreated compose.yml from template.\033[0m"
    
    read -p "Enter your domain name (default: localhost): " domain
    domain=${domain:-localhost}
    echo -e "\033[36mSetting domain to: $domain\033[0m"

    cp caddy/Caddyfile.template caddy/Caddyfile
    echo -e "\033[36mGenerating Caddy configuration files...\033[0m"
    
    # Create .env file with the domain
    if [ "$domain" = "localhost" ]; then
        echo "SITE_ADDRESS=:80" >> .env
        echo -e "\033[36mAdded SITE_ADDRESS=:80 to .env (localhost mode)\033[0m"
    else
        echo "SITE_ADDRESS=${domain}" >> .env
        echo -e "\033[36mAdded SITE_ADDRESS=${domain} to .env (production mode)\033[0m"
    fi
    
    # Create necessary directories
    mkdir -p caddy/data
    mkdir -p caddy/config
    mkdir -p logs/caddylogs
    echo -e "\033[36mCreated necessary directories for Caddy\033[0m"

    echo -e "\033[32mConfiguration complete! You can now run 'make d-up' to start the servers.\033[0m"
else
    echo -e "\033[33mcompose.yml already exists. If you want to reset it, delete it first and run this command again.\033[0m"
fi 