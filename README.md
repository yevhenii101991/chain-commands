# chain-commands
Symfony bundle that implements command chaining functionality
1) Clone repository
2) Run ```docker-compose up -d```
3) Go inside docker container, ```(winpty) docker exec -it chain-commands-php-1 bash```
4) run ```composer install```
5) then you can run command ```php bin/console foo:hello``` or ```php bin/console bar:hi```
