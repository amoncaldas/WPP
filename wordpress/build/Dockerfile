# Author: Amon Caldas <amoncaldas@gmail.com>
# Version 0.2
# 19.08.2020

FROM wordpress:5.7-php7.4-apache

ARG DEBUG_SUPPORT=false
ARG NOTIFICATIONS_TASK_SUPPORT=false


# Install nano
RUN apt-get update && apt-get install nano -y && apt-get install cron -y && apt-get install wget -y

RUN if ($DEBUG_SUPPORT == "true"); \
    then \
    echo "## DEBUG SUPPORT ENABLED, INSTALLING XDEBUG ###"; \
    pecl install xdebug-2.9.6 && docker-php-ext-enable xdebug; \
    fi

# Install gnupg
RUN apt-get update && apt-get install -y gnupg2 && apt-get install -y git

# Setup Apache
RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf
RUN sed -i 's/VirtualHost \*:80/VirtualHost \*:\*/' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's/<\/VirtualHost>/Alias \/static \/var\/www\/webapp\/static \n <directory \/var\/www\/webapp\/static >\nOptions Indexes FollowSymLinks MultiViews\nRequire all granted\n<\/directory>\n<\/VirtualHost>/' /etc/apache2/sites-available/000-default.conf


# Setup XDebug
RUN if ($DEBUG_SUPPORT == "true"); then \
    echo "### CONFIGURING XDEBUG...###"; \
    echo "xdebug.max_nesting_level = 300" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.remote_enable = 1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.remote_autostart = 1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.remote_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    # if your IDE has problems with the debug idekey you can comment out the following line
    echo "xdebug.idekey=docker" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    sed -i 's/display_errors = Off/display_errors = On/' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    fi


COPY ["wp-config.php", "/var/www/html/wp-config.php"]

# Install wp cli
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && \
    chmod +x wp-cli.phar && \
    mv wp-cli.phar /usr/local/bin/wp

# Install NODEJS/NPM
RUN curl -sL https://deb.nodesource.com/setup_12.x | bash - && \
	apt-get update && \
	apt-get install -y nodejs && \
    npm install -g npm@6.14.5




