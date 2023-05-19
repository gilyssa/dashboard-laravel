FROM php:8.1-fpm

# Defina o diretório de trabalho
WORKDIR /var/www/html

# Instale as dependências necessárias
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && cd /var/www/html && composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader

# Configure o servidor web Nginx
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Configure a porta em que o servidor web Nginx irá escutar
EXPOSE 80

# Execute os comandos para configurar o Laravel
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Execute o PHP-FPM para iniciar o servidor
CMD php-fpm
