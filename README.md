Kryn.cms
========

A enterprise open-source Content-Management-System and Content-Management-Framework with a full RESTful API
written in PHP and JavaScript using Symfony Components, Propel, Mootools and other rock-solid libraries.

We're in development. This means, there are still a _lot_ of issues in this product (not even an Alpha) and it's not everything implemented yet.

[![Build Status Drone.io](https://drone.io/marcj/Kryn.cms/status.png)](https://drone.io/marcj/Kryn.cms/latest)
[![Build Status Travis](https://travis-ci.org/KrynLabs/Kryn.cms.png?branch=refactoring)](https://travis-ci.org/KrynLabs/Kryn.cms)


Installation
------------

1. Extract the tar/zip-ball or `git clone https://github.com/KrynLabs/Kryn.cms.git`.
2. Download `composer` through `wget http://getcomposer.org/composer.phar`.
3. Install vendor libraries with `php composer.phar install`.
4. Setup a `VirtualHost` pointing to the `./web/` directory.
5. Open `domain.tld/install.php` in your browser and follow the instruction.

Requirements
------------

1. PHP 5.4+
2. *nix OS (Linux, BSD, OSX)
3. PHP extensions: PDO, mbstring, gd, zip


Features
--------

 - Based on Propel ORM (Propel supports MySQL, PostgreSQL, SQLite, MSSQL, and Oracle), http://www.propelorm.org
 - Advanced, fast and fresh administration interface
 - The administration API is completely abstracted through a RESTful JSON API
 - File abstraction layer (for mounts with external storages [s3, ftp, dropbox, etc]), CDN
 - Dynamic template engines, use the engine you want. (Smarty, Twig and plain PHP are shipped)
 - i18n using `getText` (with all of its features [e.g. including plural support, contexts]) compatible .po files
 - High-Performance through several cache layers 
 - Session storage through several layers (distributed sessions supported)
 - Easy to extend through a fancy extension editor, completely modulized
 - CRUD window generator, without writing one line of code
 - Easy and integrated backup system, perfect for live/dev-scenarios
 - Working in workspaces
 - Extremely detailed permission system
 - Ships with a solid bunch of UI input widgets
 - Several flexible authentication layers (e.g. changeable for administration, changeable per domain)
 - MVC architecture
 - Secure password storage using up-to-date encryptions
 - Symfony2-like Bundles to extend the system
 - Symfony2 bundles compatible
 - Symfony2 Components: Routing Component, HttpKernel Component, Observer, Dependency Injection
 - Monolog
 - CKEditor, inline editing and fancy WYSIWYG

Screenshot
----------

![Kryn.cms](https://raw.github.com/KrynLabs/Kryn.cms/refactoring/documentation/images/kryn-photo.jpg)
![Administration Dashboard](https://raw.github.com/KrynLabs/Kryn.cms/refactoring/documentation/images/admin-dashboard.png)
![Administration Frontend Edit](https://raw.github.com/KrynLabs/Kryn.cms/refactoring/documentation/images/admin-frontend-edit.png)
![Administration File manager](https://raw.github.com/KrynLabs/Kryn.cms/refactoring/documentation/images/admin-files-context-image.png)
![Administration CRUD Framework Window List](https://raw.github.com/KrynLabs/Kryn.cms/refactoring/documentation/images/admin-users-list.png)
![Administration CRUD Framework Window](https://raw.github.com/KrynLabs/Kryn.cms/refactoring/documentation/images/admin-users.png)

[More Screenshots](https://github.com/KrynLabs/Kryn.cms/blob/refactoring/documentation/screenshots.markdown)

More information:
https://www.facebook.com/kryncms

PHPUnit
--------

 You have several environment variables to adjust the config in the test suite.

    HOST       The domain the installation (should) have. Default is `127.0.0.1:8000`.
    DB_NAME    The database name. Default is `test`
    DB_USER    The database username. Default is `root`
    DB_PW      The database password. Default is empty.
    DB_TYPE    The database type. `mysql`, `pgsql`, `sqlite`, `sqlsrv`. Default is `mysql`.
    DB_SERVER  The database server address. Default is `127.0.0.1`
    NOINSTALL  Defines whether the bootstrap removes the config file or not. `1` or `0`. Default is `0`
    TEMP       Defines a other temp folder. E.g. `app/cache` or `/tmp/`. Default is 'app/cache'.

Examples:

    ./phpunit.phar
    HOST=dev.local ./phpunit.phar
    DB_USER=kryn DB_PW='@#$TKKAFS' ./phpunit.phar test/Tests/Object/ApiTest.php
    NOINSTALL=1 TEMP=/tmp/ php54 vendor/phpunit/phpunit/phpunit.php --stop-on-failure tests/Tests/Core/ConfigTest.php

The test suite installs automatically Kryn.cms with the configuration above if `./app/config/config.xml` does not exist
and if NOINSTALL=0. Don't forget to run `composer install` first.
