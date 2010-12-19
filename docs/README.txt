README
======

This directory should be used to place project specfic documentation including
but not limited to project notes, generated API/phpdoc documentation, or 
manual files generated or hand written.  Ideally, this directory would remain
in your development environment only and should not be deployed with your
application to it's final production location.


Setting Up VHOST
================

The following is a sample VHOST to customize.

<VirtualHost *:80>
    ServerAdmin {admin.email}
    ServerName {host.name}

    DocumentRoot {project-path}/public
    
   # This should be omitted in the production environment
   SetEnv APPLICATION_ENV development
    
    <Directory {project-path}/public>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Order allow,deny
        allow from all
    </Directory>
    
    # This should be omitted in the production environment
    Alias /docs/ "{project-path}/docs/"
    Alias /tests/ "{project-path}/tests/log/"

    ErrorLog /var/log/apache2/{host.name}-error.log

    # Possible values include: debug, info, notice, warn, error, crit,
    # alert, emerg.
    LogLevel warn

    CustomLog /var/log/apache2/{host.name}-access.log combined
</VirtualHost>

Setting Up PHP
==============
Install PHP >=5.3.2

in /etc/php5/cli/php.ini and in /etc/php5/php.ini enable include_path
include_path = ".:/usr/share/php"

Setting Up Zend Framework Library
=================================

get ZF and unzip it in your file system (ZF >= 1.11.0)
 http://framework.zend.com/download/latest

add a symlink in the library directory of project to ZF library
ln -s {zf-path}/library/Zend ./library/Zend 
or a symlink in php share include path
ln -s {zf_path}/library/Zend /usr/share/php/Zend

in development environement add the zf cli command
ln -s {zf-path}/bin/zf.sh /usr/bin/zf
or in your .bashrc
allias zf {zf-path}/bin/zf.sh

Setting Up PhpDocumentor
========================

pear install PhpDocumentor

to generate api doc
phpdoc -d {project-path}/application -o HTML:frames -t {project-path}/docs/api -ti {project.name}
or use phpdoc -c myconfig.ini (this file has to be defined)

Setting Up PHPUnit
==================

pear channel-discover pear.phpunit.de
pear channel-discover components.ez.no
pear channel-discover pear.symfony-project.com
pear install --allDeps phpunit/PHPUnit

to run PHPUnit
phpunit --configuration {project-path}/tests/phpunit.xml

to have code coverage report xdebug have to be installed
