FROM composer:2 AS composer
FROM php:8.0-fpm AS release

# duong test
ARG APP_ENV=develop
ENV BASIC_AUTH_USER=jinjer-dev
ENV BASIC_AUTH_PW=jMXsrb6z%HmaqVVB

# Composer settings
ENV COMPOSER_HOME /usr/local/lib/composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_CACHE_DIR /dev/null

# Install composer
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=composer /tmp/keys.dev.pub /tmp/keys.tags.pub ${COMPOSER_HOME}/

# Install OS Packages
RUN apt update \
    && apt upgrade --yes \
    && apt install --yes --no-install-recommends \
        # tiny is small init module https://github.com/krallin/tini \
        tini \
        # Web server
        nginx \
        # for zip
        zip \
        # for unzip
        unzip \
        # for gd php extension
        libgd-dev \
        libxrender1 \
        libx11-dev \
        libjpeg62 \
        libxtst6 \
        libjpeg-dev \
        # for xsl php extension
        libxslt1-dev \
        # for zip php extension
        libzip-dev \
        bc \
        # for fonts
        libfontconfig \
        libfreetype6 \
        xfonts-cyrillic \
        xfonts-scalable \
        fonts-liberation \
        fonts-ipafont-gothic \
        fonts-wqy-zenhei \
        fonts-tlwg-loma-otf \
        # for soap \
        libxml2-dev \
        # chunk pdf
        ghostscript \
        # detact page size pdf
        poppler-utils \
    && apt install -y vim \
    && apt clean \
    && rm -rf /var/lib/apt/lists/*

# Add docker php ext repo
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Install php extensions
RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions mbstring pdo_mysql zip exif pcntl gd memcached

# Install PHP Extensions
RUN pecl install \
        redis \
    && docker-php-ext-install \
        calendar \
        # exif \
        gettext \
        # pcntl \
        pdo \
        # pdo_mysql \
        shmop \
        # sockets \
        sysvmsg sysvsem sysvshm xsl opcache \
        # zip \
        # soap \
        soap \
    && docker-php-ext-enable \
        opcache redis

# Copy middleware config files
#COPY rootfs/ /

RUN #chmod 0444 /etc/mysql/conf.d/my.cnf

WORKDIR /usr/src/app

# Copy all source files to /usr/src/app for production.
# But available to overwrite by volume option when development.
#COPY ../docker .

# RUN echo "$BASIC_AUTH_USER:$(openssl passwd -apr1 $BASIC_AUTH_PW)\n" >> "/etc/nginx/.htpasswd"

RUN #chown -R root:www-data storage && chmod -R 777 storage

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["laravel"]

FROM release AS develop

# Install Composer packages with development package
RUN #composer install --no-progress --dev

RUN php -m | grep gd

# Enable Laravel telescope
RUN #php artisan telescope:publish
