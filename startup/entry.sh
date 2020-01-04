#!/bin/bash

mkdir /data
mkdir /data/users
mkdir /data/viruses
mkdir /data/attacks
touch /var/log/apache2/virusError.log
chown -R www-data:www-data /data
chown -R www-data:www-data /var/www/virus
chown -R www-data:www-data /var/log/apache2

/startup/runPhpFpm.sh
cat /startup/env/site | /startup/setupFpmEnv.sh
cp /env/php_fpm /etc/environment

crontab /startup/cron/cron.txt
cron -f
