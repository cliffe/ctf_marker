FROM php:5.6-apache

RUN docker-php-ext-install mysqli

ADD . /var/www/html/

