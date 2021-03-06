FROM php:7.2-apache

MAINTAINER Aurélien GIRY <aurelien.giry@gmail.com>

COPY . /usr/local/docker
RUN chmod -R 777 /usr/local/docker

RUN apt-get update && apt-get install -y \
        libxml2-dev \
        libxml2-utils \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libjpeg-dev \
        libpng-dev \
        vim \
        mysql-client \
        telnet \
    	netcat \
        wget \
        git \
        libicu-dev \
        python-pip \
        python-dev \
        ssl-cert

RUN docker-php-ext-configure gd --with-png-dir=/usr --with-jpeg-dir=/usr --with-freetype-dir=/usr/ 

RUN docker-php-ext-install soap gd zip intl opcache pdo pdo_mysql mysqli mbstring calendar

RUN pecl install xdebug

RUN docker-php-ext-enable xdebug opcache calendar

# Apache modules
RUN a2enmod ssl expires headers rewrite

# Composer
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN \
    curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Blackfire extension
RUN version=$(php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;") \
    && curl -A "Docker" -o /tmp/blackfire-probe.tar.gz -D - -L -s https://blackfire.io/api/v1/releases/probe/php/linux/amd64/$version \
    && tar zxpf /tmp/blackfire-probe.tar.gz -C /tmp \
    && mv /tmp/blackfire-*.so $(php -r "echo ini_get('extension_dir');")/blackfire.so \
    && printf "extension=blackfire.so\nblackfire.agent_socket=tcp://blackfire:8707\n" > $PHP_INI_DIR/conf.d/blackfire.ini

# Blackfire CLI
RUN mkdir -p /tmp/blackfire \
    && curl -A "Docker" -L https://blackfire.io/api/v1/releases/client/linux_static/amd64 | tar zxp -C /tmp/blackfire \
    && mv /tmp/blackfire/blackfire /usr/bin/blackfire \
    && rm -Rf /tmp/blackfire

RUN usermod -u 1000 www-data

ENTRYPOINT ["/usr/local/docker/run.sh"]
