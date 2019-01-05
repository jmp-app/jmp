# Backend

The backend uses PHP with the [Slim Framework](https://www.slimframework.com/).

**Read more in the [documentation](../docs/README.md).**

# Table of Contents:
- [Backend](#backend)
  * [Composer](#composer)
  * [Environment Variables](#environment-variables)
  * [API](#api)
  * [Database](#database)

<small><i><a href='http://ecotrust-canada.github.io/markdown-toc/'>Table of contents generated with markdown-toc</a></i></small>


## Composer

Composer is used for auto-loading classes and managing dependencies. Run `composer install` to install all dependencies.

## Environment Variables

All environment variables used by the backend are stored in the `.env` file. When running `composer install` the [.env.example](.env.example) file is copied to the `.env` file.  Add your own variables into this file, but never commit it. 

## API

The API Specification can be found in [API Spec Version 1](../docs/api-v1.md)

## Database

Information about the database schema can be found here: [database](../docs/database.md)

When using Docker, the database container is initialized according to the schema when the container is created. The [setup script](../docker/db/01_setup.sql) is used. Additionally some basic data is created too with the [init data script](../docker/db/02_initData.sql).
