## Системные требования.
- docker
- docker-compose
- composer
- npm

## Первоначальная настройка.
- поменять env на .env

- composer install
- npm install
- ./vendor/bin/sail artisan storage:link
- ./vendor/bin/sail php artisan key:generate

## Перед запуском создаем бд в mysql докера.
- ./vendor/bin/sail up -d // запускаем контейнеры
- ./vendor/bin/sail mysql -u root -p // логинимся в бд
###  Сюда вводим:
- CREATE DATABASE sail; // название дб из .env файла

## Наконец.
- ./vendor/bin/sail php artisan migrate

## Запуск:
- ./vendor/bin/sail npm run dev
- ./vendor/bin/sail php artisan serve
