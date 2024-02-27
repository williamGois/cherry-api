# Usar a imagem oficial do PHP com Apache
FROM php:8.1-apache

# Instalar extensões do PDO e PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql
    
# Instalar extensões do PHP
RUN apt-get update && apt-get install -y \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        zip \
        unzip \
        git \
        curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Habilitar o mod_rewrite para o Apache
RUN a2enmod rewrite

# Definir o diretório raiz do Apache para a pasta public do Lumen
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Atualizar o arquivo de configuração do Apache para mudar o document root
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Definir o diretório de trabalho
WORKDIR /var/www/html

# Copiar os arquivos do projeto Lumen para o container
COPY . /var/www/html

# Instalar as dependências do Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install

# Alterar as permissões dos diretórios de armazenamento e cache
RUN chown -R www-data:www-data /var/www/html/storage

# Expor a porta 80
EXPOSE 80
