#!/bin/bash

mkdir /data
mkdir /data/users
mkdir /data/viruses
mkdir /data/attacks
chown -R www-data:www-data /data
chown -R www-data:www-data /var/www/virus
chown -R www-data:www-data /var/log/apache2
/startup/runPhpFpm.sh
cat /startup/env/site | /startup/setupFpmEnv.sh
crontab /startup/cron.txt
cron -f

#while true; do
#  php /var/www/virus/scan.php
#  sleep 30
#done

#tail -f /dev/null
