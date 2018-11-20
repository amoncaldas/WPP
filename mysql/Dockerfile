# Author: Amon Caldas <amon@openrouteservice.org>
# Version 0.2
# 26.01.2018

FROM mysql:5.7

ARG IMPORT_DB=false
ARG MYSQL_ROOT_PASS=admin

# we use the MYSQL_ROOT_PASS arg to set the mysql expected env variable MYSQL_ROOT_PASSWORD
ENV MYSQL_ROOT_PASSWORD=$MYSQL_ROOT_PASS

COPY ["db-backup.sql", "/db-backup/dump.sql"]

# The .sh files placed at /docker-entrypoint-initdb.d/ will be ran at the first container execution
RUN if ($IMPORT_DB == true); \
    then \
    echo "mysql -uroot -p${MYSQL_ROOT_PASS} -e \"CREATE DATABASE wordpress\" \n\
    mysql -uroot -p${MYSQL_ROOT_PASS} wordpress < /db-backup/dump.sql" \
    > /docker-entrypoint-initdb.d/import.sh; \
    fi
