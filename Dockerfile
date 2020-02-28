FROM 157239n/php_fpm7.4
LABEL vendor=""
WORKDIR /
RUN apt-get update && apt-get install -y mysql-client php7.4-xml php7.4-mbstring \
    && curl getcomposer.org/installer | php \
    && mv /composer.phar /usr/local/bin/composer
COPY startup /startup
COPY .env /etc/environment
RUN mv /startup/virus.conf /etc/apache2/sites-available/virus.conf \
    && mv /startup/logcron /usr/local/bin/logcron \
    && mv /startup/delcron /usr/local/bin/delcron \
	&& a2dissite 000-default.conf \
	&& a2enmod rewrite \
	&& a2enmod remoteip \
	&& a2ensite virus.conf \
    && /startup/phpSettings.sh \
    && printf "\nexport PATH=$PATH:/var/www/virus/vendor/bin" >> /root/.bashrc
WORKDIR /startup
CMD ["/startup/entry.sh"]
