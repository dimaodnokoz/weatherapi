#!/bin/sh

# Artisan commands should be called in container runtime
# So can not be used in Dockerfile
# This script launches every time when container starts

# Gives Laravel files access privileges for user/group = www-data/www-data
## 7, 7 - user and group can rwx, 5 - others only rx
## 6, 6 = user and group can rw, others - r
## As in dev they mounts from host this section is not in Dockfile, but here
## First check if dirs exist to prevent errors
if [ -d "/var/www/storage" ]; then
    chown -R www-data:www-data /var/www/storage
    find /var/www/storage -type d -exec chmod 775 {} + # for dirs
    find /var/www/storage -type f -exec chmod 664 {} + # for files
fi

if [ -d "/var/www/bootstrap/cache" ]; then
    chown -R www-data:www-data /var/www/bootstrap/cache
    find /var/www/bootstrap/cache -type d -exec chmod 775 {} +
    find /var/www/bootstrap/cache -type f -exec chmod 664 {} +
fi

# ==========

# Using mysqladmin ping to check if MySQL is totally ready
## mysql-client should be installed in Dockerfile
ATTEMPTS=0
MAX_ATTEMPTS=30 # 30 * 2 sec = 1 min try in total

while [ $ATTEMPTS -lt $MAX_ATTEMPTS ]; do
    mysqladmin ping -h"mysql" -P"3306" -u"root" -p"${MYSQL_ROOT_PASSWORD}"
    PING_EXIT_CODE=$? # save exit code of last cmd

    if [ "$PING_EXIT_CODE" -eq 0 ]; then
        echo "MySQL is ready! (Exit code: $PING_EXIT_CODE)"
        break
    else
        echo "MySQL is NOT ready yet (Exit code: $PING_EXIT_CODE) - sleeping for 2 seconds"
    fi

    sleep 2
    ATTEMPTS=$((ATTEMPTS+1))
done

if [ $ATTEMPTS -eq $MAX_ATTEMPTS ]; then
    echo "Error: MySQL did not become ready within the allotted time."
    exit 1 # exit with error, if MySQL not ready
fi

# ==========

# It's safe to use with every start: do nothing, if migrations were not changed
# Key --force just disable confirmation question for critical commands
php artisan migrate --force

# Refresh cache of Laravel configuration (typically required after changes in .env)
# php artisan config:cache

# Use exec, to make PHP-FPM as main process of container (PID 1) instead of current process (this script)
exec php-fpm
