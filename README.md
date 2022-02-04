# Symfony 6 Docker-Development-Stack


Чтобы запустить проект нужно: 
1. Запустить docker командой ```docker-composer up```
2. Скопировать .env.text и переименовать копию в .env
3. Зайти в терминал php контейнера ```docker exec -it php_link sh```
4. Запустить ```composer install```
5. Запустить миграции командой
6. Выполнить миграции ```php bin/console doctrine:migrations:migrate```

После этого проект будет доступен по адресу http://127.0.0.1
