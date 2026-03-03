# Usa PHP 8.2 com Apache
FROM php:8.2-apache

# Instala dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_mysql zip

# Habilita mod_rewrite
RUN a2enmod rewrite

# Define pasta public como raiz
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copia arquivos do projeto
WORKDIR /var/www/html
COPY . .

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Instala dependências PHP
RUN composer install --no-dev --optimize-autoloader

# Instala dependências JS e faz build
RUN npm install && npm run build

# Permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Expõe porta
EXPOSE 80

# Script de inicialização
CMD php artisan config:clear && \
    php artisan cache:clear && \
    php artisan config:cache && \
    php artisan migrate --force && \
    apache2-foreground