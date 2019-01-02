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

# Developing new route

## Service & Model

## Controller

## Route

### Authentication

### Validation