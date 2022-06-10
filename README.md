### ТЗ тестовой работы
    ООО Спутник https://sputnikfund.ru/
    ТЗ находится в ./task

### стек разработки
    docker + docker-compose + nginx + php-fpm + nodejs + postgres + pgadmin

### предустановленные пакеты
    nginx
    postgres (PostgreSQL) 14.3 (Debian 14.3-1.pgdg110+1)
    pgadmin
    php v8.1.6
    imagick 3.7.0
    xdebug 3.1.4
    nodejs 16.15.1
    nodejs - если нужна последняя версия https://github.com/nodesource/distributions#debinstall

---
### установка проекта
* docker-compose up -d --build
* docker exec -it php-fpm /bin/bash

---
### необходимые конфигурации проекта
* composer create-project --prefer-dist laravel/lumen server
* composer require lumen-jwt
* php artisan migrate

---
### авторизация с access и refresh токенами
* после создания или авторизации выдается два токена access и refresh
* access токен необходимо вставить в headers Authorization
* пример: Bearer _access_token_
* время жизни токенов определяется в .env файле EXP_ACCESS и EXP_REFRESH в секундах
* ключи токенов определяется в .env файле JWT_ACCESS_SECRET и JWT_REFRESH_SECRET

---
