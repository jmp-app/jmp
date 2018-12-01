# ![JMP](logo.png)

## Gettings Started

### Pre-requisites

Make sure that you have docker, npm and composer installed on your machine.

### Installation

#### Frontend

Everything related to the frontend is located in [vue](vue). The frontend uses **[Vue.js](https://vuejs.org/)**.

> Install Dependencies

```bash
npm install --prefix ./vue
```

#### Backend

Everything related to the backend is located in [api](api). The backend uses the **[Slim Framework](https://www.slimframework.com/)**.
> Install Dependencies
```bash
composer install -d api
```

### Docker

Use the following command to start the environment for the first time:
```bash
docker-compose up -d --build
```

You can use docker-compose to start PHP, MariaDB and Nginx:

```bash
docker-compose up -d
```

The frontend will be served under `localhost/` and the backend under `localhost/api/`.

## Authors

- Simon Friedli - [@Simai00](https://github.com/Simai00)
- Dominik Strässle - [@dominikstraessle](https://github.com/dominikstraessle)
- Kay Mattern - [@mtte](https://github.com/mtte)

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
