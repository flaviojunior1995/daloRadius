# Install daloRADIUS on Debian 12
This guide will walk you through the steps to install daloRADIUS on a Debian system.

## Prerequisites
Before you begin, you should have the following:

- A Debian 12 system with **root** access.
- A basic understanding of the Linux command line.

## Installation Steps

1. Update the package list:
```
apt update
```

2. Upgrade the system:
```
apt upgrade
```

3. Install Apache web server:
```
apt install apache2
```

4. Install PHP and required modules:
```
apt install php libapache2-mod-php php-mysql php-zip php-mbstring php-cli php-common php-curl php-gd php-db php-mail php-mail-mime
```

5. Install MariaDB server:
```
apt install mariadb-server mariadb-client 
```

6. Install FreeRadius:
```
apt install freeradius freeradius-mysql freeradius-utils 
```

7. Secure the MariaDB installation:
```
mysql_secure_installation
```

8. Configure DB FreeRadius:
OBS: Change variable <password>
```
mysql -u root -e "CREATE DATABASE radius;"
mysql -u root -e "CREATE USER 'radius'@'localhost' IDENTIFIED BY '<password>';"
mysql -u root -e "GRANT ALL PRIVILEGES ON radius.* TO 'radius'@'localhost'";
mysql -u root radius < /etc/freeradius/3.0/mods-config/sql/main/mysql/schema.sql 
```

9. Create ln for sql FreeRadius
```
ln -s /etc/freeradius/3.0/mods-available/sql /etc/freeradius/3.0/mods-enabled/ 
```

10. Configure sql FreeRadius
OBS: Change variable <password>
```
nano /etc/freeradius/3.0/mods-available/sql
```
```
sql { 

	dialect = "mysql"

...

	#driver = "rlm_sql_null" 
	driver = "rlm_sql_${dialect}" 

... 

	mysql { 
		# If any of the files below are set, TLS encryption is enabled                

		tls {
			#ca_file = "/etc/ssl/certs/my_ca.crt" 
			#ca_path = "/etc/ssl/certs/" 
			#certificate_file = "/etc/ssl/certs/private/client.crt" 
			#private_key_file = "/etc/ssl/certs/private/client.key" 
			#cipher = "DHE-RSA-AES256-SHA:AES128-SHA" 
			
			#tls_required = no 
			#tls_check_cert = no 
			#tls_check_cert_cn = no 
		} 

... 
		# Connection info: 
		# 
		server = "localhost" 
		port = 3306 
		login = "radius" 
		password = "<password>" 
... 

		# Database table configuration for everything except Oracle 
		radius_db = "radius" 

...

	#  The only issue is if you have multiple listeners in a
	#  virtual server, each with a different client list, then
	#  the SQL clients are added only to the first listener.
	#
	read_clients = yes 
```

11. Change owner sql FreeRadius
```
chown freerad:freerad /etc/freeradius/3.0/mods-enabled/sql 
```

12. Configure security Apache
```
nano /etc/apache2/conf-enabled/security.conf
```
```
ServerTokens Prod  
ServerSignature Off
```

13. Configure security PHP
```
nano /etc/php/8.2/cli/php.ini
```
```
expose_php = Off  
```

14. Remove default HTML Page
```
rm -rf /usr/share/apache2/default-site/* /var/www/html/*
```

15. Disable the default virtual host:
```
a2dissite 000-default.conf
```

16. Stop the Apache service:
```
systemctl stop apache2
```

17. Stop the MariaDB service:
```
systemctl stop mariadb
```

18. Install daloRadius edited flaviojunior1995:
```
apt install unzip
cd /tmp
wget https://github.com/flaviojunior1995/daloradius/releases/download/v2.0.1b_v1/daloradius-v2.0.1beta_edited-v1.zip
unzip [daloradius-v2.0.1beta_edited-v1.zip](https://github.com/flaviojunior1995/daloradius/releases/download/v2.0.1b_v1/daloradius-v2.0.1beta_edited-v1.zip)
cp -R daloradius/* /var/www/html/
```

19. Configure Apache ports:
```
cat <<EOF > /etc/apache2/ports.conf
Listen 80
Listen 8000

<IfModule ssl_module>
    Listen 443
</IfModule>

<IfModule mod_gnutls.c>
    Listen 443
</IfModule>
EOF
```

20. Configure 2 virtual hosts (operators and users):
```
cat <<EOF > /etc/apache2/sites-available/operators.conf
<VirtualHost *:8000>
	ServerAdmin operators@localhost
	ServerName your_domain
	ServerAlias www.your_domain
	DocumentRoot /var/www/daloradius/app/operators

	<Directory /var/www/daloradius/app/operators>
		Options -Indexes +FollowSymLinks
		AllowOverride None
		Require all granted
	</Directory>

	<Directory /var/www/daloradius>
		Require all denied
	</Directory>

	ErrorLog /var/log/apache2/daloradius/operators/error.log
	CustomLog /var/log/apache2/daloradius/operators/access.log combined
</VirtualHost>
EOF

cat <<EOF > /etc/apache2/sites-available/users.conf
<VirtualHost *:80>
	ServerAdmin users@localhost
	ServerName your_domain
	ServerAlias www.your_domain
	DocumentRoot /var/www/daloradius/app/users

	<Directory /var/www/daloradius/app/users>
		Options -Indexes +FollowSymLinks
		AllowOverride None
		Require all granted
	</Directory>

	<Directory /var/www/daloradius>
		Require all denied
	</Directory>

	ErrorLog /var/log/apache2/daloradius/users/error.log
	CustomLog /var/log/apache2/daloradius/users/access.log combined
</VirtualHost>
EOF
```

21. Create log directories:
```
mkdir -p /var/log/apache2/daloradius/{operators,users}
```

22. Enable the created virtual hosts:
```
a2ensite users.conf operators.conf
```

23. Enable and restart MariaDB service:
```
systemctl enable mariadb
systemctl restart mariadb
```

24. Import the required SQL files. In this example it is supposed you are using FreeRADIUS 3.
```
mysql -u root radius < /var/www/daloradius/contrib/db/fr3-mysql-freeradius.sql
mysql -u root radius < /var/www/daloradius/contrib/db/mysql-daloradius.sql
```

25. Clone the sample configuration file
```
cd /var/www/daloradius/app/common/includes/
cp daloradius.conf.php.sample daloradius.conf.php
chown www-data:www-data daloradius.conf.php
```

26. Create `var` directory and its subdirectories, then change their ownership:
```
cd /var/www/daloradius/
mkdir -p var/{log,backup}
chown -R www-data:www-data var
```

22. Edit the configuration file to reflect FreeRADIUS and db configuration. In this example:
OBS: Change variable <password>
```
nano /var/www/daloradius/app/common/includes/daloradius.conf.php
```
```
$configValues['FREERADIUS_VERSION'] = '3';
$configValues['CONFIG_DB_ENGINE'] = 'mysqli';
$configValues['CONFIG_DB_HOST'] = 'localhost';
$configValues['CONFIG_DB_PORT'] = '3306';
$configValues['CONFIG_DB_USER'] = 'radius';
$configValues['CONFIG_DB_PASS'] = '<password>';
$configValues['CONFIG_DB_NAME'] = 'radius';
```

23. Enable and start Apache:
```
systemctl enable apache2
systemctl restart apache2
```

24. Check if the system is working fine just by visiting `http://<ip>:8000/` for the RADIUS management application or `http://<ip>` for the user portal application, default user administrator password radius

25. To Debug use:
```
freeradius -X
```
