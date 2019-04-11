#!/usr/bin/env sh

count=0
while test "$(docker inspect --format='{{json .State.Health.Status}}' db)" != "\"healthy\"" && test ${count} -lt 10; do
    echo "Waiting for db to complete startup"
    sleep 2s
    count=$((count+1))
done
if test ${count} -eq 10; then
    echo "Waited too long for db to start. Exit now"
    exit 1
fi