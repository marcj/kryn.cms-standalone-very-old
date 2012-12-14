#!/bin/sh

PHP=`which php-cgi`;

# install stuff
sudo apt-get install apache2 libapache2-mod-fcgid;
sudo a2enmod actions;
sudo a2enmod rewrite;
sudo a2enmod fcgid;
sudo a2enmod suexec;

#setup vhost+fcgi config
cat test/config/default.apache2-vhost.conf | sed -e "s,ROOTPATH,`pwd`,g" | sudo tee /etc/apache2/sites-available/default > /dev/null;

# create fgi script
sudo cat > php-fcgi << EOF
#!/bin/sh

exec $PHP
EOF

chmod +x php-fcgi;


# restart apache
sudo service apache2 restart;