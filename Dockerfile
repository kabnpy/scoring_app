# Dockerfile
#
# this Dockerfile sets up an apache web server with php 8.2 (or a recent stable version)
# and installs necessary php extensions for connecting to mysql (pdo_mysql).

# use an official php image with apache as the base
FROM php:8.2-apache

# set the working directory inside the container
WORKDIR /var/www/html

# install php extensions required for mysqL (pdo)
RUN docker-php-ext-install pdo pdo_mysql

# enable Apache's rewrite module
RUN a2enmod rewrite

# copy the custom apache virtual host configuration
# this will overwrite the default apache config for port 80
COPY apache_config/000-default.conf /etc/apache2/sites-available/000-default.conf

# enable the custom site configuration (which is already named 000-default.conf)
# and disable the default one if it exists (though COPY usually handles this).
# this command ensures our custom config is active.
RUN a2ensite 000-default.conf

# copy the entire application source code from the host into the container.
# we copy to /var/www/html, and then our apache config points to /var/www/html/public.
COPY . /var/www/html

# set appropriate permissions for the web server to read and write files
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# expose port 80
EXPOSE 80
