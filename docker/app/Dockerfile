# Get ready image of PHP (required version) with PHP-FPM on base of Alpine Linux (easy OS)
FROM php:8.2.18-fpm-alpine

# apk - package manager of Alpine linux
# apk add - command to install packages
## --no-cache = do not store packages in cache after install to reduce package size
## --update - guarantee to install the latest available versions

# Install sys libs and utils BEFORE installing PHP extensions, because these are necessary dependencies for it. List depends on selected OS
RUN apk add --no-cache --update \
    libpng-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    libexif-dev \
    mysql-client \
    mysql-dev

# Install required packages (OS level)
RUN apk add --no-cache --update git unzip nano curl

# Install required PHP extensions (by helper-script from used PHP Docker image)
RUN docker-php-ext-install pdo pdo_mysql bcmath gd zip exif

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working dir for next RUN, CMD, ENTRYPOINT commands
# And for target relative paths in COPY and ADD commands
## Source path (first argument) in COPY and ADD commands is always relative to root of build context (where 'docker compose build called'). It starts without / (and single . meant root)
## Target path (second argument) in COPY and ADD commands may be absolute image path - starts from /. Or may be revalive to image path, previously set in last WORKDIR command - starts without /
WORKDIR /var/www

# In dev copy only files with Composer dependancies
# It lets Composer to work during building image process
# Шар з залежностями буде перезбиратися лише при зміні composer.json/lock.
COPY composer.json composer.lock ./

# Install packages, dependencies etc from composer.json
## --no-dev - for stage or prod only, do not install dependencies from require-dev part of composer.json, because they are for dev process only
## --optimize-autoloader - for stage or prod only, because makes autoloader quicker in usage, but slower in generation (which is only once for prod)
RUN composer install --no-scripts --prefer-dist

# Copy Entrypoint script to initial artisan commands to container
COPY docker/app/entrypoint.sh /usr/local/bin/entrypoint.sh

# Make Entrypoint script executable
RUN chmod +x /usr/local/bin/entrypoint.sh

# Define web app user for all next commands and main process, which starts container
## By default the majority of Dockerfile commands are initiated by user 'root' with max rights, which is risky
## www-data typically has min rights to read app files and write to allowed app dirs
## www-data is a typical system user in linux distributives (in Alpine created auto with basic PHP images) for Web server/PHP-FPM, so setting the same user for web app guarantees no access conflicts
USER www-data

# Open port, indicates that container is ready to listen on port 9000 of inner docker network
## PHP-FPM listening nginx
## port not published on host machine
EXPOSE 9000

# Set script as ENTRYPOINT
## ENTRYPOINT launches first, when container starts
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Launch PHP-FPM - PHP processes manager, which works in app container
## This CMD is for Entrypoint. But this process moved to script in entrypoint.sh and makes php-fpm process as main there
## So Entrypoint will not be able to process this command here
## It is here just to remember for cases without Entrypoint scripts
CMD ["php-fpm"]
