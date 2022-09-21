FROM php:7.3-apache

COPY ./apache2/apache2.conf /etc/apache2/apache2.conf
COPY ./app .