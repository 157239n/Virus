#!/bin/bash

env>$(dirname $0)/output.log
mv $(dirname $0)/0.sql $(dirname $0)/1.sql -f
mysqldump -u"${MYSQL_USER}" -p"${MYSQL_PASSWORD}" -h"${MYSQL_HOST}" ${MYSQL_DATABASE} > $(dirname $0)/0.sql 2>/dev/null

