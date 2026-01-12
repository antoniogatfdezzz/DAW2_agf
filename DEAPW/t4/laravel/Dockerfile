FROM php:8.2-cli

WORKDIR /app

# Dependencias básicas para Laravel y Composer
RUN apt-get update \
	&& apt-get install -y git unzip libzip-dev \
	&& docker-php-ext-install pdo pdo_mysql zip \
	&& rm -rf /var/lib/apt/lists/*

# Instalar Composer desde la imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Crear la aplicación Laravel en /app
RUN composer create-project --prefer-dist laravel/laravel . \
	&& php artisan key:generate

# Sobrescribir la ruta principal para mostrar el "Hola Mundo" solicitado
COPY src/routes/web.php routes/web.php

EXPOSE 8000

# Servidor embebido de Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
