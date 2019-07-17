#!/usr/bin/env bash
# Filename:	create_test_collection.sh
# Description: Create a customized test collection based on the project's default collection
# Note: The script has to be executed in the project root
# Call:	bash create_test_collection.sh host protocol path
#		host: Name of the host or ip-address. E.g. example.com
#		protocol: Protocol to query the api endpoints. Default is https
#       path: Path to the api. Default is none. E.g. api or backend/jmp
# Author:	Dominik Strässle
# Version:	1.0
# Date:	04/11/2019
#
if [[ -z "$1" ]]; then
    echo "Host is required as first argument"
    echo "Call:	bash create_test_collection.sh host protocol path"
    exit 1
fi
host="$1"

if [[ -z "$2" ]]; then
    protocol="https"
else
    protocol="$2"
fi

if [[ -z "$3" ]]; then
    path=""
    path_raw=""
else
    path="\"$3\","
    path_raw="$3/"
fi

cd docker/newman/collections
cp jmp.postman_collection.json "${host}.postman_collection.json"
sed -i -- "s|jmp|${host}|g" "${host}.postman_collection.json"
sed -i -- "s/\"localhost\"/\"${host}\"/g" "${host}.postman_collection.json"
sed -i -- "s|http://localhost/api/|${protocol}://${host}/${path_raw}|g" "${host}.postman_collection.json";
sed -i -- "s|localhost/api/|${protocol}://${host}/${path_raw}|g" "${host}.postman_collection.json"
sed -i -- "s|\"http\"|\"${protocol}\"|g" "${host}.postman_collection.json"
sed -i -- "s|\"api\",|${path}|g" "${host}.postman_collection.json"
sed -i -- "s/\"protocol\": \"${protocol}\",//g" "${host}.postman_collection.json"
sed -i -- "s/\"host\"/\"protocol\": \"${protocol}\",\n\"host\"/g" "${host}.postman_collection.json"
