FROM node:24-alpine

WORKDIR /var/www

# Установим необходимые пакеты
RUN apk add --no-cache git

EXPOSE 5173
