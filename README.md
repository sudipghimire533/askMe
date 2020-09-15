
# What I need
* A __apache2__ web server
* __php__ 7.3 or grater
* __Facebook PHP-Graph-SDK__
* __Mariadb__ version >= 10.0 OR __mysql__ version >= 8.0
* __root permission__ to modify apache2 conf file
* __phpMyAdmin__ for easy database administration(optional)
# Getting Started

## Basic Configuration
```zsh
cd ~
git clone https://www.github.com/sudipghimire533/askMe
link /var/www/html/askMe ./askMe
```
If you have installed mysql then
```zsh
alias mariadb=mysql
````

## Get Facebook SDK
*First of all you need to have composer installed. __[see instruction](https://composer.org)__*
```zsh
cd askMe #go to project directory
cd login #install sdk in login directory (!not in project root)
composer install facebook/facebok-php-graph-sdk -vvv #install the sdk
```

## Create Database and fill test data
Login to database as root then create databse, create new user and grant required permission
```sql
MariaDB> CREATE USER 'askme'@'localhost' identified by 'password';
MariaDB> CREATE DATABASE askme;
MariaDB> GRANT ALL Privileges ON askme.* TO 'askme'@'localhost' IDENTIFIED BY 'password';
MariaDB> quit
```
In Command Prompt (!not in database prompt).
```
mariadb -u askme -p askme < /var/www/html/askMe/server/database/database_structure.sql
```

## Configure Local Domain
__*/etc/apache2/sites-available/000-default.conf*__ :
```xml
<!-- Local domain name should be localhost (requirement from facebook graph sdk) -->
<VirtualHost localhost:80>
    <Directory /var/www/html/askMe>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>
	DocumentRoot /var/www/html/askMe/
	ServerName project.localhost
</VirtualHost>
```

__*/etc/hosts*__:
```
127.0.0.1   localhost # For ipv4
::1         localhost # For ipv6
```

## enable mod_rewrite
```apache
a2enmod rewrite
```

## restart server and database
```zsh
service apache2 --full-restart
service mysql restart || service mariadb restart
```

# Get to Work
Sit infront of your computer whole day and keep building.