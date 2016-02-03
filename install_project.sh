#!/bin/bash

echo  "1. Install project"
echo  "2. Create database"
echo  "3. Clear cache and logs"
echo  "4. Generate entities"
echo  "5. Run tests"
echo  "6. Exit"

read item
case "$item" in

1)
curl -sS https://getcomposer.org/installer | php -- --install-dir=bin --filename=composer
php bin/composer install
rm bin/composer
npm install
./node_modules/.bin/bower install
./node_modules/.bin/gulp
;;

2)
php app/console doctrine:database:drop --force
php app/console doctrine:database:create
php app/console doctrine:schema:update --force
php app/console h:d:f:l --no-interaction
;;

3)
rm -rf app/cache/*
rm -rf app/logs/*
;;

4)
php app/console doctrine:generate:entities --no-backup AppBundle
;;

5)
php bin/phpunit -c app
;;

6)
exit
;;

esac