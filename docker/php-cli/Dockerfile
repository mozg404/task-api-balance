FROM php:8.4-cli

ARG USER_ID=1000
ARG GROUP_ID=1000

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    libpq-dev

RUN docker-php-ext-install \
    pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd sockets

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN groupadd -g ${GROUP_ID} appuser || true
RUN useradd -u ${USER_ID} -g ${GROUP_ID} -m appuser || true
WORKDIR /app
RUN chown -R ${USER_ID}:${GROUP_ID} /app
USER appuser