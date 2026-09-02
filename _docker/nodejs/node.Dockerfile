FROM node:20

WORKDIR /var/www

# Установим необходимые пакеты
RUN apt-get update && apt-get install -y \
    vim \
    zip \
    unzip \
    curl \
    git \
    && rm -rf /var/lib/apt/lists/*

EXPOSE 5173