FROM php:8.3-fpm

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Установка расширений PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets xml iconv simplexml

# Установка расширения redis
RUN pecl install redis && docker-php-ext-enable redis

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка рабочей директории
WORKDIR /var/www

# Копирование файлов проекта
COPY . .

# Установка зависимостей Composer
RUN composer install --ignore-platform-req=ext-zip

# Права на папки
RUN chown -R www-data:www-data /var/www/bootstrap/cache
RUN chmod -R 777 /var/www/storage

# Папка для логов laravel_cron
RUN chmod -R 777 /var/www/storage/app/uploads

CMD php-fpm
