FROM 157239n/php_fpm
LABEL vendor=""
COPY startup /startup
COPY env /startup/env
RUN mv /startup/virus.conf /etc/apache2/sites-available/virus.conf \
	&& mv /startup/log /usr/local/bin/log -f \
	&& mv /startup/dellog /usr/local/bin/dellog -f \
	&& a2dissite 000-default.conf \
	&& a2enmod rewrite \
	&& a2ensite virus.conf
WORKDIR /startup
ENTRYPOINT ["/startup/entry.sh"]
