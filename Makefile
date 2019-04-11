n ?= 2
# Doesn't work in powershell
dir ?="$(pwd)"
environment="jmp.postman_environment.json"
collection="jmp.postman_collection.json"

host="localhost"
protocol="http"
path="api"

help:
# Doesn't work in powershell
	cat Makefile

create-test-collection:
# make create-test-collection host="example.com" protocol="https" path="test/api"
# Params:
#   1. host: Name of the host or ip-address. E.g. example.com
#	2. protocol: Protocol to query the api endpoints. Default is https
#   3. path: Path to the api. Default is none. E.g. api or backend/jmp
	./docker/newman/collections/create_test_collection.sh $(host) $(protocol) $(path)

test:
# make test dir="$(pwd)" n=5
# Params:
#   1. dir: project base directory. Default "$(pwd)"
#   2. n: number of test iterations. Default 2
	docker pull postman/newman
	docker run --network='host' -v "$(dir)/docker/newman/collections":/etc/newman -t  postman/newman run -e $(environment) -n $(n)  $(collection)

build-up:
	docker-compose up -d --build
	docker exec app composer install

up:
	docker-compose up -d

rm-volume:
	docker-compose down
	docker volume rm jmp_dbdata
	docker-compose up -d

down:
	docker-compose down

cp-env:
# Set environment variables
	cp db.env.example db.env
	cp api/.env.example api/.env
	cp api/db.env.example api/db.env

vue-build:
	cd vue && \
	npm install && \
	npm run build

vue-watch:
	cd vue && \
	npm run watch
