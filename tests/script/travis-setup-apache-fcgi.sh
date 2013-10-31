#!/bin/sh

PHP=`which php-cgi`;

# install stuff
sudo apt-get install apache2 libapache2-mod-fcgid;
sudo a2enmod actions;
sudo a2enmod rewrite;
sudo a2enmod fcgid;
sudo a2enmod suexec;

#setup vhost+fcgi config
sudo mv ../config/default.apache2.conf /etc/apache2/sites-available/default

sudo sed -io 's/Listen\ 80$/Listen\ 8000/g' /etc/apache2/sites-available/default;
sudo sed -io "s,ROOTPATH,`pwd`,g" /etc/apache2/sites-available/default;

echo $PHP;
cat $PHP;

# create fgi script
sudo cat > php-fcgi << EOF
#!/bin/sh

$PHP
EOF

chmod +x php-fcgi;

# restart apache
sudo service apache2 restart;