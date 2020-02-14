FROM 157239n/php_fpm7.4
LABEL vendor=""
RUN apt-get update && apt-get install -y mysql-client
COPY startup /startup
COPY .env /etc/environment
RUN mv /startup/virus.conf /etc/apache2/sites-available/virus.conf \
    && mv /startup/logcron /usr/local/bin/logcron \
    && mv /startup/delcron /usr/local/bin/delcron \
	&& a2dissite 000-default.conf \
	&& a2enmod rewrite \
	&& a2enmod remoteip \
	&& a2ensite virus.conf \
    && /startup/phpSettings.sh
WORKDIR /startup
CMD ["/startup/entry.sh"]
