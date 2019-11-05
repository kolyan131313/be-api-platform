#Run local server
symfony server:start -d

#Run composer
`composer install` command in root of project

#Api location
http://127.0.0.1:8000/api

#create database
Need to create database for application

#Copy .env to .local.env and add following info
MAILER_URL=gmail://####:#####@localhost
DATABASE_URL=postgresql://postgres:####@127.0.0.1:5432/be_api_platform?&charset=UTF-8

#Run migrations
`./bin/console doctrine:migrations:migrate`

#Load test users of different roles
`php bin/console doctrine:fixtures:load`

#Database create database
Create databases for local environment and test environment
for example: be_api_platform (postgres|mysql) in .env.local file

#Run tests
./bin/console doctrine:migrations:migrate --env=test --dry-run
php vendor/bin/simple-phpunit

