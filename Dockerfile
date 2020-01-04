FROM 157239n/php_fpm
LABEL vendor=""
COPY startup /startup
COPY env /startup/env
RUN apt-get install -y mysql-client \
    && mv /startup/virus.conf /etc/apache2/sites-available/virus.conf \
	&& a2dissite 000-default.conf \
	&& a2enmod rewrite \
	&& a2ensite virus.conf \
    && /startup/phpSettings.sh
WORKDIR /startup
CMD ["/startup/entry.sh"]
