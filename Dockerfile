FROM php:8.1-fpm-alpine

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

# Copie o arquivo de configuração do Nginx
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Copie o arquivo de script de inicialização
COPY start.sh /start.sh

# Defina permissões para o arquivo de script de inicialização
RUN chmod +x /start.sh

# Configure a porta em que o servidor web irá escutar
EXPOSE 80

# Execute os comandos para configurar o Laravel
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Inicie o serviço do Nginx e do PHP-FPM usando o arquivo de script de inicialização
CMD ["/start.sh"]
