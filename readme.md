## Установка
- composer-install
- Скопировать и переименовать .evn.example в .env
  - Указать верные данные для DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD
  - Указать верные данные для REDIS_HOST, REDIS_PASSWORD, REDIS_PORT
  
```php
php artisan migrate
```
```php
php artisan db:seed
```

__Примечание__ 
> * Для работы authenticate метода JWT, нужно обязательно наличие пользователя с ID=1 в таблице Users
> * Для того, чтобы API возвращала правильные ответы в загловке запроса обязательно должен быть ключ X-Requested-With, значение XMLHttpRequest
> * Так же должен быть запущен Redis, иначе проверка токенов не будет работать, т.к. они хранятся в нём

## Список Маршрутов
```
/api/news
/api/news/{id}
/api/login - для авторизации, обновления токена
```

## Проверка работоспособности
```php
.\vendor\bin\phpunit
```
```
1.Установить в Postman в Headers key = X-Requested-With, Value = XMLHttpRequest
2.Обращаться по маршрутам указанным выше
```
