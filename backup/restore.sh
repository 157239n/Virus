#!/bin/bash

cat 0.sql | mysql -u"${MYSQL_USER}" -p"${MYSQL_PASSWORD}" -h"${MYSQL_HOST}" ${MYSQL_DATABASE}
