FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    apt-utils \
    git curl zip unzip \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libssl-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libmagickwand-dev \
    && docker-php-ext-configure gd \
        --with-jpeg \
        --with-freetype \
    && docker-php-ext-install \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
        bcmath \
        zip \
        gd \
    && pecl install redis imagick \
    && docker-php-ext-enable redis imagick \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
    
# Копировать конфигурацию из локальной директории в контейнер
COPY ./_docker/app/conf.d/* $PHP_INI_DIR/conf.d/

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin
    
WORKDIR /var/www

# Создаём пользователя с UID, совпадающим с локальным пользователем
ARG USERNAME=ilia
ARG UID=1000
ARG GID=1000
RUN groupadd -g ${GID} ${USERNAME} && \
    useradd -m -u ${UID} -g ${GID} ${USERNAME}

# Устанавливаем права для рабочей директории и переключаемся на нового пользователя
WORKDIR /var/www
RUN chown -R ${USERNAME}:${USERNAME} /var/www
USER ${USERNAME}