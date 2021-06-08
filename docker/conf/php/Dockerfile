FROM php:7.4-apache
#RUN apt-get update --fix-missing
RUN apt-get update \
    && apt-get install -y \
        wget \
        g++ \
        libicu-dev \
        libpq-dev \
        zip \
        unzip \
        libzip-dev \
        # mbstring
        libonig-dev \
        # GD
        libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
        libxml2-dev \
 && rm -rf /var/lib/apt/lists/*

RUN echo "date.timezone=Europe/Paris" >> "/usr/local/etc/php/php.ini"
RUN docker-php-ext-install -j$(nproc) mbstring intl pdo pdo_mysql zip opcache soap

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
 && docker-php-ext-install -j$(nproc) pdo_pgsql pgsql

# Install gd
RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
 && docker-php-ext-install -j$(nproc) gd

# Install xdebug
RUN pecl install xdebug-2.9.0 \
  && docker-php-ext-enable xdebug
COPY php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

#Install blackfire
RUN export VERSION=`php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;"` \
  && curl -A "Docker" -o /tmp/blackfire-probe.tar.gz -D - -L -s https://blackfire.io/api/v1/releases/probe/php/linux/amd64/${VERSION} \
  && tar zxpf /tmp/blackfire-probe.tar.gz -C /tmp \
  && mv /tmp/blackfire-*.so `php -r "echo ini_get('extension_dir');"`/blackfire.so \
  && echo "extension=blackfire.so\nblackfire.agent_socket=tcp://blackfire:8707\n" > $PHP_INI_DIR/conf.d/blackfire.ini
  
RUN a2enmod rewrite

COPY apache.conf /etc/apache2/sites-available/000-default.conf

ARG uid=1008
ARG gid=1008
RUN groupadd -g ${gid} localUser \
    && useradd -u ${uid} -g ${gid} -m -s /bin/bash localUser

RUN usermod -a -G www-data localUser

RUN sed --in-place "s/User \${APACHE_RUN_USER}/User localUser/" /etc/apache2/apache2.conf \
 && sed --in-place  "s/Group \${APACHE_RUN_GROUP}/Group localUser/" /etc/apache2/apache2.conf


RUN echo "upload_max_filesize=30M\n" >> "/usr/local/etc/php/php.ini" \
 && echo "post_max_size=30M\n" >> "/usr/local/etc/php/php.ini"
