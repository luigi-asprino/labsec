FROM php:7.4-apache

RUN apt-get update && apt-get install -y gpg

RUN chown -R www-data:www-data /var/www/html && \
	chmod -R 754 /var/www/html/

RUN curl -s -o /tmp/go-pear.phar http://pear.php.net/go-pear.phar && \
    echo '/usr/bin/php /tmp/go-pear.phar "$@"' > /usr/bin/pear && \
    chmod +x /usr/bin/pear && \
    pear install mail Net_SMTP

RUN a2enmod rewrite

VOLUME /var/www/html
