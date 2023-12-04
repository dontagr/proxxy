FROM php:8.1-fpm-bullseye

RUN update-ca-certificates \
    && apt-get update \
    && apt-get install -y curl git unzip libcurl4-openssl-dev libzip-dev libssl-dev libxml2-dev librdkafka-dev gnupg2 software-properties-common \
    && docker-php-ext-install pdo pdo_mysql zip sockets opcache intl

WORKDIR /var/www/symfony_docker

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony
RUN git config --global user.email "you@example.com" && git config --global user.name "Your Name"
