FROM php:7.4.5-apache
COPY config_files/php.ini /usr/local/etc/php/
ADD config_files/site_conf.conf /etc/apache2/sites-enabled/000-default.conf
#enable apache mods
RUN a2enmod rewrite
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        default-mysql-client\
        git \
        vim \
        mc
RUN docker-php-ext-install -j$(nproc) iconv
RUN docker-php-ext-configure gd --with-jpeg=/usr/include/  --with-freetype=/usr/include/
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-enable mysqli
RUN docker-php-ext-install -j$(nproc) gd
RUN rm -rf /var/lib/apt
COPY --chown=www-data:www-data src/ /var/www/html/
RUN php composer install
