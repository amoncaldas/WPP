#!/bin/sh
# Updates the WordPress using wp cli and run other post update/install tasks
# Author: Amon Santana

# Install third party plugins
wp plugin install jwt-authentication-for-wp-rest-api --allow-root --activate
wp plugin install reorder-posts --allow-root --activate
wp plugin install wp-api-menus --allow-root --activate
wp plugin install db-cache-reloaded-fix --allow-root --activate
wp plugin install remote-medias-lite --allow-root --activate
wp plugin install quick-and-easy-post-creation-for-acf-relationship-fields --allow-root --activate
wp plugin install custom-post-type-ui --allow-root --activate
wp plugin install acf-to-rest-api --allow-root --activate




# Install fam plugins
wp plugin install fam --allow-root --activate
wp plugin install fam-oauth --allow-root --activate
wp plugin install fam-multi-site --allow-root --activate



# copy custom htaccess to root
yes | cp -rf /var/www/html/wp-content/fam/.htaccess /var/www/html/.htaccess


# copy custom robots to root
yes | cp -rf /var/www/html/wp-content/fam/robots.txt /var/www/html/robots.txt



# We are not building the front-end on the target server any more, but before commit/push
# echo "Building the dashboard app..."
# cd /var/www/webapp/ && npm install && npm run build

