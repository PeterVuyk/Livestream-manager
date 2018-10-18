# Livestream API (WIP)

An API to control the livestream for broadcast live video content remotely. This project is still work in progress. 

## Getting Started

This project gives you everything you need to control the livestream. Follow the installing steps to setup the livestream on your (local) machine, and make sure that Docker and Docker-compose is installed.

### Installing

Below procedure that tell you how to get a development environment running.

1. Clone the project on your machine.

        git clone https://github.com/PeterVuyk/Livestream-api.git

2. Create a .env from the .env.dist file. Adapt it according to your needs.

        cp .env.dist .env

3. Build and run the docker containers in the background.

        docker-compose up -d --build

4. Install php dependencies.

        docker-compose exec php composer install

5. Install node module dependencies & build/compile JS files through Webpack.

        docker-compose exec yarn /bin/bash -c "cd /usr/src/app && yarn install && yarn build"

6. Create the database.

        docker-compose exec php bin/console doctrine:database:create

7. Run the database migrations to setup the database tables.

        docker-compose exec php bin/console doctrine:migrations:migrate

8. Extract an update the translation content.

        docker-compose exec php bin/console translation:update --dump-messages --force en

You are done, you can view the application on the URL: `localhost:8080`.

## TODO: Running the tests

...

### TODO: And coding style tests

...
