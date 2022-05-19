FROM php:7.4-apache

RUN apt-get update \
    && apt-get install -y \
    && apt-get autoremove -y \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && apt-get install curl -y \
    && apt-get install git -y\
    && apt-get install zip -y\
    && curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony/bin/symfony /usr/local/bin/symfony
RUN git config --global user.name "doud94220"
RUN git config --global user.email "doud75@gmail.com"

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR D:\DEV\api-rest-lbc

EXPOSE 8050