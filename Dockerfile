FROM php:8.4-fpm

RUN apt-get update \
	&& apt-get install -y --no-install-recommends git unzip libzip-dev build-essential pkg-config zlib1g-dev liblz4-dev libzstd-dev \
	&& pecl install redis \
	&& docker-php-ext-enable redis \
	&& docker-php-ext-install pdo pdo_mysql zip \
	&& rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Copy custom PHP-FPM pool configuration for better concurrency
COPY docker/php/8.4/www.conf /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/html

CMD ["php-fpm"]
