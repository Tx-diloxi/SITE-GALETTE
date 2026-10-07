FROM php:8.2-apache

# Extensions nécessaires : PDO MySQL, mysqli, mbstring, intl
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    zip \
    unzip \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli \
    mbstring \
    intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Activer mod_rewrite pour les .htaccess (URL propres)
RUN a2enmod rewrite

# Config Apache : pointer sur public/ comme DocumentRoot
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Autoriser .htaccess dans public/
RUN sed -i '/<Directory \/var\/www\/html>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

WORKDIR /var/www/html
