#!/usr/bin/env bash

# Build and run the docker containers in the background.
docker-compose up -d --build

# Install php dependencies.
docker-compose exec php composer install

# Install node module dependencies & build/compile JS files through Webpack.
docker-compose exec yarn /bin/bash -c "cd /usr/src/app && yarn install && yarn build"

# Rebuild docker containers once dependencies are available so SQS worker can start running
docker-compose down
docker-compose up -d

# Run the database migrations to setup the database tables.
docker-compose exec php bin/console doctrine:migrations:migrate -n

# Add the required cron for the schedule to crontab.
(crontab -l ; echo "* * * * * /usr/local/bin/docker-compose -f $(pwd)/docker-compose.yml exec -T php bin/console app:scheduler-execute >/dev/null 2>&1") | sort - | uniq - | crontab -
