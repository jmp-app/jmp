help:
	# Don't works in powershell
	cat Makefile

test:
	# Argument DIR with the project base directory required
	docker pull postman/newman
	docker run --network='host' -v "$(DIR)\docker\newman\collections":/etc/newman -t  postman/newman run -e jmp.postman_environment.json -n 2  jmp.postman_collection.json

build-up:
	docker-compose up -d --build
	docker exec app composer install

up:
	docker-compose up -d

vue-build:
	cd vue
	npm install
	npm run build

vue-run-watch:
	npm run watch
