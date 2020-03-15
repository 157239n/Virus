#!/bin/bash

sed -i "s/post_max_size = 8M/post_max_size = 60M/g" /etc/php/7.4/fpm/php.ini
sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 55M/g" /etc/php/7.4/fpm/php.ini
sed -i "s/max_file_uploads = 20/max_file_uploads = 50/g" /etc/php/7.4/fpm/php.ini
