# Беремо готовий образ PHP 8.1 з Alpine Linux (легка ОС)
FROM php:8.2.18-fpm-alpine

# Install necessary dependencies BEFORE installing PHP extensions
RUN apk add --no-cache --update \
    libpng-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    libexif-dev \
    mysql-client \
    mysql-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql bcmath gd zip exif

# Встановлюємо Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Встановлюємо необхідні пакети Linux
RUN apk add --no-cache --update git unzip

# Створюємо робочу директорію для нашого Laravel-проекту
WORKDIR /var/www

# Copy the application code FIRST
COPY . /var/www

# Встановлюємо залежності Composer
RUN composer install --no-dev --optimize-autoloader

# Генеруємо ключ застосунку Laravel
RUN php artisan key:generate

# Запускаємо міграції бази даних (якщо потрібно при кожному запуску контейнера)
#RUN php artisan migrate --force

# Вказуємо користувача для виконання Artisan команд
USER www-data

# Відкриваємо порт 9000 для PHP-FPM
EXPOSE 9000

# Команда для запуску PHP-FPM
CMD ["php-fpm"]
# Або вбудований веб-сервер PHP, тоді вище EXPOSE 8000 і 8000:8000 у docker-compose.yml
# CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
