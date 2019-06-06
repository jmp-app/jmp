.DEFAULT_GOAL:=help

# Tests
n ?= 2
dir ?="$(pwd)"
environment="jmp.postman_environment.json"
collection="jmp.postman_collection.json"
host="localhost"
protocol="http"
path="api"

# Docker
target="jmp_prod"

##@ Tests

.PHONY: create-test-collection test-deployment test-local

create-test-collection: ## Create a test collection for a specific host
## Params:
##	1. host: Name of the host or ip-address. E.g. example.com
##	2. protocol: Protocol to query the api endpoints. Default is https
##	3. path: Path to the api. Default is none. E.g. api or backend/jmp
	bash docker/scripts/create_test_collection.sh $(host) $(protocol) $(path)

test-deployment: create-test-collection ## Test a specific deployment (host)
## Params:
##   1. host: Name of the host or ip-address. E.g. example.com
##   2. dir: project base directory. Default "$(pwd)"
##   3. n: number of test iterations. Default 2
	make test collection="${host}.postman_collection.json"

test-local: ## Test the local development application
## Params:
##   1. dir: project base directory. Default "$(pwd)"
##   2. n: number of test iterations. Default 2
	docker pull postman/newman
	docker run --network='host' -v "$(dir)/docker/newman/collections":/etc/newman -t  postman/newman run -e $(environment) -n $(n)  $(collection)

##@ docker-compose

.PHONY: build-up-dev build-up-prod up-dev up-prod rm-volume-prod down-dev down-dev down-prod

build-up-dev: ## Build and starts the development services
	docker-compose up -d --build

build-up-prod: ## Build and starts the prod services
	docker-compose -f docker-compose.prod.yml up -d --build

up-dev: ## Start the development services
	docker-compose up -d

up-prod: ## Start the prod services
	docker-compose -f docker-compose.prod.yml up -d

rm-volume-prod: ## Remove volumes
	docker-compose -f docker-compose.prod.yml down -v

rm-volume-dev: ## Remove volumes
	docker-compose down -v

down-dev: ## Shutdown development services
	docker-compose down

down-prod: ## Shutdown prod services
	docker-compose -f docker-compose.prod.yml down

##@ Docker

.PHONY: build-image-prod create-container-prod start-container-prod run-prod

build-image-prod: ## Build an image from the prod Dockerfile
## Params:
##   1. target: Name of the image
	cd api && \
	docker build -t $(target) -f prod.Dockerfile .

create-container-prod: ## Create a new container from the jmp image
## Params:
##   1. target: Name of the image
	docker create $(target)

run-container-prod: ## Run the prod container
	cd api && \
	docker run --network jmp_default --env-file=db.env --env-file=.env -d $(target)

##@ Miscellaneous

.PHONY: cp-env$

cp-env: ## Copy environment variables
	cp db.env.example db.env
	cp api/.env.example api/.env
	cp api/db.env.example api/db.env

##@ Vue

.PHONY: vue-build vue-watch

vue-build: ## Build vue for deployment
	cd vue && \
	npm install && \
	npm run build

vue-watch: ## Run vue development server with hot reloads
	cd vue && \
	npm run watch

wait-for-db:
	bash docker/scripts/wait-for-db.sh

##@ Help

.PHONY: help

help:  ## Display this help
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m \033[35m<ARGUMENTS>\033[0m\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)
