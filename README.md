# Livestream Server

A livestream with user interface, Restful API and messaging. Broadcast live video content remotely to a given host.

## Getting Started

This project gives you everything you need to control and stream the livestream towards a host. It is not a client to show the footage!

Follow the installing steps to setup the server on your machine, make sure that Docker and Docker-compose is installed.

### Installing

Below procedure that tell you how to get a development environment running.

1. Clone the project on your machine.

        git clone https://github.com/PeterVuyk/Livestream-server.git

2. Create a .env from the .env.dist file. Adapt it according to your needs.

        cp .env.dist .env

3. Run the build file to; install the dependencies, setup the database, setup the service with docker, add the required command for the recurring schedule to crontab.

        sh build.sh

4. Setup a SNS, SQS and IAM role in AWS and add it to the environment variables.
- [Creating an SNS Topic](https://docs.aws.amazon.com/sns/latest/api/API_CreateTopic.html)
- [Creating an Amazon SQS Queue](https://docs.aws.amazon.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/sqs-create-queue.html)
- [Create IAM Roles](https://docs.aws.amazon.com/IAM/latest/UserGuide/id_roles_create.html)

## Usage

Once the installation is complete, let's take a look at the docker images we have running:

- `db`: This is the MySQL database container, the database itself is persistent and not stored in the container.
- `php`: This is the php7.2-FPM container which the application volume is mounted.
- `web`: This is the NGINX webserver container in which application volume is mounted too.
- `yarn`: This is the yarn container which is used for the frontend dependencies.
- `phpmyadmin`: Easy database access via phpMyAdmin, only available on a development machine.
- `supervisor`: The supervisor will run the worker to get messages from the sqs queue. It will automatically restart the worker if the worker somehow stopped. 

```bash
$ docker-compose ps
        Name                       Command                       State                         Ports              
------------------------------------------------------------------------------------------------------------------
livestream-mysql        /entrypoint.sh mysqld            Up (health: starting)   0.0.0.0:3306->3306/tcp, 33060/tcp
livestream-nginx        nginx                            Up                      443/tcp, 0.0.0.0:8080->80/tcp    
livestream-php          docker-php-entrypoint php-fpm    Up                      0.0.0.0:9000->9000/tcp           
livestream-phpmyadmin   /run.sh supervisord -n -j  ...   Up                      0.0.0.0:8081->80/tcp, 9000/tcp   
livestream-supervisor   docker-php-entrypoint /usr ...   Up                      9000/tcp                         
livestream-yarn         node                             Up                                                       
```

Next open the application, you can view the application via URL `localhost:8080`, see also the image below for an impression.
![alt text](https://github.com/PeterVuyk/Livestream-server/blob/master/assets/images/example.png)

Ok, login with the sample account that was created by default (username: temporary, password: secret). Once you are logged in you can:

- Create a new account, choose your preffered language, password etc.
- Remove the temporary account and manage the authorized users.
- Update the camera configurations.
- Create your first stream schedule.
- View the API Rest endpoints.

Especially for the development environment we added phpMyAdmin as a container, this is available via URL `localhost:8081`.

Lastly: To get used more familiar with the functionality and definitions, in the application, we added a user manual page, go to URL `localhost:8080/en/admin/manual` or click on 'manual' in the navigation bar. 

## Restful API

The application have a Restful API with username 'livestream_server'. If you would like to use the Restful API we advice you to update the password:

Run the command below, choose for encoder `Symfony\Component\Security\Core\User\User` and fill in a secret password:

    $ php bin/console security:encode-password

Copy the encoded password and overwrite the existing encrypted password in the file: `security.yaml`. Key: `security.providers.in_memory.memory.users.livestream_server.password`.

More information: [Symfony security - Encoding the User's Password](https://symfony.com/doc/4.0/security.html#b-configuring-how-users-are-loaded)

## Useful commands

    # Stop all running Docker containers
    $ docker-compose down
    
    # With the following commands you can enter each docker container.
    # Replace <servicename> with 'yarn', 'php', 'web' or 'db':
    $ docker-compose exec <servicename> bash
    
    # MySQL commands
    $ docker-compose exec db /bin/bash -c "mysql -u<username> -p<password>"
    
    # Start / Stop livestream via command line
    $ docker-compose exec php bash bin/console app:livestream-start
    $ docker-compose exec php bash bin/console app:livestream-stop
    
    # Consume messages from the queue (When using docker, supervisor will start and supervise this command)
    # The argument is the number of retries to get the messages from the queue, 
    # when running in docker, supervisord will restart the process automatically.
    $ docker-compose exec php bash bin/console app:messaging-queue-worker <argument>

## Running the tests

By running the following command you can run the unit test suite:

    $ docker-compose exec php /bin/bash -c "php ./bin/phpunit"

The coding style standard phpcs is used for this project. To start the CodeSniffer run:

    $ docker-compose exec php /bin/bash -c "./vendor/bin/phpcs src/"
