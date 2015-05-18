#!/bin/bash

rm -Rf "`pwd`/protected/runtime"/*
rm -Rf "`pwd`/protected/upload"/*
rm -Rf "`pwd`/assets"/*
rm -Rf "`pwd`/vendor"/*
rm -Rf "`pwd`/bower_components"/*
chown $USER:www-data -R "`pwd`"
chmod 775 -R "`pwd`/protected/runtime"
chmod 775 -R "`pwd`/protected/upload"
chmod 775 -R "`pwd`/assets"
php composer.phar install
bower install
