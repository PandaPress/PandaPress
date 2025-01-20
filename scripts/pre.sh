#!/bin/bash

# Check if the www-data group exists
group_exists=$(getent group www-data)
if [ -z "$group_exists" ]; then
    echo "Creating www-data group..."
    groupadd www-data
else
    echo "Group www-data already exists."
fi


# Check if the www-data user exists
user_exists=$(id -u www-data 2>/dev/null)
if [ -z "$user_exists" ]; then
    echo "Creating www-data user..."
    useradd -g www-data -s /usr/sbin/nologin -d /var/www -M www-data
else
    echo "User www-data already exists."
fi

echo "Group ID of www-data: $(getent group www-data | cut -d: -f3)"
echo "User ID of www-data: $(id -u www-data)"

# Exit with success status
exit 0