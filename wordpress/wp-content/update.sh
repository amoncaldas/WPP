#!/bin/sh
# Updates the WordPress using wp cli and run other post update/install tasks
# Author: Amon Santana

# Install third party plugins
wp plugin install jwt-authentication-for-wp-rest-api --allow-root --activate
wp plugin install wp-api-menus --allow-root --activate
wp plugin install custom-post-type-ui --allow-root --activate
wp plugin install acf-to-rest-api --allow-root --activate
wp plugin install wp-options-editor --allow-root --activate
wp plugin install user-role-editor --allow-root --activate
wp plugin install wp-rest-cache --allow-root --activate
wp plugin install acf-openstreetmap-field --allow-root --activate
wp plugin install classic-editor --allow-root --activate
wp plugin install extra-user-details --allow-root
wp plugin install remote-medias-lite --allow-root
wp plugin install wp-mail-smtp --allow-root --activate
wp plugin install imsanity --allow-root --activate
wp plugin install code-snippets --allow-root 
wp plugin install wp-rollback --allow-root --activate

wp plugin install disable-admin-notices --allow-root --activate
wp plugin install glance-that --allow-root --activate
wp plugin install iframe --allow-root --activate

# wp plugin install quick-and-easy-post-creation-for-acf-relationship-fields --allow-root --activate


# Activate wpp plugins
wp plugin activate wpp --allow-root
wp plugin activate wpp_rest_cache_auto_cleaner --allow-root
wp plugin activate wpp_geo --allow-root
wp plugin activate wp-options-editor --allow-root


# Set htacess modifiable
chmod 777 /var/www/html/.htaccess 

# copy custom htaccess to root
yes | cp -rf /var/www/html/wp-content/wpp/.htaccess /var/www/html/.htaccess

# Set htacess as only readable
chmod 0444 /var/www/html/.htaccess


# copy custom robots to root
# yes | cp -rf /var/www/html/wp-content/wpp/robots.txt /var/www/html/robots.txt


# We are not building the front-end on the target server any more, but before commit/push
# echo "Building the dashboard app..."
# cd /var/www/webapp/ && npm install && npm run build

