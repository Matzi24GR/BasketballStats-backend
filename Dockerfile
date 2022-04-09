FROM php:8.0-apache
ENV DB_HOST='db'
ENV DB_NAME='esakeDB'
ENV DB_USERNAME='root'
ENV DB_PASSWORD=''
WORKDIR /var/www/html
COPY ./php/ /var/www/html
RUN docker-php-ext-install mysqli pdo pdo_mysql
EXPOSE 80
