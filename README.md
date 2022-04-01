# mk-ecommerce

An ecommerce project built with sf5 just for practice.


I will use several technologies like rabbitmq, jelastic, apiplatform, redis, react components...

Maybe using all these technologies is a bit overkill but it's just for fun :)

## Status : WIP


## Completed tasks :

- Unit tests & functional tests for Users


### Launch project :
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
php bin/console doctrine:schema:update --env=test
```
- start : 
```
php bin/phpunit
```