n ?= 2
# Doesn't work in powershell
dir="$(pwd)"

help:
# Doesn't work in powershell
	cat Makefile

test:
# make test dir="$(pwd)" n=5
# Params:
#   1. dir: project base directory. Default "$(pwd)"
#   2. n: number of test iterations. Default 2
	docker pull postman/newman
	docker run --network='host' -v "$(dir)/docker/newman/collections":/etc/newman -t  postman/newman run -e jmp.postman_environment.json -n $(n)  jmp.postman_collection.json

build-up:
	docker-compose up -d --build
	docker exec app composer install

up:
	docker-compose up -d

rm-volume:
	docker-compose down
	docker volume rm jmp_dbdata
	docker-compose up -d --build

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
