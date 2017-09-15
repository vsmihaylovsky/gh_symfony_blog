#!/bin/bash

echo  "1. Install project"
echo  "2. Create database"
echo  "3. Load fixtures"
echo  "4. Clear cache and logs"
echo  "5. Generate entities"
echo  "6. Run tests"
echo  "7. Exit"

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
;;

3)
php app/console h:d:f:l --no-interaction
app/console create:blog:admin admin 1 admin@my.my
;;

4)
app/console cache:clear
rm -rf app/cache/*
rm -rf app/logs/*
;;

5)
php app/console doctrine:generate:entities --no-backup AppBundle
;;

6)
php bin/phpunit -c app
;;

7)
exit
;;

esac