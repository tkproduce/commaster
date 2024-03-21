FROM php:7.2-apache
COPY . /var/www/html/
RUN apt update && apt -y install