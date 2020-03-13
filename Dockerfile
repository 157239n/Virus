FROM 157239n/php_fpm7.4
LABEL vendor=""
WORKDIR /
RUN apt-get update && apt-get install -y mysql-client php7.4-xml php7.4-mbstring \
    && curl getcomposer.org/installer | php \
    && mv /composer.phar /usr/local/bin/composer
COPY startup /startup
COPY .env /etc/environment
RUN mv /startup/000-default.conf /etc/apache2/sites-available/000-default.conf \
    && mv /startup/logcron /usr/local/bin/logcron \
    && mv /startup/delcron /usr/local/bin/delcron \
	&& a2enmod rewrite \
	&& a2enmod remoteip \
    && /startup/phpSettings.sh \
    && printf "\nexport PATH=$PATH:/var/www/html/vendor/bin" >> /root/.bashrc
WORKDIR /startup
CMD ["/startup/entry.sh"]
