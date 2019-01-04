# Getting Started

Make sure everything is running as explained in [README.md](../README.md).

# Code Overview
## Directory Structure
* [`src/`](../api/src) Source directory. Not public
    * [`JMP/`](../api/src/JMP) Application directory
        * [`Controllers/`](../api/src/JMP/Controllers) Controllers to handle requests as registered in routes.php
        * [`Middleware/`](../api/src/JMP/Middleware) Custom Middlewares
        * [``Models/``](../api/src/JMP/Models) Raw data objects with the functionality to convert to an array
        * [``Services/``](../api/src/JMP/Services) Business logic
        * [``Utils/``](../api/src/JMP/Utils) Useful classes

## Slim specific classes
* Private
    * [``dependencies.php``](../api/src/dependencies.php) Initialize services, controllers, middlewares and add them to the slim container
    * [``settings.php``](../api/src/settings.php) Slim settings and required settings for the dependencies
    * [``routes.php``](../api/src/routes.php) Register routes with their middlewares here
* Public
    * [``index.php``](../api/public/index.php) Application entry point


## Authentication

### Usage
We use [JWT](https://jwt.io/) for authentication. The token must be submitted in the Authorization header using the Bearer schema.  
Example:  

```http_request
Authorization: Bearer <token>
```

Read more at the [api specification](api-v1.md#authentication).
### Settings
To adjust the settings, look here:
 * In [settings.php](../api/src/settings.php)
 * [.env](../api/.env) and [.env docs](dotenv.md)

## Implementation
The submitted token is decoded by the [jwt-middleware](https://github.com/tuupola/slim-jwt-auth).  
The custom [authentication middleware](../api/src/JMP/Middleware/AuthenticationMiddleware.php) checks the permissions of the enquirer using the `sub` entry of the decoded token.
If the enquirer has got the right permissions for the route, he can pass, otherwise a 401 will be returned if he hasn't submitted any or an invalid token or a 403 if he just hasn't got the required permissions.

### Login
To receive a new token, the user have to call the [login route](api-v1.md#login).  
If the username and the password are valid, a new token is generated with the following service class [Auth.php](../api/src/JMP/Services/Auth.php).

## Validation
Every user input has to be validated before it is processed.

## Implementation

## Deployment

## Model

## Array Conversion

## Code Style

### Optional

### Type Hints

### SQL


# Developing new route
This guide will take you through the most important steps to develop a new route. The example is done with the [GET /v1/users/{id}](api-v1.md#get-user) route.

## Service & Model
First create the required models and services.
The route returns a json containing a user object, so a User model is required.
[Create a new model](#create-a-new-model) if the required doesn't exist yet.
See [User.php](../api/src/JMP/Models/User.php)

The model is just a raw data object with the functionality to convert properly to an array.
The service method handles all the business logic and communicates with the database.

Now we have to implement a Service to get a user from the database selected by the userid. A [UserService](../api/src/JMP/Services/UserService.php) already exists, so we only have to add a `getUserByUserId` method. 
Otherwise we must have to [create a new ervice class](#create-a-new-service-class).

The service method will select the user with the given id. See [UserService.php](../api/src/JMP/Services/UserService.php) at the method `getUserByUserId`.
The method returns an Optional containing the User object on succeed, otherwise nothing.

Make sure you use meaningful method names.

### Create a new service class
If no useful service class already exists, you have to create a new one.
Make sure the constructor has a ContainerInterface parameter and all required dependencies are added as attributes in the constructor.

After you must add the service class to the slim container in the [dependencies.php](../api/src/dependencies.php) file.

Check out already existing serivces as examples. [Services](../api/src/JMP/Services) 

### Create a new model
If the required model not already exists, you have to create a new one.
* The model should have all columns(no foreign fields) as public attributes.
* The model must have a cunstructor with one array as parameter. All attributes are set by the values of the array.
* The model must implement the [ArrayConvertable Interface](../api/src/JMP/Models/ArrayConvertable.php)

Check out already existing model as examples. [Models](../api/src/JMP/Models) 


## Controller
Next the controller or the specific method can be created.

The [UsersController.php](../api/src/JMP/Controllers/UsersController.php) already exists, so only the required `getUser` method has to be added.
If no appropriate controller exists, [create a new controller class](#create-a-new-controller-class)
The controller method will be called, when the validation and authentication passes.
The task of the controller method is to call the user service and to build the response dependent on the return value of the service.

If the user is found, a [User](api-v1.md#user) is returned, otherwise if no user is found, a 404 with an optional [Error message](api-v1.md#error-handling) is returned.

### Create a new controller class
The controller must have a constructor with the ContainerInterface parameter and all required dependencies has to be added as attributes in the constructor.

Check out already existing controllers as examples. [Controllers](../api/src/JMP/Controllers) 

## Route
Now the new route has to be registered in [route.php](../api/src/routes.php).
It's very important, that the middlewares are added in the right order and with the right configuration.

1. [ValidationErrorResponseBuilder](../api/src/JMP/Middleware/ValidationErrorResponseBuilder.php)
2. Validation Middleware
    1. Use the right validation settings. Add them to [validation.php](../api/src/validation.php)
3. [AuthenticationMiddleware](../api/src/JMP/Middleware/AuthenticationMiddleware.php)
    1. Set the right [PermissionLevel](../api/src/JMP/Utils/PermissionLevel.php) as noted in the [api specification](api-v1.md#get-user) of your route
4. JWT Middleware