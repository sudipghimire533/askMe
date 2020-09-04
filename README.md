
# What I need
* A apache2 web server
* permission to modify apache2 conf file

# Getting Started
## Create subdomain
In `/etc/apache2/sites-available/000-default.conf` file:
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

## enable mod_rewrite
```
a2enmod rewrite
```


## restart server
```
service apache2 --full-restart
```

# Get to Work
Sit infront of your computer whole and keep building.