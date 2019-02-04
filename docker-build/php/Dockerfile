FROM php:7.2-fpm

RUN apt-get update \
    && apt-get install -y \
     --no-install-recommends supervisor debconf subversion git apt-transport-https apt-utils \
    build-essential locales acl mailutils wget zip unzip vim curl \
    gnupg gnupg1 gnupg2

RUN docker-php-ext-install pdo pdo_mysql

RUN mkdir /run/php

ADD conf/www.conf /etc/php/7.2/fpm/pool.d/www.conf
ADD conf/php-fpm.conf /etc/php/7.2/fpm/php-fpm.conf
ADD conf/php.ini /usr/local/etc/php/php.ini

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer
RUN ln -snf /usr/share/zoneinfo/Europe/Amsterdam /etc/localtime

ARG LIVESTREAM_USER
RUN useradd -ms /bin/bash ${LIVESTREAM_USER}
USER ${LIVESTREAM_USER}

WORKDIR /var/www/livestream
