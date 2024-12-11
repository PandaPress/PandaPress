#!/bin/bash


export UID=1000
export GID=1000

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo "Please run as root (with sudo)"
    exit 1
fi

# Create webuser if it doesn't exist
if ! id "webuser" &>/dev/null; then
    useradd -u 1000 -m webuser
    usermod -aG www-data webuser
fi

# Set correct permissions
chown -R webuser:webuser .
chmod -R 755 .


if [ ! -f compose.yml ]; then
    echo -e "\033[36mcompose.yml not found, creating it from template...\033[0m"
    cp compose.yml.template compose.yml
    echo -e "\033[32mCreated compose.yml from template.\033[0m"
    
    read -p "Enter your domain name (default: localhost): " domain
    domain=${domain:-localhost}
    echo -e "\033[36mSetting domain to: $domain\033[0m"

    cp nginx/default.conf.template nginx/default.conf
    echo -e "\033[36mGenerating nginx configuration files...\033[0m"
    awk -v domain="$domain" '{gsub(/server_name SERVER_NAME/, "server_name " domain)}1' nginx/default.conf > nginx/default.conf.tmp
    mv nginx/default.conf.tmp nginx/default.conf
    
    echo -e "\033[32mConfiguration complete! You can now run 'make d-up' to start the servers.\033[0m"
else
    echo -e "\033[33mcompose.yml already exists. If you want to reset it, delete it first and run this command again.\033[0m"
fi 