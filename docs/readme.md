# ![JMP](../logo.png)

## Getting Started

## Pre-requisites

Make sure you have [docker](https://www.docker.com/) and [npm](https://www.npmjs.com/) installed on your machine.

## Installation for production

Clone the repository
```bash
git clone https://github.com/Simai00/jmp.git
cd jmp
```

Build vue
```bash
cd vue
npm install
npm run build
```

Create file `api/.env` and set all variables declared in [.env.example](../api/.env.example)  
If you don't create the file, it will be created with the default values. This is not recommended in production.   
> Read more about all variables at [dotenv](dotenv.md)

Build and start the docker containers
```bash
docker-compose up -d
```

Install all the dependencies using the composer of the app container
````bash
docker exec -it app composer install
````

You can now access the frontend at [http://localhost](http://localhost) and the api at [http://localhost/api](http://localhost/api)

## Authors

- Simon Friedli - [@Simai00](https://github.com/Simai00)
- Dominik Strässle - [@dominikstraessle](https://github.com/dominikstraessle)
- Kay Mattern - [@mtte](https://github.com/mtte)

## License

This project is licensed under the MIT License - see the [LICENSE](../LICENSE) file for details.





#### Frontend

Everything related to the frontend is located in [vue](../vue). The frontend is built with **[Vue.js](https://vuejs.org/)**.
#### Backend

Everything related to the backend is located in [api](../api). The backend uses the **[Slim Framework](https://www.slimframework.com/)**.