#!/bin/sh
# Updates the WordPress using wp cli and run other post update/install tasks
# Author: Amon Santana


# Setup JWT plugin and HTTP_AUTHORIZATION on Apache
# If already installed, it will do nothing
wp plugin install jwt-authentication-for-wp-rest-api --allow-root --activate

# Add the authorization header in the .htaccess file if not already there
if grep -q 'Authorization "(.*)" HTTP_AUTHORIZATION=$1' /var/www/html/.htaccess
then
    echo "Auth header already added in htaccess..."
else
    cp -i fam/.htaccess /var/wwww/html/.htaccess
fi

# Install wp-api-menus. If already installed, it will do nothing
wp plugin install wp-api-menus --allow-root --activate


# Check if fam-oauth is activated, if not, activate it
wp plugin is-installed fam-oauth --allow-root
if ! [ $? -eq 0 ]; then
    echo "#! Error !#: plugin fam-oauth missing in the wordpress/wp-content/plugins folder"
    wp plugin install fam-oauth --allow-root --activate
else
    echo "Activating plugin fam-oauth..."
    wp plugin activate fam-oauth --allow-root
fi


# Check if fam plugin is activated, if not, activate it
wp plugin is-installed fam --allow-root
if ! [ $? -eq 0 ]; then
    echo "#! Error !# : fam plugin missing in the wordpress/wp-content/plugins folder"
    wp plugin install fam --allow-root --activate
else
    echo "Activating ors plugin..."
    wp plugin activate fam --allow-root
fi

# We are not building the front-end on the target server any more, but before commit/push
# echo "Building the dashboard app..."
# cd /var/www/webapp/ && npm install && npm run build

