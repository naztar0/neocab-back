# NeoCab Backend

## Environment setup

```
cp .env.example .env
```

Write your database and Azure credentials into the .env file.

## Docker setup

```
docker-compose build
docker-compose up -d
```

**Next commands run under the docker**

## Project setup

```
docker-compose exec app php artisan jwt:secret
docker-compose exec app php artisan storage:link
```

## Rolling migrations

```
docker-compose exec app php artisan migrate
```

## Running seeders

```
docker-compose exec app php artisan db:seed
```

## Email setup

Retrieve your SMTP credentials using this [instruction](https://support.google.com/mail/answer/7126229) and write them into .env file.
