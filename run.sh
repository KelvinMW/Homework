sudo apt-get update
sudo apt-get install php
sudo apt-get install mysql-server

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"

git clone https://github.com/GibbonEdu/core.git
cd core
composer install

mysql -u <username> -p <password> <database_name> < create_homework_table.php

php -S localhost:8000
