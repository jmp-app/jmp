help:
# Don't works in powershell
	cat Makefile

test:
# Argument DIR with the project base directory required e.g. "$(pwd)"
	docker pull postman/newman
	docker run --network='host' -v "$(DIR)/docker/newman/collections":/etc/newman -t  postman/newman run -e jmp.postman_environment.json -n 2  jmp.postman_collection.json

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

vue-build:
	cd vue && \
	npm install && \
	npm run build

vue-watch:
	cd vue && \
	npm run watch
