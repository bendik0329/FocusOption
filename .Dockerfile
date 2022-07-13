FROM webdevops/php-apache-dev:centos-7-php56

EXPOSE 80 443

# Copy Composer binary from the Composer official Docker image
COPY --from=composer:1 /usr/bin/composer /usr/bin/composer

RUN echo short_open_tag = On >> /opt/docker/etc/php/php.ini


RUN mkdir -p /var/lib/php/session
RUN chmod 777 /var/lib/php/session

ENV PHP_DISPLAY_ERRORS 0
ENV PHP_DEBUGGER none
ENV PHP_MEMORY_LIMIT 1024M
ENV WEB_DOCUMENT_ROOT /app
ENV APP_ENV dev
ENV WEB_ALIAS_DOMAIN affiliatets.vm


WORKDIR /app
COPY ./site ./

#RUN composer install
#RUN npm install
#RUN npm run prod
#RUN php artisan optimize:clear
#RUN php artisan config:clear
#RUN php artisan cache:clear
#RUN php artisan view:clear




