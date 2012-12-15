#!/bin/sh

ROOTPATH=`pwd`;
BUILDPATH=`pwd`/test/build;

mkdir -p BUILDPATH;
cd BUILDPATH;



####
# php5.3
####
wget http://de1.php.net/distributions/php-5.4.9.tar.bz2;
tar xf php-5.4.9.tar.bz2;
cd php-5.4.9;
./configure --prefix=$BUILDPATH/php54 --with-gd --enable-mbstring --with-pdo-mysql;
make && make install;
cd $BUILDPATH;



####
# apr
####
wget http://mirror3.layerjet.com/apache//apr/apr-1.4.6.tar.gz;
tar xf apr-1.4.6.tar.gz;
cd apr-1.4.6;
./configure --prefix=$BUILDPATH/apr
make && make install;
cd $BUILDPATH;


####
# apr-util
####
wget http://mirror.lwnetwork.org.uk/APACHE//apr/apr-util-1.5.1.tar.gz;
tar xf apr-util-1.5.1.tar.gz;
cd apr-util-1.5.1;
./configure --with-apr=../apr --prefix=$BUILDPATH/apr-util;
make && make install;

cd $BUILDPATH;

####
# Apache2
####
wget http://mirror3.layerjet.com/apache//httpd/httpd-2.4.3.tar.bz2;

tar xf httpd-2.4.3.tar.bz2;
cd httpd-2.4.3;

./configure --prefix=$BUILDPATH/apache2 --with-apr=$BUILDPATH/apr --with-apr-util=$BUILDPATH/apr-util;
make && make install;
cd $BUILDPATH;

# setup config
cat ../config/default.apache2.conf >> apache2/conf/httpd.conf

# change port, path, load module
sed -io 's/Listen\ 80$/Listen\ 8000/g' apache2/conf/httpd.conf;
sed -io "s,ROOTPATH,$ROOTPATH,g" apache2/conf/httpd.conf;
#activaet modrewrite
sed -io "s,^#LoadModule rewrite_module,LoadModule rewrite_module,g" apache2/conf/httpd.conf;

####
# mod_fcgi
####

wget http://apache.mirror.digionline.de//httpd/mod_fcgid/mod_fcgid-2.3.7.tar.bz2;
tar xf mod_fcgid-2.3.7.tar.bz2;

cd mod_fcgid-2.3.7;

APXS=$BUILDPATH/apache2/bin/apxs ./configure.apxs;

make & make install;

#out to build;
cd $BUILDPATH;

#rm -r httpd-2.4.3;
#rm -r mod_fcgid-2.3.7;

#out to ROOTPATH
cd $ROOTPATH;

#setup php-fcgi script

# create fgi script
cat > php-fcgi << EOF
#!/bin/sh

test/build/php54/bin/php-cgi
EOF

chmod +x php-fcgi;

echo "Buil done. Should work.";