

**Database settings:**  

| Name | Desc | Default Value | Options | Note |
|------|------|---------------|---------|------|
| `DB_ENGINE` | PDO Engine           | `mysql` | |The app depends on mariadb/mysql|  
| `DB_HOST`   | Host of the database | `db`    | ip-address, hostname, docker-container |When using docker on the same host as the app, you have to use the docker-container-name as host|
| `DB_PORT`   | Port                 | `3306`  ||As in [docker-compose.yml](../docker-compose.yml)|
| `DB_DATABASE` | Schema/database | `jmp` | |As in [docker-compose.yml](../docker-compose.yml)|
| `DB_USERNAME` | Username | `jmp_user` | |As in [docker-compose.yml](../docker-compose.yml)|
| `DB_PASSWORD` | Password | `pass4dev` | |As in [docker-compose.yml](../docker-compose.yml)|

**App Settings:**

| Name | Desc | Default Value | Options | Note |
|------|------|---------------|---------|------|
| `APP_NAME` | Application Name | `JMP` | |Same as set in [composer.json](../api/composer.json) at the autoload section |
| `APP_ENV` | Environment | `local` |  |Defines the environment|
| `APP_DEBUG` | Debugging | `true` | true, false |Display error details as explained in [Slim Default Settings](https://www.slimframework.com/docs/v3/objects/application.html#slim-default-settings) Not recommended for production environments|
| `APP_URL` | URL of the application | `http://localhost` | |Issuer of the jwt|
| `APP_LOG_LEVEL` | Logging level | `debug` | [Monolog Log Levels (case insensitive)](https://github.com/Seldaek/monolog/blob/master/doc/01-usage.md#log-levels) ||
| `APP_LOG_STDOUT` | Logging to stdout | `true` | true, false |If set to true, you can use `docker logs app`|
| `APP_LOG_FILE` | Log file | `./log/app.log` | |File must be located in `api/` or a subdirectory, else php won't have the necessary permissions |

**JWT Settings:**

| Name | Desc | Default Value | Options | Note |
|------|------|---------------|---------|------|
| `JWT_SECRET` | JWT Signature  | `supersecretkeyyoushouldnotcommittogithub` | |Read more at [stackoverflow](https://stackoverflow.com/a/31313582/7130107). You can use openssl to generate a secret. [HS256/HMAC](https://en.wikipedia.org/wiki/HMAC) is used for signing the jwt|
| `JWT_SECURE` | https only | `false` | true, false |[read more](https://github.com/tuupola/slim-jwt-auth#security)|