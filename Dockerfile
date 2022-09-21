FROM php:7.3-apache
# Install Dependencies
RUN apt-get update && apt-get install -y locales unixodbc libgss3 odbcinst \
    devscripts debhelper dh-exec dh-autoreconf libreadline-dev libltdl-dev \
    unixodbc-dev wget unzip \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo \
    && echo "en_US.UTF-8 UTF-8" > /etc/locale.gen

# Add Microsoft repo for Microsoft ODBC Driver 17 for Linux
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
    && curl https://packages.microsoft.com/config/debian/9/prod.list > /etc/apt/sources.list.d/mssql-release.list

RUN apt-get update && ACCEPT_EULA=Y apt-get install -y \
    apt-transport-https \
    msodbcsql17

# Enable the php extensions.
RUN pecl install pdo_sqlsrv-5.6.1 sqlsrv-5.6.1 \
    && docker-php-ext-enable pdo_sqlsrv sqlsrv \
    && docker-php-ext-install bcmath
# allow .htaccess with RewriteEngine
RUN a2enmod rewrite
#install some base extensions
RUN apt-get install -y \
        libzip-dev \
        zip \
  && docker-php-ext-install zip

  ENV TZ=Asia/Bangkok
  RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

COPY ./apache2/apache2.conf /etc/apache2/apache2.conf
COPY ./app .