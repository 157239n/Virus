#!/bin/bash

mv 0.sql 1.sql -f
mysqldump -u"${MYSQL_USER}" -p"${MYSQL_PASSWORD}" -h"${MYSQL_HOST}" ${MYSQL_DATABASE} > 0.sql 2>/dev/null

