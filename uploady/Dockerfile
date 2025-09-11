FROM serversideup/php:8.3-fpm-apache

# Create custom permissions so the uploaded files are accessible on the Docker host
# https://serversideup.net/open-source/docker-php/docs/guide/understanding-file-permissions
ARG USER_ID
ARG GROUP_ID

USER root
COPY --chown=$USER_ID:$GROUP_ID . /var/www/html
RUN docker-php-serversideup-set-id www-data $USER_ID:$GROUP_ID && \
    docker-php-serversideup-set-file-permissions --owner $USER_ID:$GROUP_ID --service apache

USER www-data
