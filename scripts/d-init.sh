#!/bin/bash

if [ ! -f .env ]; then
    touch .env
    echo ".env file created"
fi

read -p "Enter your domain name (default: localhost): " domain
domain=${domain:-localhost}
echo -e "\033[36mSetting domain to: $domain\033[0m"

if [ ! -f compose.development.yml ] && [ ! -f compose.production.yml ]; then
    echo -e "\033[36mcompose.development.yml and compose.production.yml not found, creating them from templates...\033[0m"
    
    if [ "$domain" = "localhost" ]; then
        # Development branch
        cp compose.development.yml.template compose.development.yml

        cp caddy/Caddyfile.template caddy/Caddyfile

        echo -e "\033[36mCaddyfile created\033[0m"
    else
        # Production branch
        cp compose.production.yml.template compose.production.yml
        echo "SITE_ADDRESS=${domain}" >> .env
        cp caddy/Caddyfile.template caddy/Caddyfile
        #awk '{gsub(/{SITE_ADDRESS}/,"'"$domain"'")}1' caddy/Caddyfile > caddy/Caddyfile.tmp && mv caddy/Caddyfile.tmp caddy/Caddyfile
        sed -i "s/localhost/${domain}/g" caddy/Caddyfile
        echo -e "\033[36mAdded SITE_ADDRESS=${domain} to .env (production mode)\033[0m"
    fi
    
    echo -e "\033[32mCreated compose files from templates.\033[0m"
    
    # Create necessary directories
    mkdir -p caddy/data
    mkdir -p caddy/config
    mkdir -p logs/caddy
    touch logs/caddy/access.log
    touch logs/caddy/error.log
    echo -e "\033[36mCreated necessary directories for Caddy\033[0m"

    echo -e "\033[32mConfiguration complete! You can now run 'make d-up' to start the servers.\033[0m"
else
    echo -e "\033[33mcompose.development.yml and compose.production.yml already exist. If you want to reset them, delete them first and run this command again.\033[0m"
fi 