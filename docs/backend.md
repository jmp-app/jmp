# Getting Started

Make sure everything is running as explained in [README.md](../README.md).

# Code Overview
## Directory Structure
```bash
src # Source directory. Not public
├── JMP # Application directory
│ ├── Controllers # Controllers to handle requests as registered in routes.php
│ │ ├── ...
│ ├── Middleware # Custom Middlewares
│ │ ├── AuthenticationMiddleware.php # Checks if the enquirer has the required permission for a route
│ │ └── ValidationErrorResponseBuilder.php # Checks if there are invalid parameters and returns the error
│ ├── Models # Raw data objects with the functionality to convert to an array
│ │ ├── ArrayConvertable.php # Required for a correct object-array conversion
│ │ ├── ...
│ ├── Services # Services are called by Controllers or other services. They handle the business logic and the database communication
│ │ ├── Auth.php # Special service used to authenticate an enquirer or to generate json web tokens
│ │ ├── ...
│ └── Utils # Useful classes
│ ├── Converter.php # Convert objects correctly to arrays
│ ├── Optional.php # If a method execution could fail, return an Optional instead of null|object or similar
│ └── PermissionLevel.php # Enumeration of all available permission levels
├── dependencies.php # Initialize services, controllers, middlewares and add them to the slim container
├── routes.php # Register routes with their middlewares here
├── settings.php # Slim settings and required settings for the dependencies
└── validation.php # Register validation rules here
```


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