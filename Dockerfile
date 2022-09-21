FROM php:7.3-apache

# Install Dependencies
RUN apt-get update && apt-get install -y locales unixodbc


COPY ./apache2/apache2.conf /etc/apache2/apache2.conf
COPY ./app .