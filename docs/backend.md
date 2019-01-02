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

## Service & Model

## Controller

## Route

### Authentication

### Validation