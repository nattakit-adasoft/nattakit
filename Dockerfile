FROM naleruto/ada-webserver:01.00.00

COPY ./apache2/apache2.conf /etc/apache2/apache2.conf
COPY ./app /var/www/html