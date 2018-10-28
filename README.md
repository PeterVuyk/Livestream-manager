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

3. Run the build file to; install the dependencies, setup the database, setup the service with docker, add the required command for the recurring schedule to crontab.

        sh build.sh

## Usage

Once the installation is complete, you can view the application by URL `localhost:8080`.

Let's take a look at the docker images we have.

- `db`: This is the MySQL database container, the database itself is persistent and not stored in the container.
- `php`: This is the php7.2-FPM container which the application volume is mounted.
- `web`: This is the NGINX webserver container in which application volume is mounted too.
- `yarn`: This is the yarn container which is used for the frontend dependencies.

```bash
$ docker-compose ps
      Name                    Command                 State                     Ports              
---------------------------------------------------------------------------------------------------
livestream-mysql   /entrypoint.sh mysqld           Up (healthy)   0.0.0.0:3306->3306/tcp, 33060/tcp
livestream-nginx   nginx                           Up             443/tcp, 0.0.0.0:8080->80/tcp    
livestream-php     docker-php-entrypoint php-fpm   Up             0.0.0.0:9000->9000/tcp           
livestream-yarn    node                            Up                                              
```

TODO: add a view images with a short description about usage.

Go to `localhost:8080/admin` page for the full user manual.

## Useful commands

    # Stop all running Docker containers
    $ docker-compose down
    
    # With the following commands you can enter each docker container.
    # Replace <servicename> with 'yarn', 'php', 'web' or 'db':
    $ docker-compose exec <servicename> bash
    
    # MySQL commands
    $ docker-compose exec db /bin/bash -c "mysql -u<username> -p<password>"

## TODO: Running the tests

...

### TODO: And coding style tests

...
