**Database settings [api/db.env](../api/db.env):**  

| Name | Desc | Default Value | Options | Note |
|------|------|---------------|---------|------|
| `DB_ENGINE` | PDO Engine           | `mysql` | |The app depends on mariadb/mysql|  
| `DB_HOST`   | Host of the database | `db`    | ip-address, hostname, docker-container |When using docker on the same host as the app, you have to use the docker-container-name as host|
| `DB_PORT`   | Port                 | `3306`  | |As in [docker-compose.yml](../docker-compose.yml)|
| `DB_DATABASE` | Schema/database | `jmp` | **If you change this, you also have to update all [db init files](../docker/db)** |As in [docker-compose.yml](../docker-compose.yml)|
| `DB_USERNAME` | Username | `jmp_user` | |As in [docker-compose.yml](../docker-compose.yml)|
| `DB_PASSWORD` | Password | `pass4dev` | |As in [docker-compose.yml](../docker-compose.yml)|


**Database settings [db.env](../db.env):**

| Name | Desc | Default Value | Options | Note |
|------|------|---------------|---------|------|
| `MYSQL_ROOT_PASSWORD` | Mysql root password | `jmp_docker_local` | | See: [mariadb docker documentation](https://docs.docker.com/samples/library/mariadb/#mysql_root_password). **Change in production!**|  

**App Settings [.env](../api/.env):**

| Name | Desc | Default Value | Options | Note |
|------|------|---------------|---------|------|
| `APP_NAME` | Application Name | `jmp` | |Same as set in [composer.json](../api/composer.json) at the autoload section |
| `APP_ENV` | Environment | `local` |  |Defines the environment|
| `APP_DEBUG` | Debugging | `true` | true, false |Display error details as explained in [Slim Default Settings](https://www.slimframework.com/docs/v3/objects/application.html#slim-default-settings). **Not recommended for production environments**|
| `APP_URL` | URL of the application | `http://localhost` | |Issuer of the jwt|
| `APP_LOG_LEVEL` | Logging level | `debug` | [Monolog Log Levels (case insensitive)](https://github.com/Seldaek/monolog/blob/master/doc/01-usage.md#log-levels) ||
| `APP_LOG_STDOUT` | Logging to stdout | `true` | true, false |If set to true, you can use `docker logs app`|
| `APP_LOG_FILE` | Log file | `log/app.log` | |File must be located in `api/` or a subdirectory, else php won't have the necessary permissions and will throw an exception |
| `APP_ROUTER_CACHE_FILE` | Cache file | `false` | `cache/routes.cache.php` or `false` |File must be located in `api/` or a already existing subdirectory, else php won't have the necessary permissions and will throw an exception [Read more about slim's cache file](https://akrabat.com/slims-route-cache-file/) |


**JWT Settings [.env](../api/.env):**

| Name | Desc | Default Value | Options | Note |
|------|------|---------------|---------|------|
| `JWT_SECRET` | JWT Signature  | `supersecretkeyyoushouldnotcommittogithub` | | **Generate a new private secret!** Read more at [stackoverflow](https://stackoverflow.com/a/31313582/7130107). You can use openssl to generate a secret. [HS256/HMAC](https://en.wikipedia.org/wiki/HMAC) is used for signing the jwt|
| `JWT_SECURE` | https only | `false` | true, false |[read more](https://github.com/tuupola/slim-jwt-auth#security)|
| `JWT_EXPIRATION` | validity in minutes | `7200` | Any integer greater than 0 | |

**CORS Settings [.env](../api/.env):**  
Read more: [CORS](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS)

| Name | Desc | Default Value | Options | Note |
|------|------|---------------|---------|------|
| `CORS_ALLOWED_ORIGINS` | Allowed Origins  | "*" | Comma seperated list of URLs as string  |  |
| `CORS_ALLOWED_HEADERS` | Allowed Headers | "X-Requested-With, Content-Type, Accept, Origin, Authorization" | Comma seperated list of Headers as string |  |
| `CORS_ALLOWED_METHODS` | Allowed Methods | "GET, POST, PUT, DELETE" | Comma seperated list of Methods as string | |
