
# What I need
* A apache2 web server
* permission to modify apache2 conf file

# Getting Started
## Create subdomain
In __*/etc/apache2/sites-available/000-default.conf*__ file:
```
<VirtualHost *:80>
    <Directory /var/www/html/project>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>
	DocumentRoot /var/www/html/project/
	ServerName project.localhost
</VirtualHost>
```
In __*/etc/hosts*__ file:
```
127.0.0.1 project.localhost
```

## enable mod_rewrite
```
a2enmod rewrite
```


## restart server
```
service apache2 --full-restart
```

# Get to Work
Sit infront of your computer whole day and keep building.