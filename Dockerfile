FROM php:8.1-apache

# 1. Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libxml2-dev \
    libsqlite3-dev \
    libonig-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# 2. Instalar y habilitar extensiones (Sintaxis corregida para PHP 8.1)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    mysqli \
    pdo \
    pdo_mysql \
    pdo_sqlite \
    zip \
    soap \
    bcmath \
    mbstring

# 3. Habilitar mod_rewrite
RUN a2enmod rewrite

WORKDIR /var/www/html
