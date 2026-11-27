FROM php:8.4-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN a2enmod rewrite

RUN echo "upload_max_filesize = 12M\npost_max_size = 12M" > /usr/local/etc/php/conf.d/uploads.ini

COPY . /var/www/html/
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf