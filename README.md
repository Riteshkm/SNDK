Projet Setup command.

1. composer install

2. npm install

3. mv .env.example .env

  Update Database Detail

  DB_CONNECTION = mysql
  DB_HOST = 127.0.0.1
  DB_PORT = 3306
  DB_DATABASE = DATABASE_NAME
  DB_USERNAME = DATABASE_USERNAME
  DB_PASSWORD = DATABASE_PASSWORD

4. php artisan migrate

5. php artisan db:seed

6. php artisan key:generate

7. npm run dev
