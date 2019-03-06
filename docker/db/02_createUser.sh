#!/usr/bin/env bash
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" -e "DROP USER IF EXISTS '${DB_USERNAME}'@'%';";
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" -e "CREATE USER '${DB_USERNAME}'@'%';";
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" -e "GRANT SELECT, INSERT, UPDATE, DELETE ON ${DB_DATABASE}.* TO '${DB_USERNAME}'@'%' IDENTIFIED BY '${DB_PASSWORD}';";
