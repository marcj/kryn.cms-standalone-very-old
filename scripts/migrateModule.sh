#!/bin/bash

FILES=`find media/$1 -mindepth 1 -maxdepth 1 | grep -v 'images$' | grep -v '.css$' | grep -v '.js$' | grep -v './css$' | grep -v './js$' | grep -v '.jpg$' | grep -v '.png$'`;

FOLDERS=`find media/$1 -iname '*.tpl' -exec dirname {} \;`;
FILES=`find media/$1 -iname '*.tpl'`;

FOLDERS=`echo ${FOLDERS//media\/$1/module\/$1\/views}`;
echo $FOLDERS;
FILES=`echo ${FILES//media\/$1\//}`;
echo $FILES;

mkdir -p $FOLDERS;

cd media/$1;
mv $FILES ../../module/$1/views/;
cd -;
