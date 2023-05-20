# Imagem base
FROM php:8.1-fpm

# Atualizar lista de pacotes e instalar dependências
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        nginx \
        unzip

# Remover configuração padrão do Nginx
RUN rm /etc/nginx/sites-enabled/default

# Copiar arquivo de configuração do Nginx
COPY conf/nginx/nginx-site.conf /etc/nginx/conf.d/default.conf

# Configurar diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos do aplicativo para o contêiner
COPY . /var/www/html

# Descompactar arquivos ZIP, se necessário
# EXEMPLO: RUN unzip /var/www/html/arquivo.zip -d /var/www/html/

# Permissões de pasta, se necessário
# EXEMPLO: RUN chown -R www-data:www-data /var/www/html

# Copiar arquivo start.sh para o contêiner
COPY start.sh /start.sh

# Permissões para o script start.sh
RUN chmod +x /start.sh

# Comandos para iniciar os serviços do PHP-FPM e Nginx
CMD service php8.1-fpm start && /start.sh
