## Системные требования:
- Composer
- NPM
- PHP 8.3

### Команда для установки PHP-зависимостей для Laravel на Ubuntu 24.04
```sh
sudo apt-get install -y php8.3-cli php8.3-sqlite3 php8.3-common php8.3-fpm php8.3-zip php8.3-gd php8.3-mbstring php8.3-curl php8.3-xml php8.3-bcmath
```

### Установка без докера
```sh
git clone https://github.com/SnappsiSnappes/laravel_inertia_vue
cd laravel_inertia_vue
mv env .env
composer install
npm install
php artisan key:generate
php artisan storage:link
php artisan migrate
php artisan serve

// на втором терминале
>>> npm run dev
```

***


### Установка с докером:
```sh
git clone https://github.com/SnappsiSnappes/laravel_inertia_vue
cd laravel_inertia_vue
mv env .env
composer install
npm install
php artisan key:generate
php artisan storage:link
php artisan sail:install # Поменять порты в docker-compose.yml (опционально)

```
#### Внутри .env раскоментировать строчки:
```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=sail
DB_USERNAME=root
DB_PASSWORD=password
```
#### Закоментировать:
```
DB_CONNECTION=sqlite
DB_HOST=sqlite
DB_PORT=3306
# DB_DATABASE=/home/kali/leonid/laravel_inertia_vue/database/database.sqlite
DB_USERNAME=sail
DB_PASSWORD=password
```

### Создание бд:
```sh
./vendor/bin/sail up -d # Запускаем контейнеры
./vendor/bin/sail mysql -u root -p # Логинимся в базу данных
```
```sql
CREATE DATABASE sail; # Название базы данных из файла .env
exit; # выходим (или quit)
```

### Создание миграции:
```sh
./vendor/bin/sail php artisan migrate
```
### Запуск:
```sh
./vendor/bin/sail npm run dev
```
