# Imagem base do PHP-FPM com Nginx
FROM php:8.1-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    unzip \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    nginx

# Configurar o diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos do aplicativo
COPY . /var/www/html

# Copiar o arquivo de configuração do Nginx
COPY conf/nginx/nginx-site.conf /etc/nginx/conf.d/default.conf

# Instalar o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Executar o Composer
RUN composer install --no-dev --working-dir=/var/www/html

# Cachear configurações
RUN php artisan config:cache

# Cachear rotas
RUN php artisan route:cache

# Executar migrações
RUN php artisan migrate --force

# Configurar as permissões dos arquivos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# Expor a porta 80 para acesso externo
EXPOSE 80

# Iniciar o serviço do Nginx e do PHP-FPM
CMD service nginx start && php-fpm
