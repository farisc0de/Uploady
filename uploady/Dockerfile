FROM php:8.2-apache

RUN docker-php-ext-install pdo_mysql

COPY . /var/www/html

RUN chmod 777 -R /var/www/html/uploads

RUN chown -R www-data:www-data /var/www/html/uploads

EXPOSE 80