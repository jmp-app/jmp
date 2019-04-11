# Getting Started

Make sure everything is running as explained in [README.md](../README.md).

# Table of Contents:
- [Getting Started](#getting-started)
- [Code Overview](#code-overview)
  * [Directory Structure](#directory-structure)
  * [Slim specific classes](#slim-specific-classes)
  * [Authentication](#authentication)
    + [Usage](#usage)
    + [Settings](#settings)
    + [Implementation](#implementation)
    + [Login](#login)
    + [Registration](#registration)
  * [Validation](#validation)
    + [Translations](#translations)
- [Code Style](#code-style)
  * [Optional](#optional)
    + [Usage](#examples)
  * [Array Conversion](#array-conversion)
  * [Type declarations & phpdoc](#type-declarations--phpdoc)
  * [SQL](#sql)
- [Developing a new route](#developing-a-new-route)
  * [Api specification](#api-specification)
  * [Create Tests](#create-tests)
  * [Service & Model](#service--model)
    + [Create a new service class](#create-a-new-service-class)
    + [Create a new model](#create-a-new-model)
  * [Controller](#controller)
    + [Create a new controller class](#create-a-new-controller-class)
  * [Route](#route)
  * [Verify Tests](#verify-all-tests)
- [Integration Tests](#integration-tests)
    * [Create a new test](#create-a-new-test)
    * [Running Tests](#running-tests)
    * [Travis CI Build Status](#travis-ci-and-build-status)
- [Deployment](#deployment)
    * [Apache](#apache)
        + [Test your deployed backend](#test-your-deployed-backend)
        + [Create and run customized test script](#create-and-run-customized-test-script)


<small><i><a href='http://ecotrust-canada.github.io/markdown-toc/'>Table of contents generated with markdown-toc</a></i></small>

# Code Overview
## Directory Structure
* [`src/`](../api/src) Source directory. Not public
    * [`jmp/`](../api/src/jmp) Application directory
        * [`Controllers/`](../api/src/jmp/Controllers) Controllers to handle requests and build the responses
        * [`Middleware/`](../api/src/jmp/Middleware) Custom [Middlewares](https://www.slimframework.com/docs/v3/concepts/middleware.html)
        * [``Models/``](../api/src/jmp/Models) Raw data objects with the functionality to convert to an array
        * [``Services/``](../api/src/jmp/Services) Business logic and database communication
        * [``Utils/``](../api/src/jmp/Utils) Utility classes

## Slim specific classes
* Private
    * [``dependencies.php``](../api/src/dependencies.php) Initialize services, controllers, middlewares and add them to the slim container
    * [``settings.php``](../api/src/settings.php) Slim settings and required settings for the dependencies
    * [``routes.php``](../api/src/routes.php) Register routes with their middlewares here
* Public
    * [``index.php``](../api/public/index.php) Application entry point


## Authentication

### Usage
We use [JWT](https://jwt.io/) for authentication. The token must be submitted in the authorization header using the bearer schema.
Example:  

```http_request
Authorization: Bearer <token>
```

Read more at the [api specification](api-v1.md#authentication).
### Settings
To adjust the settings, look here:
 * In [settings.php](../api/src/settings.php)
 * [.env](../api/.env) and [.env docs](dotenv.md)

### Implementation
The submitted token is decoded by the [jwt-middleware](https://github.com/tuupola/slim-jwt-auth). The custom [authentication middleware](../api/src/jmp/Middleware/AuthenticationMiddleware.php) checks the permissions of the enquirer using the `sub` entry of the decoded token. If the enquirer has got the right permissions for the route, he can pass. If no token is supplied or if the supplied token is invalid, a 401 is returned. In case the enquirer just hasn't got the required permissions, a 403 is returned.
### Login
To receive a new token, the user have to call the [login route](api-v1.md#login). If the username and the password are valid, a new token is generated with the following service class [Auth.php](../api/src/jmp/Services/Auth.php). The password is hashed using [`password_hash`](https://secure.php.net/manual/en/function.password-hash.php) with the `PASSWORD_DEFAULT` option before it is stored in the database. To verify a password at a login, [`password_verify`](https://secure.php.net/manual/en/function.password-verify.php) is used.
### Registration
Only administrators can register new users. To register a new user, the [register route](api-v1.md#create-user) has to be called.

## Validation
Every user input has to be validated before it is processed.

All validations are stored in [validation.php](../api/src/validation.php) and use the [RespectValidation](https://github.com/Respect/Validation) validation engine. The stored validations have to be submitted to the [validation middleware](https://github.com/DavidePastore/Slim-Validation) of the corresponding route in [routes.php](../api/src/routes.php).

**Example:**
```php
->add(new Validation($container['validation']['getUser']))
```
See more at [Routes](#route).

### Translations
In case the automatic generated error messages don't fit your requirements or hold sensible data like a password, you can add translations as described in [Translate errors](https://github.com/DavidePastore/Slim-Validation#translate-errors).

Add the translations to [validation.php](../api/src/validation.php) and to the middleware as second parameter.
**Example:**
```php
->add(new Validation(
    $this->getContainer()['validation']['login'],
    $this->getContainer()['validation']['loginTranslation']
))
```
# Code Style

It's important, that everyone complies with the following rules.

**Improvements:**
If you recognize code, which doesn't comply with these rules, just correct them. 

## Optional
In this application we use the [`Optional`](../api/src/jmp/Utils/Optional.php) very often.

In php it's possible to let a method return `mixed` types (e.g. `User|bool`) or null able objects, but we decided to not use these possibilities.

When a method doesn't get the expected value, in other words, the execution fails, then we return an `Optional` instead of a `null` or `false` value.

### Examples
**On success:**
```php
return Optional::success('the data to return, could be of any type');
```
**On failure:**
```php
return Optional::failure();
```

**Handle the returned Optional:**
```php
$optional = methodWhichMayFail();
if ($optional->isSuccess()) {
    // success
    $data = $optional->getData();
} else {
//failure
}
```
**Vice versa:**
```php
$optional = methodWhichMayFail();
if ($optional->isFailure()) {
    // failure
} else {
    // success
    $data = $optional->getData();
}
```

## Array Conversion
The response object of the slim framework offers a method called `withJson`. This method converts an associative array to JSON.
Because the php cast functionality doesn't comply with our requirements to cast model objects to associative arrays, we use the following util and interface:

**[ArrayConvertable](../api/src/jmp/Models/ArrayConvertable.php) & [ArrayConvertableTrait](../api/src/jmp/Models/ArrayConvertableTrait.php):**
Every model has to implement this interface and use this trait. It is necessary to properly convert the models into arrays.


**[Converter](../api/src/jmp/Utils/Converter.php):**
This util is used to convert a model object or  a list of model objects properly to an associative array.
Use it in the controller as shown in this examples:
```php
return $response->withJson(Converter::convert($modelObject));
```
or
```php
return $response->withJson(Converter::convertArray($list));
```

## Type declarations & phpdoc
Everywhere it is possible we use object oriented php. So every method signature has to use the [php type declarations](https://secure.php.net/manual/en/functions.arguments.php#functions.arguments.type-declaration). If you try to call a method and pass arguments of another type as declared, then php will throw a [TypeError](https://secure.php.net/manual/en/class.typeerror.php).

Also, every method has to be documented with phpdoc. A short summary or description of the method and a documented signature is sufficient.

**Example (from [UserService.php](../api/src/jmp/Services/UserService.php)):**
```php
/**
 * Select a user by its id
 * @param int $userId
 * @return Optional containing a User if successfull
 */
public function getUserByUserId(int $userId): Optional
```

## SQL
The services handle the business logic as well as the whole database communication. At the moment there isn't a separated Persistence Layer with e.g. Data Access Objects (DAOs).

That the services don't become a mess, all SQL statements have to be coded with the following rules:
* Use the heredoc syntax:
```php
$sql = <<<SQL
# SQL
SQL;
```
* Write every sql keyword upper-case
* Use the **`AS`** keyword to change column names from the sql underscore style to the camel case style. E.g. 
```SQL
SELECT user_id AS userId ...
```
* Use the following for optional parameters (Examples in [EventService.php](../api/src/jmp/Services/EventService.php)): 
```SQL
... WHERE (:optionalId IS NULL OR id = :optionalId)
```
* A specific service doesn't query tables to which the service doesn't belong unless it's a join
* Always use **[prepared statements](https://secure.php.net/manual/de/pdo.prepared-statements.php)**
* Use `COALESCE` for updating an entry with optional parameters (Example from [EventService.php](../api/src/jmp/Services/EventService.php)):
````sql
UPDATE `jmp`.`event` e
SET e.`title` = COALESCE(:title, e.`title`)
WHERE e.`id` = :id
````
and use the `bindValue` method to set the parameters:
````php
$stmt->bindValue(':title', $params['title'], PDO::PARAM_STR);
````


# Developing a new route
This guide will take you through the most important steps to develop a new route.
Make sure you've read [Code Style](#code-style) before reading the following guide.

## Api specification
First of all the new route has to be documented in the [api-specification](api-v1.md).

## Create tests
Then you have to create tests for the new route as documented in [Create a new test](#create-a-new-test).

## Service & Model
Routes return JSON mostly containing one or a list of model objects. So you have to [create a new model](#create-a-new-model) if the required doesn't exist yet. The model is just a raw data object with the functionality to convert properly to an array.

Then you need a service to communicate with the database and to handle the business logic.
[Create a new service class](#create-a-new-service-class) if no appropriate exists yet, otherwise add the required method/s to the existing service class.

### Create a new service class
If no useful service class already exists, you have to create a new one.

The service must
* have a `ContainerInterface` constructor parameter
* hold all dependencies as private attributes
* have all dependencies set inside the constructor
* be added to the slim container inside [dependencies.php](../api/src/dependencies.php)

Check out already existing services as examples. [Services](../api/src/jmp/Services) 

### Create a new model
If the required model doesn't already exist, you have to create a new one.

The model must
* have all columns (as in the database) as public attributes
* have a constructor with one array as parameter
* set all attributes (except the ones which are foreign keys in the database) by the values of the array inside the constructor
* implement the [ArrayConvertable Interface](../api/src/jmp/Models/ArrayConvertable.php)
* use the [ArrayConvertable Trait](../api/src/jmp/Models/ArrayConvertableTrait.php)

Check out already existing model as examples. [Models](../api/src/jmp/Models) 

## Controller
For each route a specific controller method exists. A controller class itself holds methods handling similar subjects. So if no appropriate controller already exists, a [new controller has to be created](#create-a-new-controller-class).

A controller is called by the associated method in the [route.php](../api/src/routes.php) script. The controller is only called after the validation and the authentication passed successful. A controller has to call the required service and has to build the response dependent on the return value of the service. More information about responses and the error responses can be found in the [api specification](api-v1.md).

### Create a new controller class
If no appropriate controller exists, you have to create a new one.

The controller must
* have a `ContainerInterface` constructor parameter
* hold all dependencies as private attributes
* have all dependencies set inside the constructor

Check out already existing controllers as examples. [Controllers](../api/src/jmp/Controllers) 

## Route
Now the new route has to be registered in [route.php](../api/src/routes.php).
It's very important, that the middlewares are added in the **right order** and with the **right configuration**.

1. [ValidationErrorResponseBuilder](../api/src/jmp/Middleware/ValidationErrorResponseBuilder.php)
2. Validation Middleware
    1. Use the right validation settings. Add them to [validation.php](../api/src/validation.php)
3. [AuthenticationMiddleware](../api/src/jmp/Middleware/AuthenticationMiddleware.php)
    1. Set the right [PermissionLevel](../api/src/jmp/Utils/PermissionLevel.php) as noted in the [api specification](api-v1.md) of your route
4. JWT Middleware

**Example:**
```php
$this->get('/users/{id:[0-9]+}', UsersController::class . ':getUser')
    ->add(new ValidationErrorResponseBuilder())
    ->add(new Validation($container['validation']['getUser']))
    ->add(new AuthenticationMiddleware($container, \jmp\Utils\PermissionLevel::ADMIN))
    ->add($jwtMiddleware);
```

## Verify all tests
First run all tests locally with two iterations as described in [Running tests](#running-tests) and after pushing your changes you should watch the [Travis CI Build Status](#travis-ci-and-build-status).

# Integration Tests
We use [Postman](https://www.getpostman.com/) and [newman](https://www.npmjs.com/package/newman) for integration tests.  

## Create a new test
The following tests are required to create a new test

**Test Data:**  
Initially test data is inserted into the database during the docker-compose build. The following SQL-Script contains all test data: [03_initData.sql](../docker/db/03_initData.sql). 

**Import existing tests and environments:**  
First import the test collection and environment from the [docker/newman/collections](../docker/newman/collections) directory.  

**Structure:**  
* jmp
    * category (e.g. `events`)
        * action (e.g. `create`)
            * admin (uses the environment variable `admin-token` as authentication-token)
                * all requests creating events are located here
                * one request for each possible scenario
                    * Successful
                    * Bad Request
                    * Not Found
                    * Forbidden
                    * etc.
            * nonadmin (uses the environment variable `nonadmin-token` as authentication-token)
                * all requests creating events are located here
                * one request for each possible scenario
                    * Successful
                    * Bad Request
                    * Not Found
                    * Forbidden
                    * etc.

Create the missing directory structure needed for your tests and set the authentication tokens for the `admin` and `nonadmin` directories.

**Prerequest scripts:**  
Use the prerequest scripts to set all query parameters and body-data:
````javascript
vars = pm.variables;
vars.set('limit', 10);
````
You can pass the variables using double braces:
````http request
http://localhost/api/v1/events?eventType={{eventType}}&group={{group}}&limit={{limit}}&offset={{offset}}&all={{all}}&elapsed={{elapsed}}
````

**Tests:**  
Postman internally uses [Chai](https://www.chaijs.com/) as BDD / TDD assertion library.  
You should test as much as possible and as precise as possible.
The tests have to:
* be as precise as possible
* test as much as possible
* avoid side-effects (only per admin/nonadmin directory)
* use the variables set in the prerequest scripts: `vars.get('limit');`

Use the already existing tests as reference.

**Save:**  
To save the newly created tests export the `jmp`-collection and replace the existing in the repository with the new one.
Then you only have to commit and push the changes.

### Running tests:  
You can run the tests with the postman runner (don't forget to set the jmp-environment) or with newman.

newman:
````bash
make test dir="$(pwd)"
````

### Travis CI and Build Status:  
Everytime you push your changes, a Travis CI job is triggered and all tests are executed.  
Build Status:  
[![Build Status](https://travis-ci.com/jmp-app/jmp.svg?branch=master)](https://travis-ci.com/jmp-app/jmp)

# Deployment
## Apache
If you want to use the [Apache Web Server](https://httpd.apache.org/) isntead of nginx, you have to do some additional configuration.

**Note:**  
Replace **example.com** by your own domain.

1. Create a separate local branch for your changes: `git checkout -b example.com`
2. Install/Update php dependencies (a running app container or a local php composer installation is required):  
`docker exec app composer install`  
`docker exec app composer update`
1. Configure your web hosting as described:
    * At least php 7.1
    * mysql or mariadb with:
        * This scheme: [jmp](../docker/db/01_setup.sql)
        * A database user with restricted privileges (SELECT, INSERT, UPDATE, DELETE on all tables of the jmp scheme) and a password
2. Configure all environment variables as described in [dotenv](dotenv.md) and [Get Started](../README.md#installation).
3. Rename the folder [api/public](../api/public) to [api/example.com](../api/example.com)
4. Configure [.htaccess](../api/.htaccess):
    * Change **example.com** to your own domain
5. Copy [api](../api) to the webroot of your server
6. [Test the api](#test-your-deployed-backend)
 
### Test your deployed backend
To run the newman test collection you have to do some search/replace with the [collection](../docker/newman/collections/jmp.postman_collection.json).

The Makefile targets `create-test-collection` and `test-deployment` will do the work for you.
Use it as described in [Create and run customized test script](#create-and-run-customized-test-script)

#### Create and run customized test script
Makefile
```bash
make create-test-collection test-deployment dir="$(pwd)" host="example.com" protocol="https" path="test/api"

```

This script will make a customized copy in the [docker/newman/collections](../docker/newman/collections) directory with the given host, protocol and path.
So you can instantly test your deployment.
