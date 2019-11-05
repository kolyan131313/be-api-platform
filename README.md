#Run local server
symfony server:start -d

#Run composer
`composer install` command in root of project

#Api location
http://127.0.0.1:8000/api

#Generate ssh keys for JWT
- `mkdir -p config/jwt`
- `openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096`
- `openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout`

#Database create
Create databases for local environment and test environment
for example: be_api_platform (postgres|mysql) in .env.local file

#Copy .env to .local.env and add following info
MAILER_URL=gmail://####:#####@localhost
DATABASE_URL=postgresql://postgres:####@127.0.0.1:5432/be_api_platform?&charset=UTF-8

#Run migrations
`./bin/console doctrine:migrations:migrate`

#Load test users of different roles
`php bin/console doctrine:fixtures:load`

#Run tests
- `php bin/console doctrine:migrations:migrate --env=test --dry-run` with JWT_PASSPHRASE variable from .env.local
- `php bin/console doctrine:fixtures:load --env=test`
- `php vendor/bin/simple-phpunit`

