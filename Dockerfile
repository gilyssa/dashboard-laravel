FROM php:8.1-fpm

# Defina o diretório de trabalho
WORKDIR /var/www/html

# Instale as dependências necessárias
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install zip

# Copie para o diretório de trabalho
COPY ./ /var/www/html

# Instale o Composer e execute para instalar as dependências do Laravel
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader

# Configure a porta em que o servidor web irá escutar
EXPOSE 80

# Execute os comandos para configurar o Laravel
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Execute o PHP-FPM para iniciar o servidor
CMD php-fpm
