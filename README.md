#Run local server
symfony server:start -d

#Run migrations
./bin/console doctrine:migrations:migrate

#Load test users of different roles
php bin/console doctrine:fixtures:load

#Database create database
Create databases for local environment and test environment
for example: be_api_platform

#Run tests
php bin/console doctrine:fixtures:load