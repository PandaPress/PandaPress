#!/bin/bash

# Check if script is run as root
if [ "$EUID" -ne 0 ]; then 
    echo "Please run as root (use sudo)"
    exit 1
fi

# Configuration
REPO_URL="https://github.com/PandaPress/PandaPress.git"
TARGET_DIR="panda_press"

# Create directory if it doesn't exist
mkdir -p "$TARGET_DIR"

# Set ownership to www-data
chown www-data:www-data "$TARGET_DIR"

# Clone the repository as www-data user
su www-data -c "git clone $REPO_URL $TARGET_DIR"

# Check if clone was successful
if [ $? -eq 0 ]; then
    echo "Repository cloned successfully to $TARGET_DIR"
    echo "Directory ownership set to www-data:www-data"
else
    echo "Failed to clone repository"
    exit 1
fi
