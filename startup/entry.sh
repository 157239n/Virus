#!/bin/bash

mkdir /var/log/cron
mkdir /data
mkdir /data/users
mkdir /data/viruses
mkdir /data/attacks
mkdir /data/logs
touch /var/log/apache2/virusError.log
chown -R www-data:www-data /data
chown -R www-data:www-data /var/www/virus
chown -R www-data:www-data /var/log/apache2

/startup/runPhpFpm.sh
cat /etc/environment | /startup/setupFpmEnv.sh

crontab /startup/cron.txt
cron -f
