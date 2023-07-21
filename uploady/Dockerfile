FROM php:8.2-apache

ARG user

RUN docker-php-ext-install pdo_mysql

COPY . /var/www/html

RUN usermod -u ${user} www-data && groupmod -g ${user} www-data;

RUN chmod 777 -R /var/www/html/uploads

RUN chown -R www-data:www-data /var/www/html/uploads

EXPOSE 80