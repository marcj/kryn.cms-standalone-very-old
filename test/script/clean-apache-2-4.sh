#!/bin/sh

./test/build/apache2/bin/apachectl -k stop;

rm -r test/build/;
rm php-fcgi;
