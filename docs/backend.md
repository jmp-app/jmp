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

# Developing new route

## Service & Model

## Controller

## Route

### Authentication

### Validation