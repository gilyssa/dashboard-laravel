FROM php:8.1-fpm

# Instale as dependências necessárias
RUN apt-get update && apt-get install -y \
    nginx git \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader \
    && chown -R www-data:www-data /var/www/html \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Configure o servidor web Nginx
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Copie o código do seu projeto para o diretório de trabalho do contêiner
COPY . /var/www/html

# Defina o diretório de trabalho
WORKDIR /var/www/html

# Configure a porta em que o servidor web Nginx irá escutar
EXPOSE 80

# Execute o PHP-FPM para iniciar o servidor
CMD php-fpm
