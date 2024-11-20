

## Первоначальная настройка.
- поменять env на .env

- ./vendor/bin/sail composer install
- ./vendor/bin/sail npm i
- ./vendor/bin/sail artisan storage:link
- ./vendor/bin/sail php artisan key:generate
- ./vendor/bin/sail php artisan migrate

Перед запуском создаем бд в mysql докера.
- ./vendor/bin/sail mysql -u root -p 

Сюда вводим:
- use sail;
- CREATE DATABASE sail; // название дб из .env файла

Запуск:
- ./vendor/bin/sail npm run dev
- ./vendor/bin/sail php artisan serve
