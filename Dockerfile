FROM php:8.1-apache

# ODBCドライバのインストール
ENV ACCEPT_EULA=Y
RUN apt-get update && apt -y install apt-transport-https gnupg2
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add -
RUN curl https://packages.microsoft.com/config/debian/12/prod.list > /etc/apt/sources.list.d/mssql-release.list
RUN sed -i "s@deb \[arch=amd64,arm64,armhf signed-by=/usr/share/keyrings/microsoft-prod.gpg\] https://packages.microsoft.com/debian/12/prod bookworm main@deb \[arch=amd64,arm64,armhf\] https://packages.microsoft.com/debian/12/prod bookworm main@g" /etc/apt/sources.list.d/mssql-release.list
RUN mv -i /etc/apt/trusted.gpg.d/debian-archive-*.asc  /root/
RUN ln -s /usr/share/keyrings/debian-archive-* /etc/apt/trusted.gpg.d/
RUN apt-get update
RUN apt-get -y install msodbcsql18 unixodbc-dev
RUN apt-get -y install mssql-tools

# PHPドライバのインストール
RUN pecl install sqlsrv
RUN pecl install pdo_sqlsrv
RUN docker-php-ext-enable sqlsrv pdo_sqlsrv

COPY . /var/www/html/commaster

WORKDIR /var/www/html/commaster
