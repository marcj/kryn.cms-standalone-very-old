#!/bin/sh

ROOTPATH=`pwd`;
BUILDPATH=`pwd`/build;
PHPCGI=`which php5-cgi`;
if [ "$PHPCGI" == "" ]; then PHPCGI=`which php-cgi`; fi;
if [ "$PHPCGI" == "" ]; then PHPCGI=`which php-cgi53`; fi;
if [ "$PHPCGI" == "" ]; then PHPCGI=`which php-cgi54`; fi;

mkdir build;
cd build;

####
# apr
####
wget http://mirror3.layerjet.com/apache//apr/apr-1.4.6.tar.gz;
tar xf apr-1.4.6.tar.gz;
cd apr-1.4.6;
./configure --prefix=$ROOTPATH/build/apr
make && make install;
cd $BUILDPATH;


####
# apr-util
####
wget http://mirror.lwnetwork.org.uk/APACHE//apr/apr-util-1.5.1.tar.gz;
tar xf apr-util-1.5.1.tar.gz;
cd apr-util-1.5.1;
./configure --with-apr=../apr --prefix=$ROOTPATH/build/apr-util;
make && make install;

cd $BUILDPATH;

####
# Apache2
####
wget http://mirror3.layerjet.com/apache//httpd/httpd-2.4.3.tar.bz2;

tar xf httpd-2.4.3.tar.bz2;
cd httpd-2.4.3;

./configure --prefix=$ROOTPATH/build/apache2 --with-apr=$ROOTPATH/build/apr --with-apr-util=$ROOTPATH/build/apr-util;
make && make install;
cd $BUILDPATH;

# setup config
cat ../test/config/default.apache2.conf >> apache2/conf/httpd.conf

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

APXS=$ROOTPATH/build/apache2/bin/apxs ./configure.apxs;

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

$PHPCGI
EOF

chmod +x php-fcgi;

echo "Buil done. Should work.";