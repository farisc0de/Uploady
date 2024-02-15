FROM php:apache

RUN docker-php-ext-install pdo pdo_mysql mysqli && docker-php-ext-enable mysqli

COPY . /var/www/html

RUN chmod 755 -R /var/www/html/

RUN chown -R www-data:www-data /var/www/html/

EXPOSE 80