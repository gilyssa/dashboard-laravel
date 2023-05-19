#!/bin/bash

# Inicie o serviço do Nginx em segundo plano
nginx -g "daemon off;" &

# Inicie o serviço do PHP-FPM em primeiro plano
php-fpm
