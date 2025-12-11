#!/bin/bash
# Install WordPress on Ubuntu 22.04+ (Generic Base)
# Usage: ./provision_vps.sh <mysql_root_pass> <wp_db_pass>

MYSQL_ROOT_PASS=$1
WP_DB_PASS=$2

if [ -z "$MYSQL_ROOT_PASS" ] || [ -z "$WP_DB_PASS" ]; then
    echo "Usage: $0 <mysql_root_pass> <wp_db_pass>"
    exit 1
fi

export DEBIAN_FRONTEND=noninteractive

echo ">>> Updating System..."
apt-get update && apt-get upgrade -y

echo ">>> Installing Dependencies (Apache, PHP 8.3, MySQL)..."
apt-get install -y software-properties-common
add-apt-repository -y ppa:ondrej/php
apt-get update
apt-get install -y apache2 mysql-server \
    php8.3 php8.3-cli php8.3-common php8.3-mysql \
    php8.3-xml php8.3-xmlrpc php8.3-curl php8.3-gd \
    php8.3-imagick php8.3-mbstring php8.3-zip \
    php8.3-intl php8.3-bz2 php8.3-bcmath php8.3-soap \
    libapache2-mod-php8.3 unzip

echo ">>> Configuring MySQL..."
mysql <<EOF
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '${MYSQL_ROOT_PASS}';
CREATE DATABASE IF NOT EXISTS wordpress;
CREATE USER IF NOT EXISTS 'wordpress'@'localhost' IDENTIFIED BY '${WP_DB_PASS}';
GRANT ALL PRIVILEGES ON wordpress.* TO 'wordpress'@'localhost';
FLUSH PRIVILEGES;
EOF

echo ">>> Downloading WordPress..."
rm -rf /var/www/html/*
cd /tmp
wget https://wordpress.org/latest.tar.gz
tar xzvf latest.tar.gz
cp -R wordpress/* /var/www/html/
rm -f /var/www/html/index.html

echo ">>> Configuring wp-config.php..."
cp /var/www/html/wp-config-sample.php /var/www/html/wp-config.php
sed -i "s/database_name_here/wordpress/" /var/www/html/wp-config.php
sed -i "s/username_here/wordpress/" /var/www/html/wp-config.php
sed -i "s/password_here/${WP_DB_PASS}/" /var/www/html/wp-config.php

# Set salts
curl -s https://api.wordpress.org/secret-key/1.1/salt/ >> /tmp/salts
sed -i "/AUTH_KEY/d; /SECURE_AUTH_KEY/d; /LOGGED_IN_KEY/d; /NONCE_KEY/d" /var/www/html/wp-config.php
sed -i "/AUTH_SALT/d; /SECURE_AUTH_SALT/d; /LOGGED_IN_SALT/d; /NONCE_SALT/d" /var/www/html/wp-config.php
sed -i "/define( 'DB_COLLATE'/r /tmp/salts" /var/www/html/wp-config.php

echo ">>> Setting Permissions..."
chown -R www-data:www-data /var/www/html
find /var/www/html -type d -exec chmod 755 {} \;
find /var/www/html -type f -exec chmod 644 {} \;

echo ">>> Configuring Apache..."
a2enmod rewrite
cat > /etc/apache2/sites-available/wordpress.conf <<'APACHECONF'
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html
    
    <Directory /var/www/html>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
APACHECONF

a2dissite 000-default.conf
a2ensite wordpress.conf

echo ">>> Configuring .htaccess..."
cat > /var/www/html/.htaccess <<'HTACCESS'
# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress
HTACCESS
chown www-data:www-data /var/www/html/.htaccess
chmod 644 /var/www/html/.htaccess

echo ">>> Installing WP-CLI..."
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
chmod +x wp-cli.phar
mv wp-cli.phar /usr/local/bin/wp

echo ">>> Restarting Apache..."
systemctl restart apache2

echo ">>> Provisioning Complete!"
