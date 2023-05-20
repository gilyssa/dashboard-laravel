FROM php:7.4-fpm

# Instalar dependências
RUN apt-get update && apt-get install -y \
    unzip \
    libpq-dev \
    libzip-dev

# Instalar extensões PHP necessárias
RUN docker-php-ext-install pdo pdo_pgsql zip

# Copiar arquivos do aplicativo para o contêiner
COPY . /var/www/html

# Definir o diretório de trabalho
WORKDIR /var/www/html

# Copiar o arquivo de configuração nginx-site.conf para o diretório de configuração do Nginx
COPY conf/nginx/nginx-site.conf /etc/nginx/conf.d/default.conf

# Copiar o script de implantação para o contêiner
COPY scripts/00-laravel-deploy.sh /var/www/html/scripts/00-laravel-deploy.sh

# Dar permissão de execução ao script de implantação
RUN chmod +x /var/www/html/scripts/00-laravel-deploy.sh

# Executar o script de implantação durante a construção da imagem
RUN /var/www/html/scripts/00-laravel-deploy.sh

# Expor a porta 80 para acesso HTTP
EXPOSE 80

# Comando para iniciar o serviço do PHP-FPM e do Nginx
CMD ["start.sh"]
