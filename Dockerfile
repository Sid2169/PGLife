FROM php:8.2-apache

# mysqli is required by the app; headers lets us set security headers.
RUN docker-php-ext-install mysqli \
    && a2enmod rewrite headers

# The stock image sets "AllowOverride None" for /var/www, which would ignore our
# .htaccess hardening (and expose pglife.sql). Enable it for the web root.
RUN sed -ri '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Sensible production PHP settings (can be overridden by env/config).
RUN { \
        echo 'display_errors = Off'; \
        echo 'log_errors = On'; \
        echo 'error_log = /dev/stderr'; \
        echo 'expose_php = Off'; \
    } > /usr/local/etc/php/conf.d/zz-pglife.ini

WORKDIR /var/www/html

COPY . /var/www/html/

# Install the entrypoint that maps Apache to the platform-provided $PORT, and
# keep a copy of it out of the web root.
COPY docker-entrypoint.sh /usr/local/bin/pglife-entrypoint
RUN chmod +x /usr/local/bin/pglife-entrypoint \
    && rm -f /var/www/html/docker-entrypoint.sh \
    && chown -R www-data:www-data /var/www/html

ENTRYPOINT ["pglife-entrypoint"]
CMD ["apache2-foreground"]
