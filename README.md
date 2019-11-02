#Run migrations
./bin/console doctrine:migrations:migrate

#Load test users of different roles
php bin/console doctrine:fixtures:load