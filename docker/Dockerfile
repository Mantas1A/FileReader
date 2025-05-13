# Dockerfile
FROM php:8.2-apache

WORKDIR /var/www/

# Custom Virtual Host config
COPY ./docker/vhost.conf /etc/apache2/sites-available/000-default.conf

COPY ./src /var/www

RUN a2enmod rewrite

EXPOSE 80
