#!/bin/bash

# Copiar o arquivo de configuração nginx-site.conf para o diretório de configuração do Nginx
cp conf/nginx/nginx-site.conf /etc/nginx/conf.d/default.conf

# Iniciar o serviço do Nginx
nginx -g "daemon off;"
