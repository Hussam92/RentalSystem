#!/bin/bash

#
# Copyright (c) 2021. This code is property of the Sdui GmbH.
#

checkAndMigrate() {
  for i in {30..0}; do
			if php artisan CheckDatabaseConnection; then
				break
			fi
			echo 'MySQL init process in progress...'
			sleep 1
		done
  if php artisan CheckDatabaseConnection; then
    php /var/www/artisan migrate
    exec php-fpm
  else
    return "${?}"
  fi
}

set -e

role=${CONTAINER_ROLE:-app}
env=${APP_ENV:-production}

if [ "$env" != "local" ]; then
    echo "Caching configuration..."
    (cd /var/www && php artisan config:cache && php artisan route:cache && php artisan view:cache)
fi

if [ "$role" = "app" ]; then

    checkAndMigrate

elif [ "$role" = "queue" ]; then

    echo "Running the queue..."
    exec supervisord --nodaemon --configuration /var/www/docker/supervisor.conf


elif [ "$role" = "scheduler" ]; then

    while true
    do
      php /var/www/artisan schedule:run --verbose --no-interaction &
      sleep 60
    done

else
    echo "Could not match the container role \"$role\""
    exit 1
fi
