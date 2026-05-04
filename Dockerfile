FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

COPY . /var/www/html/

RUN cp /var/www/html/config.example.php /var/www/html/config.php

RUN chown -R www-data:www-data /var/www/html
