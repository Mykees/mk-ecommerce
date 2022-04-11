# mk-ecommerce

An ecommerce project built with sf5.


I will use several technologies like rabbitmq, apiplatform, redis, react components, stripe...

Maybe using some of these technologies is a bit overkill but it's just for fun :)

## Status : WIP


## Completed tasks :

- Unit tests & functional tests for Users
- Unit tests & functional tests for Products


### Launch project :
- Composer :
```
composer install
```

- assets :
```
yarn install
```
```
yarn run dev
```

- Start docker :
```
docker-compose up -d
```

Go to :
[127.0.0.1:8006](127.0.0.1:8006)


### Launch tests:
- Create database :

```
docker exec -it ec-php bash
```
```
php bin/console doctrine:database:create --env=test
```
```
php bin/console doctrine:schema:update --env=test --force
```
- start : 
```
php bin/phpunit
```

### Generate Test Coverage :
```
XDEBUG_MODE=coverage php bin/phpunit --coverage-html var/log/test/test-coverage
```