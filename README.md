# Panda Press

- A modern CMS but without composer

## Features

- MVC architecture
- PHP 8.2+
- PSR-4 compatibility
- composer free, beginner friendly
- AdminLTE dashboard
- powerful plugins
- beautiful themes
- open source
- always free
- MIT license

## Useful links

- [How to Generate a Server Certificate for MongoDB](https://docs.bigchaindb.com/projects/server/en/latest/k8s-deployment-template/server-tls-certificate.html)

## Getting Started

```bash

# pre-check
getent group www-data > /dev/null && echo "Group www-data already exists." || (echo "Creating www-data group..." && sudo groupadd www-data && echo "Group www-data created.")
getent passwd www-data > /dev/null && echo "User www-data already exists." || (echo "Creating www-data user..." && sudo useradd -g www-data -s /usr/sbin/nologin -d /var/www -M www-data && echo "User www-data created.")

echo "Group ID of www-data: $(getent group www-data | cut -d: -f3)"
echo "User ID of www-data: $(id -u www-data)"

# Clone the repository
cd /var/www
mkdir panda_press
chown www-data:www-data panda_press
sudo -u www-data /usr/bin/git clone https://github.com/PandaPress/PandaPress.git panda_press
cd panda_press

# init and run the containers
make d-init
make d-up

```
