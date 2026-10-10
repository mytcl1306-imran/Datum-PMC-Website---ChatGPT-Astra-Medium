FROM php:8.3-apache
RUN apt-get update && apt-get install -y --no-install-recommends libonig-dev libcurl4-openssl-dev && docker-php-ext-install pdo_mysql mbstring curl && a2enmod rewrite headers && rm -rf /var/lib/apt/lists/*
WORKDIR /var/www/html
COPY . .
COPY deploy/apache.conf /etc/apache2/sites-available/000-default.conf
COPY deploy/php.ini /usr/local/etc/php/conf.d/production.ini
RUN mkdir -p storage /var/lib/php/sessions && chown -R www-data:www-data storage /var/lib/php/sessions && chmod 1733 /var/lib/php/sessions
EXPOSE 80
