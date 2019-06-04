n ?= 2
# Doesn't work in powershell
dir ?="$(pwd)"
environment="jmp.postman_environment.json"
collection="jmp.postman_collection.json"

host="localhost"
protocol="http"
path="api"

target="jmp_prod"

help: ## Displays the makefile documenation.
		## Based on https://stackoverflow.com/a/47107132
	@sed -ne '/@sed/!s/## //p' $(MAKEFILE_LIST)

create-test-collection: ## Create a test collection for a specific host
        ## Don't use this script for local tests
        ## make create-test-collection host="example.com" protocol="https" path="test/api"
        ## Params:
        ##	1. host: Name of the host or ip-address. E.g. example.com
        ##	2. protocol: Protocol to query the api endpoints. Default is https
        ##	3. path: Path to the api. Default is none. E.g. api or backend/jmp
	bash docker/scripts/create_test_collection.sh $(host) $(protocol) $(path)

test-deployment: create-test-collection ## Test a specific deployment (host)
        ## Don't use this script for local tests
        ## make test-deployment host="example.com" protocol="https" path="test/api"
        ## Params:
        ##   1. host: Name of the host or ip-address. E.g. example.com
        ##   2. dir: project base directory. Default "$(pwd)"
        ##   3. n: number of test iterations. Default 2
	make test collection="${host}.postman_collection.json"


test-local: rm-volume wait-for-db ## Test the local development application
        ## make test dir="$(pwd)" n=5
        ## Params:
        ##   1. dir: project base directory. Default "$(pwd)"
        ##   2. n: number of test iterations. Default 2
	docker pull postman/newman
	docker run --network='host' -v "$(dir)/docker/newman/collections":/etc/newman -t  postman/newman run -e $(environment) -n $(n)  $(collection)

build-up:
	docker-compose up -d --build

build:
	cd api && \
	docker build -t $(target) .

create-container:
	docker create $(target)

run-container-local:
	cd api && \
	docker run --network jmp_default --env-file=db.env --env-file=.env -d $(target)

wait-for-db:
	bash docker/scripts/wait-for-db.sh

up:
	docker-compose up -d

rm-volume: down
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

format: ## Help comments are display with their leading whitespace. For
	## example, all comments in this snippet are aligned with spaces.
