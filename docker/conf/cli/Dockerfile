FROM php:7.4

ARG uid=1008
ARG gid=1008

RUN groupadd -g ${gid} localUser \
    && useradd -u ${uid} -g ${gid} -m -s /bin/bash localUser

RUN usermod -a -G www-data localUser

# Installation de l'environnement node

# Force l'utilisation de la bonne version de node : https://github.com/nodejs/help/issues/554#issuecomment-429633801
RUN printf 'Package: nodejs\n\
Pin: origin deb.nodesource.com\n\
Pin-Priority: 1001\n' \
>> /etc/apt/preferences.d/nodejs

RUN apt-get update && apt-get install -y gnupg
RUN curl -sL https://deb.nodesource.com/setup_8.x | bash -
RUN apt-get update && apt-get install -y nodejs

RUN apt-get update \
    && apt-get install -y \
        zip \
        unzip \
        wget \
        git \
        libicu-dev \
        zlib1g-dev \
        bash-completion \
        libzip-dev \
        # mbstring
        libonig-dev \
        libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
        libxml2-dev \
        firebird-dev \
        libfbclient2 \
        libpq-dev \
        libssh2-1-dev \
        libssh2-1 \
 && rm -rf /var/lib/apt/lists/*

RUN npm install -g grunt-cli

#extensions et leurs dépendances
RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
 && docker-php-ext-install -j$(nproc) intl pdo_mysql zip gd mbstring soap pdo_pgsql pgsql
RUN curl http://pecl.php.net/get/ssh2-1.2.tgz -o ssh2.tgz && \
    pecl install ssh2 ssh2.tgz && \
    docker-php-ext-enable ssh2 && \
    rm -rf ssh2.tgz
RUN echo "error_reporting = E_ALL" >> /usr/local/etc/php/php.ini
