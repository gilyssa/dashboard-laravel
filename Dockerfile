FROM php:8.1-fpm

# Instale as dependências necessárias
RUN apt-get update && apt-get install -y \
    nginx

# Copie o código do seu projeto para o diretório de trabalho do contêiner
COPY . /var/www/html

# Instale as dependências do Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN chown -R www-data:www-data /var/www/html
RUN composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader

# Configure o servidor web Nginx
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Defina o diretório de trabalho
WORKDIR /var/www/html

# Configure a porta em que o servidor web Nginx irá escutar
EXPOSE 80

# Execute os comandos para configurar o Laravel
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# Execute o PHP-FPM para iniciar o servidor
CMD php-fpm
