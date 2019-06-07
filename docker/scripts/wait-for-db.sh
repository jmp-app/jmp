# Filename:	wait-for-db.sh
# Description: Checks the docker mariadb database availability for a maximum of 30 times waiting 2s between until the database is available
# Call:	bash wait-for-db.sh
# Author:	Dominik Strässle
# Version:	1.0
# Date:	04/11/2019

count=0
while test "$(docker inspect --format='{{json .State.Health.Status}}' db)" != "\"healthy\"" && test ${count} -lt 30; do
    echo "Waiting for db to complete startup"
    sleep 2s
    count=$((count+1))
done
if test ${count} -eq 30; then
    echo "Waited too long for db to start. Exit now"
    exit 1
fi