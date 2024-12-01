#!/bin/bash

if [ ! -f compose.yml ]; then
    echo -e "\033[36mcompose.yml not found, creating it from template...\033[0m"
    cp compose.yml.template compose.yml
    echo -e "\033[32mCreated compose.yml from template.\033[0m"
    
    read -p "Enter your domain name (default: localhost): " domain
    domain=${domain:-localhost}
    echo -e "\033[36mSetting domain to: $domain\033[0m"

    cp nginx/default.conf.template nginx/default.conf
    echo -e "\033[36mGenerating nginx configuration files...\033[0m"
    if [ "$domain" = "localhost" ]; then
        awk -v domain="$domain" '{gsub(/server_name SERVER_NAME/, "server_name " domain)}1' nginx/default.conf > nginx/default.conf.tmp
    else
        awk -v domain="$domain" '{gsub(/server_name SERVER_NAME/, "server_name " domain " www." domain)}1' nginx/default.conf > nginx/default.conf.tmp
    fi
    mv nginx/default.conf.tmp nginx/default.conf
    
    echo -e "\033[32mConfiguration complete! You can now run 'make d-up' to start the servers.\033[0m"
else
    echo -e "\033[33mcompose.yml already exists. If you want to reset it, delete it first and run this command again.\033[0m"
fi 