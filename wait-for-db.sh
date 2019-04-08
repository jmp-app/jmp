#!/usr/bin/env sh

count=0
while test "$(docker inspect --format='{{json .State.Health.Status}}' db)" = "\"healthy\"" && test ${count} -lt 10; do
    echo "waiting for db to complete startup"
    sleep 2s
    count=$((count+1))
done
if test ${count} -eq 10; then
    exit 1 "Waited too long for db to start"
fi