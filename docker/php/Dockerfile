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
    opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# OPcache : évite de recompiler les scripts PHP à chaque requête (temps de réponse du serveur)
RUN printf 'opcache.enable=1\nopcache.memory_consumption=64\nopcache.max_accelerated_files=4000\nopcache.revalidate_freq=2\n' > /usr/local/etc/php/conf.d/opcache.ini

# Composer (dépendances PHP : PHPMailer)
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Activer mod_rewrite pour les .htaccess (URL propres)
RUN a2enmod rewrite headers expires deflate

# Config Apache : pointer sur public/ comme DocumentRoot
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Autoriser .htaccess dans public/
RUN sed -i '/<Directory \/var\/www\/html>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

WORKDIR /var/www/html
