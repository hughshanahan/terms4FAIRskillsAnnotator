#!/bin/sh

# This script contains all command to launch by Dockerfile.

# Set the write permissions on the var directory
chown -R www-data:www-data var

# Install Composer Dependencies
composer Install

# Generate Documentation
sh generate-documentation.sh

# Start Web Server
/usr/sbin/apache2ctl -D FOREGROUND